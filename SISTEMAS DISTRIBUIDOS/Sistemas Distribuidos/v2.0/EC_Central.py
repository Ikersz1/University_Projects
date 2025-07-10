# EC_Central.py

import uuid
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
from typing import Dict
import sys
import signal
import atexit
import requests
from flask import Flask, request, jsonify

from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC
from cryptography.hazmat.primitives.hashes import SHA256
from cryptography.hazmat.primitives.kdf.hkdf import HKDF
from cryptography.hazmat.backends import default_backend
import base64

from Crypto.Cipher import AES
from Crypto.Util.Padding import pad, unpad

app = Flask(__name__)
import logging
import socket
from datetime import datetime
import os

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
        self.CTC_URL = self.read_ctc_url()
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
        self.traffic_status = "OK"  # Estado inicial del tráfico
        self.lock = threading.Lock()
        self.last_map_state = None
        self.last_map_update_time = 0
        self.running = True
        # Configuración de la central para la auditoria.
        self.setup_logging()
        
        self.customer_last_active: Dict[str, float] = {}
        self.customer_check_interval = 60  # segundos para considerar a un cliente desconectado
        
        # Iniciar el thread de monitoreo de clientes
        self.customer_monitor_thread = threading.Thread(target=self.monitor_customers)
        self.customer_monitor_thread.daemon = True
        self.customer_monitor_thread.start()

        # Iniciar el thread de monitoreo del tráfico
        self.traffic_monitor_thread = threading.Thread(target=self.monitor_traffic)
        self.traffic_monitor_thread.daemon = True
        self.traffic_monitor_thread.start()
        
        # Configurar manejadores de señales para cierre graceful
        signal.signal(signal.SIGINT, self.handle_shutdown)
        signal.signal(signal.SIGTERM, self.handle_shutdown)
        atexit.register(self.cleanup)
        
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
                
        self.kafka_consumer_taxis = KafkaConsumer(
            'taxi_status',
            bootstrap_servers=kafka_bootstrap_servers,
            value_deserializer=lambda v: json.loads(v.decode('utf-8')),
            group_id='central_taxi_status',
            auto_offset_reset='earliest',
            enable_auto_commit=True
        )

        self.reconnect_timer = QTimer()
        self.reconnect_timer.timeout.connect(self.try_reconnect)
        self.reconnect_timer.start(5000)  # Intentar reconectar cada 5 segundos

    def read_ctc_url(self):
        try:
            with open('ctc_url.txt', 'r') as file:
                url = file.read().strip()
                if not url:
                    raise ValueError("El archivo de URL está vacío.")
                return url
        except FileNotFoundError:
            print("Error: No se encontró el archivo de URL de CTC.")
        except Exception as e:
            print(f"Error leyendo el archivo de URL de CTC: {e}")
        
    def setup_logging(self):
        log_file = 'audit.log'
        if os.path.exists(log_file):  # Si el archivo existe
            os.remove(log_file)  # Eliminar el archivo

        logging.basicConfig(
            filename=log_file,
            level=logging.INFO,
            format='%(asctime)s - %(message)s',
            datefmt='%Y-%m-%d %H:%M:%S'
        )
    
    def log_event(self, event_type, details):
        """Registra un evento en el archivo de auditoría."""
        ip_address = socket.gethostbyname(socket.gethostname())
        log_message = {
            "timestamp": datetime.now().isoformat(),
            "ip_address": ip_address,
            "event_type": event_type,
            "details": details
        }
        logging.info(log_message)

    def generate_token(self, taxi_id):

        token = str(uuid.uuid4())  # Genera un token único
        cursor = self.db_connection.cursor()
        cursor.execute('''
            INSERT OR REPLACE INTO taxi_tokens (taxi_id, token)
            VALUES (?, ?)
        ''', (taxi_id, token))
        self.db_connection.commit()
        return token
    
    def validate_token(self, taxi_id, token):

        cursor = self.db_connection.cursor()
        cursor.execute('''
            SELECT token FROM taxi_tokens WHERE taxi_id = ?
        ''', (taxi_id,))
        result = cursor.fetchone()
        return result and result[0] == token
    
    def monitor_traffic(self):

        self.last_traffic_status = "OK"  # Inicializamos con un estado válido por defecto
        self.last_temperature = 15  # Por defecto, una temperatura positiva válida
        self.last_city = "Alicante"  # Ciudad por defecto

        while self.running:
            try:
                url = self.read_ctc_url()
                response = requests.get(f"{url}/traffic_status")
                
                if response.status_code == 200:
                    data = response.json()
                    
                    with self.lock:
                        self.traffic_status = data["status"]
                        self.last_traffic_status = self.traffic_status
                        self.last_temperature = data["temperature"]
                        self.last_city = data["city"]
                    print(f"Estado del tráfico: {self.traffic_status} (Ciudad: {data['city']}, Temp: {data['temperature']}°C)")
                    
                    # Registrar el evento de cambio en el estado del tráfico en la auditoría
                    self.log_event("Traffic Status Change", {
                        "status": self.traffic_status,
                        "city": data["city"],
                        "temperature": data["temperature"]
                    })
                    
                    if self.traffic_status == "KO":
                        self.handle_traffic_ko()
                
                else:
                    # Extraer mensaje de error si está disponible
                    error_message = response.json().get("error", "Unknown error")
                    print(f"CTC Error: {error_message} (Status code: {response.status_code})")
                    self.log_event("Traffic Status Error", {
                        "error": error_message,
                        "status_code": response.status_code
                    })
                    
                    # Usamos el último estado válido
                    self.use_last_known_status()
            except Exception:
                # Manejo de errores de conexión con EC_CTC
                print("CTC disconnected. Using last known status.")
                self.log_event("Traffic Status Fetch Error", {"Error": "CTC disconnected"})
                
                # Usamos el último estado válido
                self.use_last_known_status()

            time.sleep(8)


    def use_last_known_status(self):
        
        with self.lock:
            self.traffic_status = self.last_traffic_status
            print(f"Ultimo estado: {self.traffic_status} (Ciudad: {self.last_city}, Temp: {self.last_temperature}°C)")
            
            self.log_event("Traffic Status Fallback", {
                "status": self.traffic_status,
                "city": self.last_city,
                "temperature": self.last_temperature
            })
            
            if self.traffic_status == "KO":
                print("Last known status is KO. Handling traffic KO")
                self.handle_traffic_ko()

    def handle_traffic_ko(self):
        
        print("Traffic is in danger, notifying taxis to return to base...")
        for taxi_id in list(self.taxis.keys()):
            self.send_taxi_command(taxi_id, "return_to_base")
 
    def monitor_customers(self):
        """Monitorea la actividad de los clientes y detecta desconexiones solo para los que no tienen taxi asignado."""
        while self.running:
            current_time = time.time()
            disconnected_customers = []
            
            with self.lock:
                for customer_id in list(self.customers.keys()):
                    # Verificar si el cliente tiene taxi asignado
                    cursor = self.db_connection.cursor()
                    try:
                        cursor.execute('''
                            SELECT taxi_id FROM services 
                            WHERE customer_id = ? AND status = 'OK'
                        ''', (customer_id,))
                        result = cursor.fetchone()
                    finally:
                        cursor.close()  # Cerrar el cursor después de cada operación
                    
                    if not result:  # Proceder solo si no hay taxi asignado
                        last_active = self.customer_last_active.get(customer_id, 0)
                        if current_time - last_active > self.customer_check_interval:
                            disconnected_customers.append(customer_id)
                
                # Procesar clientes desconectados
                for customer_id in disconnected_customers:
                    print(f"El cliente {customer_id} se ha desconectado")
                    
                    # Eliminar al cliente del mapa y registros
                    if customer_id in self.customers:
                        del self.customers[customer_id]
                    if customer_id in self.customer_last_active:
                        del self.customer_last_active[customer_id]
            
            # Forzar actualización del mapa si hubo cambios
            if disconnected_customers:
                self.update_map()
                self.send_map_update()
            
            # Esperar antes de la siguiente comprobación
            time.sleep(1)

            
    def try_reconnect(self):
        try:
            if not self.kafka_consumer_taxis.subscription():
                print("Reconnecting to Kafka topics...")
                self.kafka_consumer_taxis.subscribe(['taxi_status'])
                self.process_missed_taxi_updates()
        except Exception as e:
            # Registrar el error en los logs de auditoría
            self.log_event("Kafka Reconnection Error", {
                "error": str(e),
                "action": "Reconnecting to Kafka topics",
                "details": {
                    "topics": ['taxi_status']
                }
            })
            print(f"Error reconnecting to Kafka topics: {e}")
            
    def process_missed_taxi_updates(self):
        print("Processing missed taxi updates...")
        try:
            for message in self.kafka_consumer_taxis:
                data = message.value
                self.handle_estado_taxi(data)
        except Exception as e:
            # Registrar el error en los logs de auditoría
            self.log_event("Missed Taxi Updates Processing Error", {
                "error": str(e),
                "action": "Processing missed taxi updates",
                "details": {}
            })
            print(f"Error processing missed taxi updates: {e}")
                
    def handle_shutdown(self, signum, frame):
        """Manejador de señales para cierre controlado"""
        print("\nIniciando cierre controlado de la central...")
        self.notify_shutdown()
        self.running = False
        sys.exit(0)
        
    def cleanup(self):
        """Limpieza final antes del cierre."""
        if self.running:
            self.running = False
            
            # Detener el bucle principal de Pygame si está activo
            if hasattr(self, 'status_window') and self.status_window:
                print("Cerrando ventana de Pygame...")
                self.status_window.close()  # Cierra la ventana de Pygame
            
            # Cerrar recursos de Pygame
            pygame.quit()
            print("Pygame cerrado correctamente.")
            
            # Esperar a que todos los threads terminen
            print("Esperando que los threads terminen...")
            if hasattr(self, 'customer_monitor_thread'):
                self.customer_monitor_thread.join(timeout=1.0)
            if hasattr(self, 'traffic_monitor_thread'):
                self.traffic_monitor_thread.join(timeout=1.0)
            
            # Cerrar Kafka
            if hasattr(self, 'kafka_producer'):
                self.kafka_producer.close()
            if hasattr(self, 'kafka_consumer'):
                self.kafka_consumer.close()
            if hasattr(self, 'kafka_consumer_taxis'):
                self.kafka_consumer_taxis.close()
            
            # Notificar cierre exitoso
            print("Recursos liberados exitosamente.")

            
    def notify_shutdown(self):
        """Notifica a todos los taxis y clientes que la central se está cerrando"""
        try:
            # Notificar a todos los taxis activos
            shutdown_message = {
                'type': 'central_shutdown',
                'message': 'La central se ha caído. El servicio continuará en modo autónomo.'
            }
            
            # Enviar mensaje a taxis
            self.kafka_producer.send('taxi_instructions', shutdown_message)
            
            # Enviar mensaje a clientes
            self.kafka_producer.send('customer_responses', shutdown_message)
            
            # Asegurar que los mensajes se envíen
            self.kafka_producer.flush()
            
            print("Mensajes de cierre enviados a taxis y clientes")
            
        except Exception as e:
            # Registrar el error en los logs de auditoría
            self.log_event("Shutdown Notification Error", {
                "error": str(e),
                "action": "Notifying shutdown",
                "details": {}
            })
            print(f"Error al notificar el cierre: {e}")
        finally:
            # Cerrar conexiones
            if hasattr(self, 'kafka_producer'):
                self.kafka_producer.close()
            if hasattr(self, 'kafka_consumer'):
                self.kafka_consumer.close()
            if hasattr(self, 'db_connection'):
                self.db_connection.close()

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
            # Create a new temporary map state
            new_map = [[' ' for _ in range(self.map_size)] for _ in range(self.map_size)]
            
            # Place locations
            for loc_id, (x, y) in self.locations.items():
                if 0 <= x < self.map_size and 0 <= y < self.map_size:
                    new_map[y][x] = loc_id
            
            # Place taxis
            for taxi_id, taxi_info in self.taxis.items():
                x, y = taxi_info['position']
                if 0 <= x < self.map_size and 0 <= y < self.map_size:
                    new_map[y][x] = ('T', taxi_id)
            
            # Place customers
            for customer_id, customer_data in self.customers.items():
                # If customer_data is a tuple, it's the original coordinate format
                if isinstance(customer_data, tuple) and len(customer_data) == 2:
                    x, y = customer_data
                # If it's a string (new format with status), extract the original coordinates
                elif isinstance(customer_data, str) and customer_data.startswith('GO'):
                    continue  # Skip customers already in a taxi
                else:
                    continue  # Skip any unexpected data format
                
                if 0 <= x < self.map_size and 0 <= y < self.map_size:
                    new_map[y][x] = ('C', customer_id)
            
            self.map = new_map
            
            # Send map update after each change
            current_state = self.get_map_state()
            if self.states_are_different(current_state, self.last_map_state):
                self.send_map_update()
                
            # Generar y guardar el JSON actualizado
            self.save_interface_state()

    def save_interface_state(self):
        # Prepare enhanced taxi information
        enhanced_taxis = {}
        
        for taxi_id, taxi_info in self.taxis.items():
            # Initialize destination as '-'
            destination = '-'
            
            # Try to fetch the most recent service for this taxi
            cursor = self.db_connection.cursor()
            cursor.execute('''
                SELECT end_location FROM services 
                WHERE taxi_id = ? AND status = 'OK'
                ORDER BY id DESC LIMIT 1
            ''', (taxi_id,))
            result = cursor.fetchone()
            
            if result:
                destination = result[0]
            
            # Prepare enhanced taxi data
            enhanced_taxis[taxi_id] = {
                'position': taxi_info['position'],
                'status': taxi_info.get('status', '-'),
                'destination': destination
            }

        # Prepare enhanced customer information
        enhanced_customers = {}
        
        for client_id, pos in self.customers.items():
            # Try to fetch the most recent service for this customer
            cursor = self.db_connection.cursor()
            cursor.execute('''
                SELECT end_location, taxi_id FROM services 
                WHERE customer_id = ? AND status = 'OK'
                ORDER BY id DESC LIMIT 1
            ''', (client_id,))
            result = cursor.fetchone()
            
            if result:
                destination, taxi_id = result
                customer_status = f"OK. Taxi {taxi_id}"
            else:
                destination = '-'
                customer_status = 'Waiting'
            
            enhanced_customers[client_id] = {
                'position': pos if isinstance(pos, tuple) else None,
                'destination': destination,
                'status': customer_status
            }

        # Create the interface state dictionary
        interface_state = {
            "map": self.map,  # Estado actual del mapa
            "customers": enhanced_customers,
            "taxis": enhanced_taxis,
            "locations": self.locations,
            "timestamp": time.time()  # Marca de tiempo actual
        }

        # Guardar en un archivo JSON
        try:
            with open('mapa.json', 'w') as file:
                json.dump(interface_state, file, indent=4)
        except Exception as e:
            print(f"Error saving interface state: {e}")
            
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
                
        # Crear la tabla taxi_tokens nuevamente
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS taxi_tokens
            (taxi_id TEXT PRIMARY KEY,
             token TEXT,
             issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS taxi_keys (
                taxi_id TEXT PRIMARY KEY,
                symmetric_key TEXT,
                issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
        ''')
        
        # Crear las demás tablas si no existen
        
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

    def get_taxi_key(self, taxi_id):
        """Recupera la clave simétrica para un taxi desde la base de datos."""
        cursor = self.db_connection.cursor()
        cursor.execute('SELECT symmetric_key FROM taxi_keys WHERE taxi_id = ?', (taxi_id,))
        row = cursor.fetchone()
        if row:
            return base64.urlsafe_b64decode(row[0])  # Devuelve la clave como bytes
        return None

    def encrypt_message(self, taxi_id, message):
        """Encripta un mensaje usando la clave del taxi."""
        key = self.get_taxi_key(taxi_id)
        if not key:
            raise ValueError(f"No symmetric key found for taxi {taxi_id}")
        
        cipher = AES.new(key, AES.MODE_CBC)
        # Aseguramos que el mensaje sea un byte string
        message_bytes = str(message).encode('utf-8')
        # Rellenar el mensaje para que sea múltiplo de 16 bytes
        padded_message = pad(message_bytes, AES.block_size)
        encrypted_message = cipher.encrypt(padded_message)
        # Retornar el mensaje cifrado junto con el IV utilizado para el cifrado
        iv = base64.urlsafe_b64encode(cipher.iv).decode('utf-8')
        encrypted_message_base64 = base64.urlsafe_b64encode(encrypted_message).decode('utf-8')
        return iv, encrypted_message_base64
    
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

    def decrypt_message(self, iv, encrypted_message, taxi_id):
        """Desencripta un mensaje usando la clave simétrica del taxi."""
        key = self.get_taxi_key(taxi_id)  # Implementa esta función para obtener la clave del taxi.
        if not key:
            raise ValueError(f"No symmetric key found for taxi {taxi_id}")
        
        try:
            cipher = AES.new(key, AES.MODE_CBC, iv=base64.urlsafe_b64decode(iv))
            encrypted_message_bytes = base64.urlsafe_b64decode(encrypted_message)
            decrypted_message = unpad(cipher.decrypt(encrypted_message_bytes), AES.block_size)
            return decrypted_message.decode('utf-8')
        except Exception as e:
            #print(f"Error during decryption: {e}")
            raise


    def process_messages(self):
        """Procesa mensajes de Kafka."""
        try:
            print("Starting to process messages...")
            for message in self.kafka_consumer:
                try:
                    topic = message.topic
                    data = message.value

                    if topic == 'taxi_requests' and data.get('type') == 'pedir_taxi':
                        self.handle_pedir_taxi(data)
                        self.log_event("Taxi Request", {"request_data": data})
                        time.sleep(0.1)

                    elif topic == 'taxi_status' and data.get('type') == 'estado_taxi':
                        self.handle_estado_taxi(data)
                        self.log_event("Taxi Status Update Processed", {"status_data": data})

                    # Actualización del mapa
                    self.update_map()
                    time.sleep(0.1)
                    self.send_map_update()

                except Exception as e:
                    self.log_event("Message Handling Error", {"error": str(e), "message_data": message.value})
                    print(f"Error processing message: {e}")
                    import traceback
                    traceback.print_exc()
                    continue
        except Exception as e:
            self.log_event("Fatal Message Processing Error", {"error": str(e)})
            print(f"Fatal error in process_messages: {e}")
            import traceback
            traceback.print_exc()
            raise


    def send_taxi_command(self, taxi_id, command, payload=None):
        """Envía un comando cifrado a un taxi."""
        # Cifrar los datos antes de enviarlos
        iv, encrypted_command = self.encrypt_message(taxi_id, command)
        iv_payload, encrypted_payload = self.encrypt_message(taxi_id, payload) if payload else (None, None)

        command_message = {
            'taxi_id': taxi_id,
            'command': encrypted_command,
            'payload': encrypted_payload,
            'iv': iv,
            'iv_payload': iv_payload
        }
        self.kafka_producer.send('taxi_instructions', command_message)
        #print(f"DEBUG: Sent encrypted command {command} to taxi {taxi_id} with encrypted payload: {encrypted_payload}")

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
                while True:
                    respuesta = input("¿Está seguro de que desea salir? (Y/N): ").strip().lower()
                    if respuesta == 'y':
                        print("\nIniciando cierre controlado de la central...")
                        
                        cursor = self.db_connection.cursor()
                        cursor.execute('DROP TABLE IF EXISTS taxi_tokens')
                        self.db_connection.commit()
                        print("Tabla 'taxi_tokens' eliminada correctamente.")
                        
                        self.notify_shutdown()  # Notificar a taxis y clientes
                        self.cleanup()  # Liberar recursos y cerrar procesos
                        print("Cerrando aplicación. ¡Hasta luego!")
                        exit(0)  # Salida limpia
                    elif respuesta == 'n':
                        print("Cancelando la operación de salida. Regresando al menú.")
                        break
                    else:
                        print("Respuesta inválida. Por favor, ingrese 'Y' o 'N'.")

            
            # Opciones para manejar comandos específicos de taxis
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
        """
        Maneja el estado reportado por un taxi. Descifra los campos sensibles,
        registra eventos de autenticación, actualiza el estado y maneja incidencias.
        """
        import time  # Asegúrate de que el módulo time esté importado

        try:
            taxi_id = status['taxi_id']  # Convertir el ID del taxi a string

            # Extraer los campos cifrados y sus IVs
            encrypted_status = status.get('encrypted_status')
            iv_status = status.get('iv_status')
            encrypted_position = status.get('encrypted_position')
            iv_position = status.get('iv_position')
            encrypted_token = status.get('encrypted_token')
            iv_token = status.get('iv_token')

            # Verificar que todos los campos necesarios estén presentes
            if not all([encrypted_status, iv_status, encrypted_position, iv_position, encrypted_token, iv_token]):
                raise ValueError("Missing encrypted fields in status message.")

            # Descifrar los campos
            try:
                status_decrypted = self.decrypt_message(iv_status, encrypted_status, taxi_id)
                position_decrypted = self.decrypt_message(iv_position, encrypted_position, taxi_id)
                token_decrypted = self.decrypt_message(iv_token, encrypted_token, taxi_id)
            except Exception as e:
                self.log_event("Decryption Error", {
                    "error": str(e),
                    "taxi_id": taxi_id,
                    "status_data": status
                })
                print(f"Error decrypting message for taxi {taxi_id}: {e}")
                return

            # Validar el token
            if not self.validate_token(taxi_id, token_decrypted):
                print(f"Invalid token for taxi {taxi_id}. Ignoring message.")
                return

            with self.lock:  # Bloquear acceso seguro a los datos compartidos
                prev_position = self.taxis[taxi_id]['position'] if taxi_id in self.taxis else None

                # Actualizar estado y posición
                self.taxis[taxi_id] = {
                    'status': status_decrypted,
                    'position': [int(coord) for coord in position_decrypted.strip('[]').split(',')]  # Convertir la posición a lista de enteros
                }

                # Registrar autenticación y actualización de estado
                self.log_event("Taxi Authenticated", {
                    "taxi_id": taxi_id,
                    "status": status_decrypted,
                    "position": self.taxis[taxi_id]['position']
                })

                self.log_event("Taxi Status Update", {
                    "taxi_id": taxi_id,
                    "status": status_decrypted,
                    "position": self.taxis[taxi_id]['position'],
                    "previous_position": prev_position
                })

                # Manejar desconexión de taxi
                if self.taxis[taxi_id]['status'] == 'DISCONNECTED':
                    print(f"Taxi {taxi_id} desconectado")
                    self.expire_token(taxi_id)
                    self.send_taxi_command(taxi_id, 'desconectar')

                # Verificar si el taxi está en estado 'BASE' y en posición (1,1)
                if self.taxis[taxi_id]['status'] == 'BASE' and tuple(self.taxis[taxi_id]['position']) == (1, 1):
                    print(f"Taxi {taxi_id} está en estado 'BASE' en la posición (1,1). Desconectando...")
                    self.send_taxi_command(taxi_id, 'disconnect')
                    return  # Salir de la función para evitar más procesamiento

                # Consultar servicio asociado al taxi
                cursor = self.db_connection.cursor()
                cursor.execute('''
                    SELECT customer_id, end_location, status
                    FROM services 
                    WHERE taxi_id = ? AND status = 'OK'
                    ORDER BY id DESC LIMIT 1
                ''', (taxi_id,))
                service = cursor.fetchone()

                if service:
                    end_location=None
                    customer_id, end_location, service_status = service
                    
                    # Manejar diferentes formatos posibles de customer_data
                    customer_pos = None
                    if customer_id in self.customers:
                        customer_data = self.customers[customer_id]
                        if isinstance(customer_data, tuple) and len(customer_data) == 2:
                            customer_pos = customer_data
                        elif isinstance(customer_data, str) and 'GO' in customer_data:
                            # Ya está en el taxi
                            customer_pos = None
                            
                    # Obtener las coordenadas del destino final
                    end_location_coords = self.locations.get(end_location)

                    if not end_location_coords:
                        print(f"Invalid end location: {end_location}. Expected coordinates but got None.")
                        return  # Salir si el destino no es válido

                    # Si el taxi ya está en la posición del cliente, realizar la recogida
                    if customer_pos and tuple(self.taxis[taxi_id]['position']) == tuple(customer_pos):
                        print(f"Taxi {taxi_id} ya está en la posición del cliente {customer_id}, recogiendo...")
                        self.customers[customer_id] = f"GO ({taxi_id})"
                        self.taxi_passengers[taxi_id] = customer_id

                        self.kafka_producer.send('taxi_instructions', {
                            'type': 'move_instruction',
                            'taxi_id': taxi_id,
                            'destination': end_location_coords,
                            'next_destination': None,
                        })
                        return  # Salir después de la recogida

                    # Si el taxi ya ha llegado al destino final
                    elif tuple(self.taxis[taxi_id]['position']) == tuple(end_location_coords) and self.taxis[taxi_id]['status'] == 'END':
                        print(f"Taxi ha llegado a la posición final {end_location_coords}")
                        if taxi_id in self.taxi_passengers:
                            customer_id = self.taxi_passengers[taxi_id]
                            del self.taxi_passengers[taxi_id]

                            self.customers[customer_id] = (end_location_coords[0], end_location_coords[1])

                            self.kafka_producer.send('customer_responses', {
                                'type': 'destination_reached',
                                'customer_id': customer_id,
                                'taxi_id': taxi_id,
                                'destination': list(end_location_coords)  # Incluir coordenadas del destino
                            })

                        cursor.execute('''
                            UPDATE services 
                            SET status = 'END' 
                            WHERE taxi_id = ? AND status = 'OK'
                        ''', (taxi_id,))
                        self.db_connection.commit()

                # Actualizar estado del taxi en la base de datos
                cursor.execute('''
                    INSERT OR REPLACE INTO taxis (id, status, position_x, position_y)
                    VALUES (?, ?, ?, ?)
                ''', (taxi_id, self.taxis[taxi_id]['status'], int(self.taxis[taxi_id]['position'][0]), int(self.taxis[taxi_id]['position'][1])))
                self.db_connection.commit()

        except KeyError as e:
            self.log_event("Taxi Data Key Error", {
                "error": str(e),
                "status_data": status
            })
            raise

        except Exception as e:
            self.log_event("Taxi Handling General Error", {
                "taxi_id": status.get('taxi_id', 'Unknown'),
                "error": str(e)
            })
            raise

            
    def handle_pedir_taxi(self, request):
        customer_id = request['customer_id']
        pickup = request['pickup']
        destination = request['destination']

        with self.lock:
            # Actualizar timestamp de última actividad del cliente
            self.customer_last_active[customer_id] = time.time()
            
            if pickup:
                pickup_x, pickup_y = pickup
                self.customers[customer_id] = (pickup_x, pickup_y)
                #print(f"DEBUG: Added customer {customer_id} at position {pickup}")

            available_taxi_id = self.find_available_taxi()
            if available_taxi_id:
                self.assign_taxi_to_customer(available_taxi_id, customer_id, pickup, destination)
            else:
                print(f"No available taxis for customer {customer_id}.")
                self.notify_no_taxi_available(customer_id, pickup, destination)

    def assign_taxi_to_customer(self, taxi_id, customer_id, pickup, destination):
        # Enviar instrucciones al taxi
        self.send_taxi_to_customer(taxi_id, customer_id, pickup, destination)

        # Notificar al cliente de la asignación del taxi
        self.kafka_producer.send('customer_responses', {
            'type': 'customer_assignment',
            'taxi_id': taxi_id,
            'customer_id': customer_id,
            'pickup': pickup,
            'destination': destination
        })

        # Registrar el servicio en la base de datos
        self.record_service_in_db(customer_id, taxi_id, pickup, destination)

    def send_taxi_to_customer(self, taxi_id, customer_id, pickup, destination):
        """Envía instrucciones de movimiento cifradas a un taxi."""
        iv_pickup, encrypted_pickup = self.encrypt_message(taxi_id, pickup)
        iv_destination, encrypted_destination = self.encrypt_message(taxi_id, destination)

        self.kafka_producer.send('taxi_instructions', {
            'type': 'move_instruction',
            'taxi_id': taxi_id,
            'encrypted_pickup': encrypted_pickup,
            'encrypted_destination': encrypted_destination,
            'iv_pickup': iv_pickup,
            'iv_destination': iv_destination,
        })
        print(f"DEBUG: Sent move instruction for taxi {taxi_id} to customer {customer_id}")

    def notify_no_taxi_available(self, customer_id, pickup, destination):
        # Notificar que no hay taxis disponibles
        self.kafka_producer.send('customer_responses', {
            'type': 'no_taxi_available',
            'customer_id': customer_id,
            'pickup': pickup,
            'destination': destination
        })

    def record_service_in_db(self, customer_id, taxi_id, pickup, destination):
        cursor = self.db_connection.cursor()
        cursor.execute('''
            INSERT INTO services (customer_id, taxi_id, start_location, end_location, status)
            VALUES (?, ?, ?, ?, ?)
        ''', (customer_id, taxi_id, str(self.taxis[taxi_id]['position']), destination, 'OK'))
        self.db_connection.commit()

    def find_available_taxi(self):
        for taxi_id, taxi_info in self.taxis.items():
            if taxi_info['status'] in ['OK', 'END']:
                return taxi_id
        return None

    def expire_token(self, taxi_id):
        cursor = self.db_connection.cursor()
        cursor.execute('DELETE FROM taxi_tokens WHERE taxi_id = ?', (taxi_id,))
        self.db_connection.commit()

    # Función para generar una clave simétrica segura
    def generate_symmetric_key(self, taxi_id):
        key = str(base64.urlsafe_b64encode(os.urandom(32)).decode('utf-8'))
        cursor = self.db_connection.cursor()
        cursor.execute('''
            INSERT OR REPLACE INTO taxi_keys (taxi_id, symmetric_key)
            VALUES (?, ?)
        ''', (taxi_id, key))
        self.db_connection.commit()
        return key        
    
    @app.route('/authenticate', methods=['POST'])
    def authenticate_taxi():
        data = request.get_json()
        taxi_id = data.get('taxi_id')
        if not taxi_id:
            return jsonify({"error": "Taxi ID is required"}), 400
        
        token = central.generate_token(taxi_id)  # Generar token
        key = central.generate_symmetric_key(taxi_id)
        return jsonify({"token": token, "key": key}), 200
       

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

                
                    for client_id, client_data in self.customers.items():
                        # Si es una tupla con coordenadas
                        if isinstance(client_data, tuple) and len(client_data) == 2:
                            client_x, client_y = client_data
                            if x == client_x and y == client_y:
                                pygame.draw.rect(screen, YELLOW, rect)
                                text = font.render(str(client_id), True, BLACK)
                                text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                                screen.blit(text, text_rect)
                        # Si es un string de estado (GO)
                        elif isinstance(client_data, str) and client_data.startswith('GO'):
                            continue  # No dibujar clientes ya en un taxi

                    # Finalmente, verificar si hay un taxi en esta posición
                    for taxi_id, taxi_info in self.taxis.items():
                        taxi_x, taxi_y = taxi_info['position']
                        if x == taxi_x and y == taxi_y:
                            # Color según estado
                            if taxi_info['status'] in ['KO', 'END', 'STOP', 'BASE']:
                                pygame.draw.rect(screen, RED, rect)
                            elif taxi_info['status'] in ['DISCONNECTED']:
                                pygame.draw.rect(screen, BLACK, rect)    
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
    # Ejecutar Flask en un hilo separado
    flask_thread = threading.Thread(target=lambda: app.run(host="0.0.0.0", port=8000, debug=False))
    flask_thread.daemon = True
    flask_thread.start()
    
    central.run()
