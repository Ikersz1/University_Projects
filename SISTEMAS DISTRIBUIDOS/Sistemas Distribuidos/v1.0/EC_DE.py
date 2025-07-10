import socket
import threading
import json
from kafka import KafkaProducer, KafkaConsumer
import argparse
import time
import pygame
import traceback
import sqlite3

class DigitalEngine:
    def __init__(self, central_ip, central_port, kafka_bootstrap_servers, sensor_port, taxi_id):
        self.central_ip = central_ip
        self.central_port = central_port
        print(f"Initializing Digital Engine for taxi {taxi_id}...")
        
        self.db_connection = sqlite3.connect('central.db')  # Cambia esto si la base de datos está en otro lugar
        
        # Configurar Kafka producer
        self.kafka_producer = KafkaProducer(
            bootstrap_servers=kafka_bootstrap_servers,
            value_serializer=lambda v: json.dumps(v).encode('utf-8')
        )
        
        # Configurar Kafka consumer para instrucciones
        self.kafka_consumer = KafkaConsumer(
            'taxi_instructions',
            bootstrap_servers=kafka_bootstrap_servers,
            value_deserializer=lambda v: json.loads(v.decode('utf-8')),
            group_id=f'taxi_group_{taxi_id}',
            auto_offset_reset='earliest',
            enable_auto_commit=True
        )
        
        # Configurar Kafka consumer para actualizaciones del mapa
        self.map_consumer = KafkaConsumer(
            'map_updates',
            bootstrap_servers=kafka_bootstrap_servers,
            value_deserializer=lambda v: json.loads(v.decode('utf-8')),
            group_id=f'taxi_map_group_{taxi_id}',
            auto_offset_reset='earliest',
            enable_auto_commit=True,
            consumer_timeout_ms=1000  # Timeout para poll()
        )
        
        self.sensor_port = sensor_port
        self.taxi_id = taxi_id
        self.position = [1, 1]
        self.destination = None
        self.status = "KO"
        self.sensor_socket = None
        self.running = True
        self.lock = threading.Lock()
        self.map_size = 20
        self.map = None
        self.taxis = {}
        self.customers = {}
        self.taxi_passengers = {}
        self.last_map_update_time = 0
        self.screen = None
        self.clock = pygame.time.Clock()
        self.sensor_connected = False
        self.next_destination = None
        self.map_initialized = False
        self.phase = 'idle'
        
    def connect_to_central(self):
        try:
            # Verificar si el taxi está en la base de datos
            if self.is_taxi_registered():
                print(f"Taxi ID {self.taxi_id} is not registered. Denying connection.")
                exit(1)  # Denegar conexión
            else:
                print("Verificado Correctamente")

            # Enviar mensaje de registro al topic de registro de taxis
            registration_message = {
                'type': 'taxi_registration',
                'taxi_id': self.taxi_id,
                'status': self.status,
                'position': self.position
            }
            print(f"Sending registration message: {registration_message}")
            self.kafka_producer.send('taxi_registration', registration_message)
            self.kafka_producer.flush()
            
            # Esperar la primera actualización del mapa
            self.wait_for_initial_map()
            
            print(f"Connected to Central via Kafka")
            print(f"Subscribed to topics: {self.kafka_consumer.subscription()}")
        except Exception as e:
            print(f"Error connecting to Central: {e}")
            print(traceback.format_exc())
            exit(1)

    def is_taxi_registered(self):
        cursor = self.db_connection.cursor()
        cursor.execute("SELECT * FROM taxis WHERE id = ?", (self.taxi_id,))
        result = cursor.fetchone()
        return result is None

    def listen_for_sensors(self):
        server_socket = None
        try:
            server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            server_socket.bind(('0.0.0.0', self.sensor_port))
            server_socket.settimeout(1.0)  # Timeout de 1 segundo
            server_socket.listen(1)
            print(f"Listening for sensor connections on port {self.sensor_port}")
            
            while self.running:
                try:
                    # Acepta la conexión del sensor
                    self.sensor_socket, addr = server_socket.accept()
                    print(f"Sensor connected from {addr}")
                    self.sensor_connected = True
                    
                    # Actualiza estado a OK si estaba en KO
                    with self.lock:
                        if self.status == "KO":
                            self.status = "OK"
                            self.send_status_update()
                    
                    # Maneja la recepción de datos del sensor
                    self.handle_sensor_data()
                    
                except socket.timeout:
                    continue  # Intentar aceptar una conexión nuevamente
                except Exception as e:
                    print(f"Error accepting sensor connection: {e}")
                    time.sleep(1)
            
        except Exception as e:
            print(f"Error in sensor connection: {e}")
        finally:
            if server_socket:
                server_socket.close()
            
    def handle_sensor_data(self):
        while self.running and self.sensor_socket:
            try:
                data = self.sensor_socket.recv(1024).decode('utf-8')
                if not data:
                    # Si no hay datos, el sensor se desconectó
                    break
                    
                # Procesa el mensaje de estado del sensor
                status = data.split('<STX>')[1].split('<ETX>')[0]
                print(f"Received sensor status: {status}")
                
                with self.lock:
                    if status == 'KO':
                        self.status = 'KO'
                    elif status == 'OK':
                        # Solo cambiar a RUN si el DE no está en estado STOP
                        if self.status == 'END':
                            self.status = 'END' 
                        elif self.status != 'STOP':
                            self.status = 'RUN' if self.destination else 'OK'                    
                    self.send_status_update()
                    
            except Exception as e:
                print(f"Error receiving sensor data: {e}")
                break
        
        # Desconexión detectada
        self.sensor_connected = False
        with self.lock:
            self.status = "KO"
            self.send_status_update()

    def send_status_update(self):
        try:
            status_message = {
                'type': 'estado_taxi',
                'taxi_id': self.taxi_id,
                'status': self.status,
                'position': self.position
            }
            print(f"Sending status update: {status_message}")
            self.kafka_producer.send('taxi_status', status_message)
            self.kafka_producer.flush()
        except Exception as e:
            print(f"Error sending status update: {e}")
            
    def move_towards_destination(self):
        while self.running:
            with self.lock:
                # Solo mover si hay un destino y el estado es 'RUN'
                if self.destination and self.status == 'RUN' and self.sensor_connected:
                    current_pos = self.position.copy()

                    # Mover hacia el destino
                    if current_pos[0] < self.destination[0]:
                        current_pos[0] += 1
                    elif current_pos[0] > self.destination[0]:
                        current_pos[0] -= 1

                    if current_pos[1] < self.destination[1]:
                        current_pos[1] += 1
                    elif current_pos[1] > self.destination[1]:
                        current_pos[1] -= 1

                    # Actualizar posición si se movió
                    if current_pos != self.position:
                        self.position = current_pos
                        self.send_status_update()
                        print(f"Moved to position: {self.position}")

                    # Verificar nuevamente si llegó al destino
                    if self.position == self.destination:
                        print(f"Reached destination: {self.destination} polla")
                        if self.next_destination:
                            self.destination = self.next_destination
                            self.next_destination = None
                            print(f"Moving to next destination: {self.destination}")
                        else:
                            print("1")
                            self.destination = None
                            self.status = 'END'
                            self.send_status_update()

            time.sleep(1)  # Controlar la velocidad del movimiento


    def process_instructions(self):
        print("Starting to process instructions...")
        try:
            for message in self.kafka_consumer:
                instruction = message.value
                
                if instruction.get('taxi_id') == self.taxi_id:
                    print(f"Processing instruction for taxi {self.taxi_id}: {instruction}")
                    
                    with self.lock:
                        if instruction.get('type') == 'move_instruction':
                            if self.status != 'KO':
                                self.destination = instruction['destination']
                                self.status = 'RUN'
                                if 'next_destination' in instruction:
                                    self.next_destination = instruction['next_destination']
                                self.send_status_update()
                                print(f"Moving to destination: {self.destination}")
                        
                        elif instruction.get('command'):
                            self.handle_command(instruction['command'], instruction.get('payload'))
                            
        except Exception as e:
            print(f"Error processing instructions: {e}")
            print(traceback.format_exc())
    
    def handle_command(self, command, payload=None):
        if command == 'stop_taxi':
            self.status = 'STOP'
            print(f"Taxi {self.taxi_id} stopped")
            
        elif command == 'resume_taxi':
            if self.destination:
                self.status = 'RUN'
            else:
                self.status = 'OK'
            print(f"Taxi {self.taxi_id} resumed")
            
        elif command == 'change_destination' and payload:
            self.destination = payload['new_destination']
            self.status = 'RUN'
            print(f"New destination set: {self.destination}")
            
        elif command == 'return_to_base':
            self.destination = [1, 1]
            self.status = 'OK'
            print("Returning to base")
            
        self.send_status_update()
        
        
    def wait_for_initial_map(self):
        print("Waiting for initial map update...")
        max_retries = 10
        retries = 0
        
        while retries < max_retries and not self.map_initialized:
            try:
                messages = self.map_consumer.poll(timeout_ms=5000)
                for topic_partition, msg_list in messages.items():
                    for message in msg_list:
                        update = message.value
                        if update.get('type') == 'map_update':
                            self.process_map_update(update)
                            if self.map is not None:
                                print("Initial map received successfully")
                                self.map_initialized = True
                                return
                
                retries += 1
                if not self.map_initialized:
                    print(f"Retry {retries}/{max_retries} for initial map")
                    time.sleep(1)
                    
            except Exception as e:
                print(f"Error waiting for initial map: {e}")
                print(traceback.format_exc())
                retries += 1
                
        if not self.map_initialized:
            print("Failed to receive initial map after maximum retries")
            exit(1)
        
    def process_map_update(self, update):
        try:
            if update.get('type') != 'map_update':
                return
                
            new_map = update.get('map')
            if new_map is None:
                print("ERROR: Received map update with None map")
                return
                
            if not isinstance(new_map, list) or not new_map:
                print(f"ERROR: Invalid map format. Map type: {type(new_map)}")
                return
            
            with self.lock:
                self.map = new_map
                self.taxis = update.get('taxis', {})
                self.customers = update.get('customers', {})
                self.taxi_passengers = update.get('taxi_passengers', {})
                self.last_map_update_time = time.time()
                
        except Exception as e:
            print(f"Error processing map update: {e}")
            print(traceback.format_exc())
            
    def process_map_updates(self):
        print("Starting to process map updates...")
        while self.running:
            try:
                messages = self.map_consumer.poll(timeout_ms=1000)
                for topic_partition, msg_list in messages.items():
                    for message in msg_list:
                        self.process_map_update(message.value)
                        
            except Exception as e:
                print(f"Error in process_map_updates: {e}")
                print(traceback.format_exc())
                time.sleep(1)  # Evitar bucle infinito en caso de error	

    def draw_map(self, screen):
        if not self.running or not screen or not self.map:
            return

        # Definir colores
        WHITE = (255, 255, 255)
        BLACK = (0, 0, 0)
        RED = (255, 0, 0)
        GREEN = (0, 255, 0)
        BLUE = (0, 0, 255)
        YELLOW = (255, 255, 0)

        cell_size = 40
        font = pygame.font.Font(None, 28)
        
        # Limpiar la pantalla
        screen.fill(WHITE)
        
        with self.lock:
            try:
                # Primero dibujamos la cuadrícula y las ubicaciones
                for y in range(self.map_size):
                    for x in range(self.map_size):
                        rect = pygame.Rect(x * cell_size, y * cell_size, cell_size, cell_size)
                        pygame.draw.rect(screen, BLACK, rect, 1)  # Borde de la celda
                        
                        # Dibujar ubicaciones (letras)
                        if y < len(self.map) and x < len(self.map[y]):
                            cell = self.map[y][x]
                            if isinstance(cell, str) and cell != ' ':
                                rect_interior = rect.inflate(-2, -2)
                                pygame.draw.rect(screen, BLUE, rect_interior)
                                text = font.render(str(cell), True, WHITE)
                                text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                                screen.blit(text, text_rect)
                
                # Luego dibujamos los taxis
                for taxi_id, taxi_info in self.taxis.items():
                    if 'position' in taxi_info:
                        x, y = taxi_info['position']
                        rect = pygame.Rect(x * cell_size, y * cell_size, cell_size, cell_size)
                        rect_interior = rect.inflate(-2, -2)
                        
                        # Color basado en el estado
                        if taxi_info.get('status') in ['KO', 'END']:
                            pygame.draw.rect(screen, RED, rect_interior)
                        else:
                            pygame.draw.rect(screen, GREEN, rect_interior)
                        
                        # ID del taxi
                        display_id = str(taxi_id)
                        if taxi_id in self.taxi_passengers:
                            display_id = f"{taxi_id}{self.taxi_passengers[taxi_id]}"
                        text = font.render(display_id, True, WHITE)
                        text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                        screen.blit(text, text_rect)
                
                # Finalmente dibujamos los clientes
                for customer_id, pos in self.customers.items():
                    x, y = pos
                    rect = pygame.Rect(x * cell_size, y * cell_size, cell_size, cell_size)
                    rect_interior = rect.inflate(-2, -2)
                    pygame.draw.rect(screen, YELLOW, rect_interior)
                    text = font.render(str(customer_id), True, BLACK)
                    text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                    screen.blit(text, text_rect)

                pygame.display.flip()
                
            except Exception as e:
                print(f"Error drawing map: {e}")
                print(traceback.format_exc())
                
    def initialize_pygame(self):
        try:
            pygame.init()
            self.screen = pygame.display.set_mode((800, 800))
            pygame.display.set_caption(f'Taxi {self.taxi_id} Map View')
            print("PyGame initialized successfully")
        except Exception as e:
            print(f"Error initializing PyGame: {e}")
            print(traceback.format_exc())

    def handle_pygame_events(self):
        while self.running:
            for event in pygame.event.get():
                if event.type == pygame.QUIT:
                    self.running = False
            self.draw_map(self.screen)
            self.clock.tick(30)

    def run(self):
        try:
            # Conectar con el servidor central
            self.connect_to_central()
            
            # Inicializar la interfaz de Pygame
            self.initialize_pygame()
            
            # Iniciar los threads
            threads = [
                threading.Thread(target=self.listen_for_sensors, daemon=True),       # Escuchar sensores
                threading.Thread(target=self.process_map_updates, daemon=True),      # Procesar actualizaciones del mapa
                threading.Thread(target=self.process_instructions, daemon=True),     # Procesar instrucciones
                threading.Thread(target=self.move_towards_destination, daemon=True), # Mover taxi
            ]

            # Iniciar todos los threads
            for thread in threads:
                thread.start()
            
            print("Todos los threads iniciados correctamente")

            # Bucle principal de Pygame y manejo de eventos
            while self.running:
                self.handle_pygame_events()

            # Cleanup
            pygame.quit()
            if self.sensor_socket:
                self.sensor_socket.close()

        except Exception as e:
            print(f"Error en el método run: {e}")
            print(traceback.format_exc())


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="EC_DE: Taxi Digital Engine")
    parser.add_argument("central_ip", help="IP address of the Central")
    parser.add_argument("central_port", type=int, help="Port of the Central")
    parser.add_argument("kafka_bootstrap_servers", help="Kafka bootstrap servers")
    parser.add_argument("sensor_port", type=int, help="Port to listen for sensor connections")
    parser.add_argument("taxi_id", help="Unique ID for this taxi")
    args = parser.parse_args()

    digital_engine = DigitalEngine(
        args.central_ip,
        args.central_port,
        args.kafka_bootstrap_servers,
        args.sensor_port,
        args.taxi_id
    )
    digital_engine.run()