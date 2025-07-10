import json
import time
import threading
import argparse
from kafka import KafkaProducer, KafkaConsumer

class Customer:
    def __init__(self, kafka_bootstrap_servers, customer_id, requests_file, initial_position):
        self.kafka_producer = KafkaProducer(
            bootstrap_servers=kafka_bootstrap_servers,
            value_serializer=lambda v: json.dumps(v).encode('utf-8')
        )
        self.kafka_consumer = KafkaConsumer(
            'customer_responses',
            bootstrap_servers=kafka_bootstrap_servers,
            value_deserializer=lambda v: json.loads(v.decode('utf-8'))
        )
        self.customer_id = customer_id
        self.requests_file = requests_file
        self.requests = self.load_requests()
        self.current_request_index = 0
        self.position = initial_position
        self.last_destination = None
        self.response_received = False
        self.retry_timer = None
        self.current_destination = None  # Added to track current destination for retries

    def load_requests(self):
        with open(self.requests_file, 'r') as file:
            data = json.load(file)
        return data.get('Requests', [])

    def send_request(self, destination):
        self.current_destination = destination  # Store current destination
        request = {
            'type': 'pedir_taxi',
            'customer_id': self.customer_id,
            'pickup': self.position,
            'destination': destination,
            'timestamp': time.time()
        }
        print(f"DEBUG: Sending request: {request}")
        self.kafka_producer.send('taxi_requests', request)
        print(f"Sent request for pickup at {self.position} and destination {destination}")
        
        # Start timer for request timeout
        self.response_received = False
        if self.retry_timer:
            self.retry_timer.cancel()
        self.retry_timer = threading.Timer(4, self.retry_request, args=[destination])
        self.retry_timer.start()

    def retry_request(self, destination):
        if not self.response_received:
            print("DEBUG: No response in 4 seconds, retrying request...")
            self.send_request(destination)

    def process_response(self):
        print("DEBUG: Waiting for response...")
        for message in self.kafka_consumer:
            response = message.value
            print(f"DEBUG: Received response: {response}")
            
            if response.get('customer_id') == self.customer_id:
                self.response_received = True
                if self.retry_timer:
                    self.retry_timer.cancel()
                
                if response.get('type') == 'customer_assignment':
                    print(f"Request accepted: Taxi {response['taxi_id']} assigned for pickup at {self.position} and destination {response['destination']}")
                elif response.get('type') == 'no_taxi_available':
                    print(f"Request denied: No taxis available")
                    # Wait 4 seconds and retry the same request
                    print("Waiting 4 seconds before retrying...")
                    time.sleep(4)
                    print("Retrying request...")
                    self.send_request(self.current_destination)
                elif response.get('type') == 'destination_reached':
                    print(f"Arrived at destination with taxi {response['taxi_id']}")
                    if 'destination' in response:
                        self.position = response['destination']
                        print(f"Updated customer position to: {self.position}")
                    
                    time.sleep(4)  # Wait 4 seconds before sending next request
                    self.send_next_request()

    def send_next_request(self):
        if self.current_request_index < len(self.requests):
            next_request = self.requests[self.current_request_index]
            destination = next_request['Id']
            self.last_destination = destination
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