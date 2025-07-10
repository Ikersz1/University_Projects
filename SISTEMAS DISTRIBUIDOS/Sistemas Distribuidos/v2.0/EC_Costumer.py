import json
import time
import threading
import argparse
from kafka import KafkaProducer, KafkaConsumer
from kafka.errors import NoBrokersAvailable

class Customer:
    def __init__(self, kafka_bootstrap_servers, customer_id, requests_file, initial_position):
        self.kafka_bootstrap_servers = kafka_bootstrap_servers
        self.customer_id = customer_id
        self.requests_file = requests_file
        self.requests = self.load_requests()
        self.current_request_index = 0
        self.position = initial_position
        self.response_received = False
        self.retry_timer = None
        self.current_destination = None
        self.position = self.get_initial_position(initial_position)

        # Inicializa conexiones Kafka
        self.create_kafka_connections()
        
    def get_initial_position(self, initial_position):
        """
        Obtiene la posición inicial del cliente desde el archivo de mapa 
        si el initial_position es una letra.
        """
        # Si ya es una lista de coordenadas, devolverla directamente
        if isinstance(initial_position, list) and len(initial_position) == 2:
            return initial_position
        
        # Si es una letra, buscar en el archivo mapa.json
        try:
            with open('mapa.json', 'r') as file:
                map_data = json.load(file)
                
            locations = map_data.get('locations', {})
            if initial_position in locations:
                return locations[initial_position]
            
            # Si no se encuentra la letra, usar [0, 0]
            print(f"Warning: No initial position found for location {initial_position}. Using [0, 0].")
            return [0, 0]
        
        except FileNotFoundError:
            print("mapa.json file not found. Using [0, 0] as initial position.")
            return [0, 0]
        except json.JSONDecodeError:
            print("Error decoding mapa.json. Using [0, 0] as initial position.")
            return [0, 0]
        
    def create_kafka_connections(self):
        try:
            self.kafka_producer = KafkaProducer(
                bootstrap_servers=self.kafka_bootstrap_servers,
                value_serializer=lambda v: json.dumps(v).encode('utf-8')
            )
            self.kafka_consumer = KafkaConsumer(
                'customer_responses',
                bootstrap_servers=self.kafka_bootstrap_servers,
                value_deserializer=lambda v: json.loads(v.decode('utf-8'))
            )
        except NoBrokersAvailable:
            print("Failed to connect to CENTRAL. Retrying...")
            self.wait_for_central()

    def load_requests(self):
        with open(self.requests_file, 'r') as file:
            data = json.load(file)
        return data.get('Requests', [])

    def send_request(self, destination):
        self.current_destination = destination  # Almacena el destino actual
        request = {
            'type': 'pedir_taxi',
            'customer_id': self.customer_id,
            'pickup': self.position,
            'destination': destination,
            'timestamp': time.time()
        }
        self.kafka_producer.send('taxi_requests', request)
        print(f"Sent request for pickup at {self.position} and destination {destination}")
        
        # Inicia temporizador para el tiempo de espera de la solicitud
        self.response_received = False
        if self.retry_timer:
            self.retry_timer.cancel()
        self.retry_timer = threading.Timer(4, self.retry_request, args=[destination])
        self.retry_timer.start()

    def retry_request(self, destination):
        if not self.response_received:
            self.send_request(destination)

    def wait_for_central(self):
        """Intenta reconectar con CENTRAL periódicamente hasta que sea exitoso."""
        while True:
            try:
                self.create_kafka_connections()
                if self.current_destination:
                    print("Reenviando la última solicitud...")
                    self.send_request(self.current_destination)  # Reenvía la última solicitud
                break
            except NoBrokersAvailable:
                print("CENTRAL not available. Retrying in 5 seconds...")
                time.sleep(5)

    def process_response(self):
        for message in self.kafka_consumer:
            response = message.value
            
            if response.get('customer_id') == self.customer_id:
                self.response_received = True
                if self.retry_timer:
                    self.retry_timer.cancel()
                
                if response.get('type') == 'customer_assignment':
                    print(f"Request accepted: Taxi {response['taxi_id']} assigned for pickup at {self.position} and destination {response['destination']}")
                elif response.get('type') == 'no_taxi_available':
                    print(f"Request denied: No taxis available")
                    print("Waiting 4 seconds before retrying...")
                    time.sleep(4)
                    self.send_request(self.current_destination)
                elif response.get('type') == 'destination_reached':
                    print(f"Arrived at destination with taxi {response['taxi_id']}")
                    if 'destination' in response:
                        self.position = response['destination']
                        print(f"Updated customer position to: {self.position}")
                    
                    time.sleep(2)
                    self.send_next_request()
            elif response.get('type') == 'central_shutdown':
                print("Central shutdown detected. Waiting to reconnect...")
                self.wait_for_central()

    def send_next_request(self):
        if self.current_request_index < len(self.requests):
            next_request = self.requests[self.current_request_index]
            destination = next_request['Id']
            self.send_request(destination)
            self.current_request_index += 1
        else:
            print("All requests have been processed.")
            exit(0)

    def run(self):
        print(f"Customer {self.customer_id} starting at position {self.position}...")
        self.send_next_request()
        self.process_response()

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="EC_Customer: EasyCab Customer Application")
    parser.add_argument("kafka_bootstrap_servers", help="Kafka bootstrap servers")
    parser.add_argument("customer_id", help="Unique ID for this customer")
    parser.add_argument("requests_file", help="JSON file containing service requests")
    parser.add_argument("initial_position", nargs=2, type=int, help="Initial position as 'x y'")
    args = parser.parse_args()

    initial_position = [args.initial_position[0], args.initial_position[1]]
    customer = Customer(args.kafka_bootstrap_servers, args.customer_id, args.requests_file, initial_position)
    customer.run()

