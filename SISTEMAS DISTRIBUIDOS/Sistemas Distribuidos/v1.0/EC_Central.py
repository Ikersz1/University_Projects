# EC_Central.py

import json
import threading
import time
import pygame
import argparse
import sqlite3
from kafka import KafkaProducer, KafkaConsumer
from PyQt5.QtWidgets import QApplication, QMainWindow, QWidget, QVBoxLayout, QHBoxLayout, QTableWidget, QTableWidgetItem, QLabel
from PyQt5.QtCore import Qt, QTimer
from PyQt5.QtGui import QPainter, QImage
import sys

class PygameWidget(QWidget):
    def __init__(self, central, parent=None):
        super().__init__(parent)
        self.central = central
        self.setMinimumSize(800, 800)
        
        pygame.init()
        self.surface = pygame.Surface((800, 800))
        
        # Aumentar la frecuencia de actualización
        self.timer = QTimer()
        self.timer.timeout.connect(self.update)
        self.timer.start(16)  # ~60 FPS
        
    def paintEvent(self, event):
        if self.central and self.central.running:
            self.surface.fill((255, 255, 255))
            self.central.draw_map(self.surface)
            
            # Convertir y dibujar
            w = self.surface.get_width()
            h = self.surface.get_height()
            data = pygame.image.tostring(self.surface, 'RGB')
            image = QImage(data, w, h, w * 3, QImage.Format_RGB888)
            
            painter = QPainter(self)
            painter.drawImage(0, 0, image)

class StatusWindow(QMainWindow):
    def __init__(self, central):
        super().__init__()
        self.central = central
        self.setWindowTitle("*** EASY CAB Release 1 ***")
        self.setGeometry(100, 100, 1600, 900)
        
        # Widget central
        central_widget = QWidget()
        self.setCentralWidget(central_widget)
        main_layout = QHBoxLayout(central_widget)
        
        # Panel izquierdo para el mapa
        left_panel = QWidget()
        left_layout = QVBoxLayout(left_panel)
        self.pygame_widget = PygameWidget(self.central)
        left_layout.addWidget(self.pygame_widget)
        main_layout.addWidget(left_panel)
        
        # Panel derecho para las tablas
        right_panel = QWidget()
        right_layout = QVBoxLayout(right_panel)
        
        # Título
        title_label = QLabel("*** EASY CAB Release 1 ***")
        title_label.setAlignment(Qt.AlignCenter)
        right_layout.addWidget(title_label)
        
        # Tabla de Taxis
        self.taxi_table = QTableWidget()
        self.taxi_table.setColumnCount(3)
        self.taxi_table.setHorizontalHeaderLabels(["Id.", "Destino", "Estado"])
        right_layout.addWidget(QLabel("Taxis"))
        right_layout.addWidget(self.taxi_table)
        
        # Tabla de Clientes
        self.client_table = QTableWidget()
        self.client_table.setColumnCount(3)
        self.client_table.setHorizontalHeaderLabels(["Id.", "Destino", "Estado"])
        right_layout.addWidget(QLabel("Clientes"))
        right_layout.addWidget(self.client_table)
        
        main_layout.addWidget(right_panel)
        
        # Timer para actualizar las tablas
        self.timer = QTimer()
        self.timer.timeout.connect(self.update_tables)
        self.timer.start(1000)  # Actualizar cada segundo

    def update_tables(self):
        with self.central.lock:
            # Actualizar tabla de taxis
            self.taxi_table.setRowCount(len(self.central.taxis))
            for i, (taxi_id, data) in enumerate(self.central.taxis.items()):
                self.taxi_table.setItem(i, 0, QTableWidgetItem(str(taxi_id)))
                destination = '-'
                cursor = self.central.db_connection.cursor()
                cursor.execute('''
                    SELECT end_location FROM services 
                    WHERE taxi_id = ? AND status = 'OK'
                    ORDER BY id DESC LIMIT 1
                ''', (taxi_id,))
                result = cursor.fetchone()
                if result:
                    destination = result[0]
                self.taxi_table.setItem(i, 1, QTableWidgetItem(str(destination)))
                self.taxi_table.setItem(i, 2, QTableWidgetItem(str(data.get('status', '-'))))
            
            # Actualizar tabla de clientes
            self.client_table.setRowCount(len(self.central.customers))
            for i, (client_id, pos) in enumerate(self.central.customers.items()):
                self.client_table.setItem(i, 0, QTableWidgetItem(str(client_id)))
                cursor = self.central.db_connection.cursor()
                cursor.execute('''
                    SELECT end_location, taxi_id FROM services 
                    WHERE customer_id = ? AND status = 'OK'
                    ORDER BY id DESC LIMIT 1
                ''', (client_id,))
                result = cursor.fetchone()
                if result:
                    destination, taxi_id = result
                    self.client_table.setItem(i, 1, QTableWidgetItem(str(destination)))
                    self.client_table.setItem(i, 2, QTableWidgetItem(f"OK. Taxi {taxi_id}"))
                else:
                    self.client_table.setItem(i, 1, QTableWidgetItem('-'))
                    self.client_table.setItem(i, 2, QTableWidgetItem('Waiting'))
                    

class Central:
    def __init__(self, listen_port, kafka_bootstrap_servers, locations_file):
        self.listen_port = listen_port
        self.kafka_producer = KafkaProducer(
            bootstrap_servers=kafka_bootstrap_servers,
            value_serializer=lambda v: json.dumps(v).encode('utf-8')
        )
        self.kafka_consumer = KafkaConsumer(
            'taxi_requests',
            'taxi_status',
            bootstrap_servers=kafka_bootstrap_servers,
            value_deserializer=lambda v: json.loads(v.decode('utf-8'))
        )
        self.db_connection = sqlite3.connect('central.db', check_same_thread=False)
        self.locations = self.load_locations(locations_file)
        self.map_size = 20
        self.taxis = {}
        self.customers = {}
        self.taxi_passengers = {}
        self.lock = threading.Lock()
        self.last_map_state = None
        self.last_map_update_time = 0
        self.running = True
        #CUIDAO
        self.map_update_timer = QTimer()
        self.map_update_timer.timeout.connect(self.update_map)
        self.map_update_timer.start(100)  # Actualizar cada 100ms
        
        # Inicializar el mapa con espacios en blanco
        self.map = [[' ' for _ in range(self.map_size)] for _ in range(self.map_size)]
        # Colocar las ubicaciones en el mapa
        for loc_id, (x, y) in self.locations.items():
            if 0 <= x < self.map_size and 0 <= y < self.map_size:
                self.map[y][x] = loc_id

    def update_status_tables(self):
        """Actualiza las tablas de estado con la información actual"""
        with self.lock:
            # Actualizar estado de taxis
            for taxi_id, taxi_info in self.taxis.items():
                self.taxi_status[taxi_id] = {
                    'destination': self.get_taxi_destination(taxi_id),
                    'status': taxi_info['status']
                }
            
            # Actualizar estado de clientes
            cursor = self.db_connection.cursor()
            cursor.execute('''
                SELECT customer_id, taxi_id, end_location, status 
                FROM services 
                WHERE status = 'ASSIGNED'
            ''')
            for row in cursor.fetchall():
                customer_id, taxi_id, destination, status = row
                self.client_status[customer_id] = {
                    'destination': destination,
                    'status': f'OK. Taxi {taxi_id}'
                }
        
        # Actualizar la ventana de estado
        self.status_window.update_tables(self.taxi_status, self.client_status)

    def update_map(self):
        with self.lock:
            # Crear un nuevo estado temporal del mapa
            new_map = [[' ' for _ in range(self.map_size)] for _ in range(self.map_size)]
            
            # Colocar las ubicaciones
            for loc_id, (x, y) in self.locations.items():
                if 0 <= x < self.map_size and 0 <= y < self.map_size:
                    new_map[y][x] = loc_id
                
            # Colocar los taxis
            for taxi_id, taxi_info in self.taxis.items():
                x, y = taxi_info['position']
                if 0 <= x < self.map_size and 0 <= y < self.map_size:
                    new_map[y][x] = ('T', taxi_id)
                
            # Colocar los clientes
            for customer_id, (x, y) in self.customers.items():
                if 0 <= x < self.map_size and 0 <= y < self.map_size:
                    new_map[y][x] = ('C', customer_id)
            
            self.map = new_map
            
            # Enviar actualización del mapa después de cada cambio
            current_state = self.get_map_state()
            if self.states_are_different(current_state, self.last_map_state):
                self.send_map_update()
            
    def load_locations(self, file_path):
        with open(file_path, 'r') as file:
            data = json.load(file)
        locations = {}
        for loc in data['locations']:
            x, y = map(int, loc['POS'].split(','))
            locations[loc['Id']] = (x-1, y-1)  # Adjust to 0-based indexing
        return locations

    def init_db(self):
        cursor = self.db_connection.cursor()
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS taxis
            (id TEXT PRIMARY KEY, status TEXT, position_x INTEGER, position_y INTEGER)
        ''')
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS services
            (id INTEGER PRIMARY KEY AUTOINCREMENT, customer_id TEXT, taxi_id TEXT, 
             start_location TEXT, end_location TEXT, status TEXT)
        ''')
        self.db_connection.commit()
            
    def get_map_state(self):
        """Genera una representación del estado actual del mapa para comparación"""
        return {
            'map': [row[:] for row in self.map],
            'customers': dict(self.customers),
            'taxis': {k: dict(v) for k, v in self.taxis.items()}
        }
    
    def states_are_different(self, state1, state2):
        """Compara dos estados del mapa para ver si son diferentes"""
        if state1 is None or state2 is None:
            return True
        return (state1['map'] != state2['map'] or 
                state1['customers'] != state2['customers'] or 
                state1['taxis'] != state2['taxis'])
    
    def send_map_update(self):
        """Envía una actualización del mapa a los taxis"""
        current_time = time.time()
        
        # Solo enviar si han pasado al menos 0.1 segundos desde la última actualización
        if current_time - self.last_map_update_time >= 0.1:
            map_data = {
                'type': 'map_update',
                'map': self.map,
                'customers': self.customers,
                'taxis': self.taxis,
                'timestamp': current_time
            }
            self.kafka_producer.send('map_updates', map_data)
            
            self.last_map_state = self.get_map_state()
            self.last_map_update_time = current_time

    def process_messages(self):
        """Método mejorado para procesar mensajes de Kafka"""
        try:
            print("Starting to process messages...")
            for message in self.kafka_consumer:
                try:
                    topic = message.topic
                    data = message.value

                    if topic == 'taxi_requests':
                        if data.get('type') == 'pedir_taxi':
                            self.handle_pedir_taxi(data)
                    elif topic == 'taxi_status':
                        if data.get('type') == 'estado_taxi':
                            self.handle_estado_taxi(data)
                    
                    # Forzar actualización del mapa después de cada mensaje
                    self.update_map()
                    self.send_map_update()
                    
                except Exception as e:
                    print(f"Error processing message: {e}")
                    import traceback
                    traceback.print_exc()
                    continue
                    
        except Exception as e:
            print(f"Fatal error in process_messages: {e}")
            import traceback
            traceback.print_exc()


    def send_taxi_command(self, taxi_id, command, payload=None):
        """Envía un comando específico a un taxi."""
        command_message = {
            'taxi_id': taxi_id,
            'command': command,
            'payload': payload
        }
        self.kafka_producer.send('taxi_instructions', command_message)
        print(f"DEBUG: Sent command {command} to taxi {taxi_id} with payload: {payload}")

    def handle_taxi_instructions(self):
        while True:
            print("\nOpciones de comando para taxis:")
            print("1. Parar taxi")
            print("2. Reanudar taxi")
            print("3. Cambiar destino")
            print("4. Volver a la base")
            print("q. Salir")
            
            option = input("\nElija una opción: ")
            
            if option.lower() == 'q':
                print("Saliendo del menú de comandos.")
                break
                
            # Solicitar ID del taxi para cualquier opción válida
            if option in ['1', '2', '3', '4']:
                taxi_id = input("Introduzca el ID del taxi: ")
                
                if option == '1':
                    print(f"Enviando comando de parada al taxi {taxi_id}")
                    self.send_taxi_command(taxi_id, 'stop_taxi')
                    
                elif option == '2':
                    print(f"Enviando comando de reanudación al taxi {taxi_id}")
                    self.send_taxi_command(taxi_id, 'resume_taxi')
                    
                elif option == '3':
                    new_destination = input("Introduzca el nuevo destino (formato: x,y): ")
                    try:
                        x, y = map(int, new_destination.split(','))
                        print(f"Enviando nuevo destino [{x},{y}] al taxi {taxi_id}")
                        self.send_taxi_command(taxi_id, 'change_destination', {'new_destination': [x, y]})
                    except ValueError:
                        print("Error: Formato inválido. Use el formato: x,y (ejemplo: 5,3)")
                        
                elif option == '4':
                    print(f"Enviando comando de retorno a base al taxi {taxi_id}")
                    self.send_taxi_command(taxi_id, 'return_to_base')
            else:
                print("Opción no válida. Por favor, elija una opción del menú.")


    def handle_estado_taxi(self, status):
        taxi_id = str(status['taxi_id'])
        with self.lock:
            prev_position = self.taxis[taxi_id]['position'] if taxi_id in self.taxis else None
            
            self.taxis[taxi_id] = {
                'status': status['status'],
                'position': [int(status['position'][0]), int(status['position'][1])]
            }

            if prev_position and prev_position != self.taxis[taxi_id]['position']:
                cursor = self.db_connection.cursor()
                cursor.execute('''
                    SELECT customer_id, end_location, status
                    FROM services 
                    WHERE taxi_id = ? AND status = 'OK'
                    ORDER BY id DESC LIMIT 1
                ''', (taxi_id,))
                service = cursor.fetchone()
                
                if service:
                    customer_id, end_location, service_status = service
                    customer_pos = self.customers.get(customer_id)
                    end_location_coords = self.locations[end_location]  # Obtener coordenadas del destino
                    
                    if customer_pos and tuple(self.taxis[taxi_id]['position']) == tuple(customer_pos):
                        # Fusionar IDs cuando el taxi recoge al cliente
                        self.taxi_passengers[taxi_id] = customer_id
                        del self.customers[customer_id]
                        
                        self.kafka_producer.send('taxi_instructions', {
                            'type': 'move_instruction',
                            'taxi_id': taxi_id,
                            'destination': self.locations[end_location],
                            'phase': 'service'
                        })
                    elif tuple(self.taxis[taxi_id]['position']) == self.locations[end_location]:
                        # Notificar al cliente que ha llegado al destino
                        if taxi_id in self.taxi_passengers:
                            customer_id = self.taxi_passengers[taxi_id]
                            self.kafka_producer.send('customer_responses', {
                                'type': 'destination_reached',
                                'customer_id': customer_id,
                                'taxi_id': taxi_id,
                                'destination': list(end_location_coords)  # Incluir coordenadas del destino
                            })
                            del self.taxi_passengers[taxi_id]
                        
                        cursor.execute('''
                            UPDATE services 
                            SET status = 'END' 
                            WHERE taxi_id = ? AND status = 'OK'
                        ''', (taxi_id,))
                        self.db_connection.commit()
                        

            cursor = self.db_connection.cursor()
            cursor.execute('''
                INSERT OR REPLACE INTO taxis (id, status, position_x, position_y)
                VALUES (?, ?, ?, ?)
            ''', (taxi_id, status['status'], int(status['position'][0]), int(status['position'][1])))
            self.db_connection.commit()
            
    def handle_pedir_taxi(self, request):
        print(f"Processing taxi request: {request}")
        customer_id = request['customer_id']
        pickup = request['pickup']
        destination = request['destination']

        with self.lock:
            if pickup:
                pickup_x, pickup_y = pickup
                self.customers[customer_id] = (pickup_x, pickup_y)
                print(f"DEBUG: Added customer {customer_id} at position {pickup}")

            available_taxi_id = self.find_available_taxi()
            if available_taxi_id:
                taxi = self.taxis[available_taxi_id]  # Obtener el taxi disponible

                # Verificar si el taxi está en la misma posición que el cliente
                if taxi['position'] == pickup and taxi['status'] == 'END':
                    print(f"Taxi {available_taxi_id} is at the same position as customer {customer_id}. Assigning immediately.")
                    self.send_taxi_to_customer(available_taxi_id, customer_id, pickup, destination)
                else:
                    # Si el taxi no está en la misma posición, envíalo a recoger al cliente
                    self.send_taxi_to_customer(available_taxi_id, customer_id, pickup, destination)

                self.kafka_producer.send('customer_responses', {
                    'type': 'customer_assignment',
                    'taxi_id': available_taxi_id,
                    'customer_id': customer_id,
                    'pickup': pickup,
                    'destination': destination
                })
            else:
                print(f"No available taxis for customer {customer_id}.")
                self.kafka_producer.send('customer_responses', {
                    'type': 'no_taxi_available',
                    'customer_id': customer_id,
                    'pickup': pickup,
                    'destination': destination
                })


    def send_taxi_to_customer(self, taxi_id, customer_id, pickup, destination):
        print(f"Sending taxi {taxi_id} to pick up customer {customer_id} at {pickup}")
        self.kafka_producer.send('taxi_instructions', {
            'type': 'move_instruction',
            'taxi_id': taxi_id,
            'destination': pickup,
            'next_destination': destination,
            'phase': 'pickup'
        })
        cursor = self.db_connection.cursor()
        cursor.execute('''
            INSERT INTO services (customer_id, taxi_id, start_location, end_location, status)
            VALUES (?, ?, ?, ?, ?)
        ''', (customer_id, taxi_id, str(self.taxis[taxi_id]['position']), destination, 'OK'))
        self.db_connection.commit()


    def find_available_taxi(self):
        for taxi_id, taxi_info in self.taxis.items():
            if taxi_info['status'] in ['OK']:
                return taxi_id
        return None
    
    def draw_map(self, screen):
        if not self.running:
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
            # Dibujar la cuadrícula y contenido
            for y in range(self.map_size):
                for x in range(self.map_size):
                    rect = pygame.Rect(x * cell_size, y * cell_size, cell_size, cell_size)
                    pygame.draw.rect(screen, BLACK, rect, 1)

                    # Primero, verificar si hay una ubicación en esta posición
                    location_id = None
                    for loc_id, (loc_x, loc_y) in self.locations.items():
                        if x == loc_x and y == loc_y:
                            location_id = loc_id
                            pygame.draw.rect(screen, BLUE, rect)
                            text = font.render(str(loc_id), True, WHITE)
                            text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                            screen.blit(text, text_rect)
                            break

                    # Luego, verificar si hay un cliente en esta posición
                    for client_id, (client_x, client_y) in self.customers.items():
                        if x == client_x and y == client_y:
                            pygame.draw.rect(screen, YELLOW, rect)
                            text = font.render(str(client_id), True, BLACK)
                            text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                            screen.blit(text, text_rect)
                            break

                    # Finalmente, verificar si hay un taxi en esta posición
                    for taxi_id, taxi_info in self.taxis.items():
                        taxi_x, taxi_y = taxi_info['position']
                        if x == taxi_x and y == taxi_y:
                            # Color según estado
                            if taxi_info['status'] in ['KO', 'END', 'STOP']:
                                pygame.draw.rect(screen, RED, rect)
                            else:
                                pygame.draw.rect(screen, GREEN, rect)
                            
                            # Mostrar ID del taxi y del pasajero si lo tiene
                            display_id = taxi_id
                            if taxi_id in self.taxi_passengers:
                                display_id = f"{taxi_id}{self.taxi_passengers[taxi_id]}"
                            
                            text = font.render(str(display_id), True, WHITE)
                            text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                            screen.blit(text, text_rect)
                            break                     
    def run(self):
        try:
            # Inicializar PyQt5
            app = QApplication(sys.argv)
            
            # Crear la ventana principal
            self.status_window = StatusWindow(self)
            self.status_window.show()
            
            self.init_db()
            
            # Iniciar threads
            message_thread = threading.Thread(target=self.process_messages)
            message_thread.daemon = True
            message_thread.start()
            
            command_thread = threading.Thread(target=self.handle_taxi_instructions)
            command_thread.daemon = True
            command_thread.start()
            
            # Enviar estado inicial del mapa
            self.update_map()
            self.send_map_update()
            
            # Iniciar el loop de eventos de Qt
            sys.exit(app.exec_())
            
        except Exception as e:
            print(f"Fatal error in run method: {e}")
        finally:
            self.running = False
            pygame.quit()


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Central Taxi System')
    parser.add_argument('--listen_port', type=int, required=True)
    parser.add_argument('--kafka_bootstrap_servers', type=str, required=True)
    parser.add_argument('--locations_file', type=str, required=True)
    args = parser.parse_args()
    
    central = Central(args.listen_port, args.kafka_bootstrap_servers, args.locations_file)
    central.run()