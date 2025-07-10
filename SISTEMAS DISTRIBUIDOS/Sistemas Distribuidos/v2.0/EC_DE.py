import socket
import threading
import json
from kafka import KafkaProducer, KafkaConsumer
import argparse
import time
import pygame
import traceback
import sqlite3
import requests
import os
import signal

from Crypto.Cipher import AES
from Crypto.Util.Padding import unpad
from Crypto.Util.Padding import pad

import base64

import os

class DigitalEngine:
    def __init__(self, central_ip, central_port, kafka_bootstrap_servers, sensor_port):
        self.central_ip = central_ip
        self.central_port = central_port
        print(f"Initializing Digital Engine...")
        
        self.db_connection = sqlite3.connect('central.db')  # Cambia esto si la base de datos está en otro lugar
        
        # Configurar Kafka producer
        self.kafka_producer = KafkaProducer(
            bootstrap_servers=kafka_bootstrap_servers,
            value_serializer=lambda v: json.dumps(v).encode('utf-8')
        )
        
        # Inicializar variables para configuraciones dependientes del taxi
        self.taxi_id = None
        self.kafka_consumer = None
        self.map_consumer = None

        self.sensor_port = sensor_port
        self.registry_url = self.read_registry_url
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
        self.volver_base = False
        self.key = None

    def read_registry_url(self):
        try:
            with open('registry_url.txt', 'r') as file:
                url = file.read().strip()
                if not url:
                    raise ValueError("El archivo de URL es incorrecto")
                return url
        except FileNotFoundError:
            print("Error: No se encontro el ficher")
        except Exception as e:
            print(f"Error leyendo el archivo de URL del Registry: {e}")


    def menu(self):
        while True:
            print("\n--- MENU ---")
            print("1. Autenticarse")
            print("2. Registrarse")
            print("3. Dar de baja")
            print("4. Salir")
            
            option = input("Elija una opción: ")
            
            if option == '1':  # Autenticarse
                self.authenticate()  # Llama a la función existente de autenticación
                break  # Salimos del menú si se autentica con éxito
            
            elif option == '2':  # Registrarse
                self.register_taxi()  # Llama a la función de registro

            elif option == '3':  # Registrarse
                self.unregister_taxi()  # Llama a la función de registro
            
            elif option == '4':  # Salir
                print("Saliendo del sistema...")
                exit(0)
            
            else:
                print("Opción inválida. Por favor, elija una opción válida.")


    def encrypt_message(self, message):
        """Encripta un mensaje usando la clave del taxi."""
        key = self.load_symmetric_key()  # Cargar la clave desde el archivo
        if not key:
            raise ValueError(f"No symmetric key found for taxi {self.taxi_id}")
        
        # Crear el cifrador AES en modo CBC con un IV generado automáticamente
        cipher = AES.new(key, AES.MODE_CBC)
        
        # Convertir el mensaje a bytes y agregar padding
        message_bytes = str(message).encode('utf-8')
        padded_message = pad(message_bytes, AES.block_size)
        
        # Cifrar el mensaje
        encrypted_message = cipher.encrypt(padded_message)
        
        # Retornar el IV y el mensaje cifrado codificados en Base64
        iv = base64.urlsafe_b64encode(cipher.iv).decode('utf-8')
        encrypted_message_base64 = base64.urlsafe_b64encode(encrypted_message).decode('utf-8')
        return iv, encrypted_message_base64

    
    
    def register_taxi(self):
        self.taxi_id = input("Ingresa la ID del taxi a registrar: ")
        if not self.taxi_id.strip():
            print("El ID del taxi no puede ser nulo, reintentando...")
            return

        try:
            registry_url = self.read_registry_url()  # Leer URL desde el archivo
            response = requests.post(
                f"{registry_url}/register_taxi",
                json={"id": self.taxi_id},
                verify="certificate.crt"  # Certificado del servidor para validarlo
            )
            if response.status_code == 201:
                print(f"Taxi {self.taxi_id} registrado")
            else:
                print(f"Error al registrar taxi {self.taxi_id}: {response.text}")
        except Exception as e:
            print(f"Error al registrar el taxi {self.taxi_id}: {str(e)}")


    def unregister_taxi(self):
        self.taxi_id = input("Ingrese la ID del taxi a dar de baja: ")
        if not self.taxi_id.strip():
            print("El campo ID no puede ser nulo. Intente nuevamente.")
            return

        try:
            registry_url = self.read_registry_url()  # Leer URL desde el archivo
            response = requests.delete(
                f"{registry_url}/delete_taxi",
                json={"id": self.taxi_id},
                verify="certificate.crt"  # Certificado del servidor para validarlo
            )
            if response.status_code == 200:
                print(f"Taxi {self.taxi_id} dado de baja exitosamente.")
                self.taxi_id = None  # Restablecer el ID del taxi
            else:
                print(f"Error al dar de baja el taxi {self.taxi_id}: {response.text}")
        except Exception as e:
            print(f"Error al dar de baja el taxi {self.taxi_id}: {str(e)}")


    
    def configure_kafka(self):
        self.kafka_consumer = KafkaConsumer(
            'taxi_instructions',
            bootstrap_servers=self.kafka_producer.config['bootstrap_servers'],
            value_deserializer=lambda v: json.loads(v.decode('utf-8')),
            group_id=f'taxi_group_{self.taxi_id}',
            auto_offset_reset='earliest',
            enable_auto_commit=True
        )
        self.map_consumer = KafkaConsumer(
            'map_updates',
            bootstrap_servers=self.kafka_producer.config['bootstrap_servers'],
            value_deserializer=lambda v: json.loads(v.decode('utf-8')),
            group_id=f'taxi_map_group_{self.taxi_id}',
            auto_offset_reset='earliest',
            enable_auto_commit=True,
            consumer_timeout_ms=1000
        )
        print(f"Kafka consumers configured for taxi ID: {self.taxi_id}")


    def save_symmetric_key(self, taxi_id, key):
        # Crear un archivo PEM específico para el taxi
        pem_path = f"{taxi_id}_key.pem"
        try:
            with open(pem_path, "wb") as pem_file:
                pem_file.write(
                    f"{key}".encode()
                )
            print(f"Symmetric key for taxi {taxi_id} saved securely to {pem_path}")
        except IOError as e:
            print(f"Error saving symmetric key for taxi {taxi_id}: {e}")
        finally:
            # Borrar la clave de la memoria después de guardarla
            key = None

    def authenticate(self):
        self.taxi_id = input("Enter Taxi ID: ").strip()
        if not self.taxi_id:
            print("Taxi ID no puede ser nulo, intente otra vez.")
            return

        try:
            # Enviar solicitud de autenticación
            response = requests.post(
                f"http://{self.central_ip}:{self.central_port}/authenticate",
                json={"taxi_id": self.taxi_id},
                timeout=10
            )

            if response.status_code == 200:
                data = response.json()
                self.token = data.get("token")
                symmetric_key = data.get("key")  # Cambiar a "key" para coincidir con la respuesta del servidor

                if self.token and symmetric_key:
                    print(f"Autenticado correctamente, su Token es: {self.token}")

                    # Guardar la clave y eliminarla de la memoria
                    self.save_symmetric_key(self.taxi_id, symmetric_key)
                    symmetric_key = None
                    return

            print(f"Autenticacion fallida: {response.json()}")  # Cambiar para mostrar mejor la respuesta
        except Exception as e:
            print(f"Error durante la autenticacion: {e}")


        
    def connect_to_central(self):
        try:
            # Verificar si el taxi está en la base de datos
            if self.is_taxi_registered():
                print(f"Taxi ID {self.taxi_id} no registrado, denegando conexion.")
                exit(1)  # Denegar conexión
            else:
                print("Verificado Correctamente")

            self.configure_kafka()

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
                
                with self.lock:
                    if status == 'KO':
                        self.status = 'KO'
                    elif status == 'OK':
                        # Solo cambiar a RUN si el DE no está en estado STOP
                        if self.status == 'END':
                            self.status = 'END' 
                        elif self.status != 'STOP' and self.status != 'BASE':
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
            if not self.token:
                print("No token available. Authentication required.")
                return

            # Preparar el mensaje de estado
            status_message = {
                'type': 'estado_taxi',
                'taxi_id': self.taxi_id
            }

            # Cifrar los campos sensibles
            try:
                iv_status, encrypted_status = self.encrypt_message(self.status)
                iv_position, encrypted_position = self.encrypt_message(self.position)
                iv_token, encrypted_token = self.encrypt_message(self.token)

                # Añadir campos cifrados al mensaje
                status_message.update({
                    'encrypted_status': encrypted_status,
                    'iv_status': iv_status,
                    'encrypted_position': encrypted_position,
                    'iv_position': iv_position,
                    'encrypted_token': encrypted_token,
                    'iv_token': iv_token
                })
            except Exception as e:
                print(f"Error encrypting status update fields: {e}")
                return

            # Enviar el mensaje cifrado al topic de Kafka
            self.kafka_producer.send('taxi_status', status_message)
            self.kafka_producer.flush()
            #print("Encrypted status update sent successfully.")

        except Exception as e:
            print(f"Error sending status update: {e}")

            
    def move_towards_destination(self):
        # Verificar si ya estamos en la posición de destino antes de iniciar el bucle
        if self.position == self.destination:
            self._set_next_destination()
            return  # Salir de la función si ya estamos en el destino

        # Iniciar el bucle de movimiento
        while self.running:
            with self.lock:
                # Solo mover si hay un destino y el estado es 'RUN'
                if self.destination and self.status in ['RUN','BASE'] and self.sensor_connected:
                    self._move_towards(self.destination)

                    # Verificar nuevamente si llegó al destino
                    if self.position == self.destination:
                        print(f"Reached destination: {self.destination}")
                        self._set_next_destination()

            time.sleep(1)  # Controlar la velocidad del movimiento

    def _move_towards(self, target):
        """ Mueve la posición actual hacia el objetivo dado. """

        current_pos = self.position.copy()

        # Mover hacia el destino
        if current_pos[0] < target[0]:
            current_pos[0] += 1
        elif current_pos[0] > target[0]:
            current_pos[0] -= 1

        if current_pos[1] < target[1]:
            current_pos[1] += 1
        elif current_pos[1] > target[1]:
            current_pos[1] -= 1

        # Actualizar posición si se movió
        if current_pos != self.position:
            self.position = current_pos
            self.send_status_update()
            print(f"Moved to position: {self.position}")


    def _set_next_destination(self):
        
        """ Establece el siguiente destino si existe. """

        print(f"Volver a base = {self.volver_base}")
        print(f"Siguente destino = {self.next_destination}")
        if self.volver_base == True and self.next_destination == None:
            self.destination = None
            self.status = 'END'
            self.send_status_update()
            
            self.destination = [1, 1]
            self.status = 'BASE'
            print("Returning to base")
            self.send_status_update()
            
        
        elif self.next_destination:
            if isinstance(self.next_destination, str):
                self.destination = None
                self.status = 'END'
                self.send_status_update()
                self.next_destination = None
                return
            
            self.destination = self.next_destination
            self.next_destination = None
            self.send_status_update()
        
        else:
            self.destination = None
            self.status = 'END'
            self.next_destination = None
            self.send_status_update()

    def load_symmetric_key(self):
        """Carga la clave simétrica desde el archivo PEM y la devuelve."""
        pem_path = f"{self.taxi_id}_key.pem"
        try:
            with open(pem_path, "rb") as pem_file:
                key = pem_file.read().decode('utf-8')
                return base64.urlsafe_b64decode(key)  # Decodificar y retornar la clave
        except IOError as e:
            print(f"Error loading symmetric key for taxi {self.taxi_id}: {e}")
            return None  # Retornar None si ocurre un error


    def decrypt_message(self, iv, encrypted_message):
        """Desencripta el mensaje usando AES en modo CBC."""
        key = self.load_symmetric_key()
        if key is None:
            print("Error: Symmetric key not loaded.")
            return None

        try:
            cipher = AES.new(key, AES.MODE_CBC, iv=base64.urlsafe_b64decode(iv))
            encrypted_message_bytes = base64.urlsafe_b64decode(encrypted_message)
            decrypted_message = unpad(cipher.decrypt(encrypted_message_bytes), AES.block_size)
            return decrypted_message.decode('utf-8')
        except (ValueError, KeyError) as e:
            print(f"Error during decryption: {e}")
            return None

        
    def process_instructions(self):
        """Procesa las instrucciones recibidas por el Kafka consumer."""
        print("Starting to process instructions...")
        try:
            for message in self.kafka_consumer:
                instruction = message.value
                
                if instruction.get('taxi_id') == self.taxi_id:
                    print(f"Processing instruction for digital engine {self.taxi_id}: {instruction}")

                    with self.lock:
                        if instruction.get('type') == 'move_instruction':
                            if self.status != 'KO':
                                # Validar si los valores cifrados están presentes
                                iv_pickup = instruction.get('iv_pickup')
                                encrypted_pickup = instruction.get('encrypted_pickup')
                                iv_destination = instruction.get('iv_destination')
                                encrypted_destination = instruction.get('encrypted_destination')

                                if iv_pickup and encrypted_pickup and iv_destination and encrypted_destination:
                                    # Desencriptar ubicaciones
                                    try:
                                        pickup = self.decrypt_message(iv_pickup, encrypted_pickup)
                                        destination = self.decrypt_message(iv_destination, encrypted_destination)
                                        #print(f"DEBUG: Decrypted pickup: {pickup}")
                                        #print(f"DEBUG: Decrypted destination: {destination}")

                                        # Validar JSON
                                        pickup = json.loads(pickup)
                                        #destination = json.loads(destination)
                                        #print(f"DEBUG: Parsed pickup: {pickup}, Parsed destination: {destination}")

                                        self.destination = pickup
                                        self.next_destination = destination if 'next_destination' in instruction else None
                                        self.status = 'RUN'
                                        self.send_status_update()
                                        #print(f"DEBUG: Moving to destination: {self.destination}")
                                    except json.JSONDecodeError as e:
                                        print(f"Error decoding pickup or destination: {e}")
                                    except Exception as e:
                                        print(f"Error decrypting pickup or destination: {e}")
                                else:
                                    # Manejar formato antiguo (sin cifrado)
                                    if 'destination' in instruction:
                                        self.destination = instruction['destination']
                                        self.next_destination = instruction.get('next_destination', None)
                                        self.status = 'RUN'
                                        self.send_status_update()
                                        #print(f"DEBUG: Moving to destination: {self.destination}")
                                    else:
                                        print(f"Invalid instruction format: {instruction}")


                        elif instruction.get('command'):
                            iv_command = instruction.get('iv')
                            encrypted_command = instruction.get('command')

                            if iv_command is None or encrypted_command is None:
                                print(f"Missing 'iv' or 'command': {instruction}")
                                continue

                            try:
                                command = self.decrypt_message(iv_command, encrypted_command)
                                #print(f"DEBUG: Decrypted command: {command}")

                                if command:
                                    self.handle_command(command, instruction.get('payload'))
                                else:
                                    print("Error decrypting command")
                            except Exception as e:
                                print(f"Error decrypting command: {e}")
                                print(f"Instruction: {instruction}")

                        else:
                            print(f"Unknown instruction type or missing command: {instruction}")

        except Exception as e:
            print(f"Error processing instructions: {e}")
            print(traceback.format_exc())

    def reset_and_restart(self):

        # Reset key attributes
        self.taxi_id = None
        self.position = [1, 1]
        self.destination = None
        self.status = "KO"
        self.taxi_passengers = {}
        self.token = None
        self.volver_base = False

        # Restart the authentication menu
        print("Disconnected. Returning to authentication menu...")
        self.menu()
    
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
            self.volver_base = True
            
            if self.status == 'END':
                print("Returning to base")
                self._set_next_destination()
            
            else:    
                print("Returning to base after this service")
            
        elif command == 'disconnect':
            print(f"Taxi {self.taxi_id} is disconnecting...")
            self.status = 'DISCONNECTED'
        
        elif command == 'desconectar':
            self.reset_and_restart()

        self.send_status_update()
        
    def reset_and_restart(self):
        # Preguntar al usuario si desea volver a autenticarse
        while True:
            respuesta = input("Se ha desconectado. ¿Desea volver a autenticarse? (Y/N): ").strip().lower()
            if respuesta == 'y':
                # Resetear atributos clave
                self.taxi_id = None
                self.position = [1, 1]
                self.destination = None
                self.status = "KO"
                self.taxi_passengers = {}
                self.token = None
                self.volver_base = False

                print("Reiniciando y volviendo al menú de autenticación...")
                self.menu()
                break  # Salir del bucle una vez que se maneje la respuesta
            elif respuesta == 'n':
                print("Desconectando...")
                self.running = False  # Detener el bucle principal
                self.terminate_threads()  # Finalizar todos los threads
                exit(0)
            else:
                print("Respuesta inválida. Por favor, ingrese 'Y' o 'N'.")

    def terminate_threads(self):
        # Finalizar todos los threads activos
        print("Finalizando todos los threads...")
        self.running = False  # Señal para detener todos los hilos
        if self.sensor_socket:
            self.sensor_socket.close()  # Cerrar el socket del sensor si está abierto
        if self.kafka_consumer:
            self.kafka_consumer.close()
        if self.map_consumer:
            self.map_consumer.close()
        pygame.quit()  # Finalizar la ventana de Pygame si está activa
        print("Todos los recursos han sido liberados correctamente.")

                
    def wait_for_initial_map(self):
        print("Waiting for initial map update...")
        max_retries = 30
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
                
            with self.lock:
                new_customers = update.get('customers', {})
                # Validar formato de clientes
                valid_customers = {}
                for customer_id, pos in new_customers.items():
                    if isinstance(pos, list) and len(pos) == 2:
                        valid_customers[customer_id] = tuple(pos)
                
                self.customers = valid_customers
                self.map = update.get('map', self.map)
                self.taxis = update.get('taxis', {})
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
                        if taxi_info.get('status') in ['KO', 'END', 'STOP', 'BASE']:
                            pygame.draw.rect(screen, RED, rect_interior)
                        elif taxi_info.get('status') in ['DISCONNECTED']:
                            pygame.draw.rect(screen, BLACK, rect_interior)
                        else:
                            pygame.draw.rect(screen, GREEN, rect_interior)
                        
                        # ID del taxi
                        display_id = str(taxi_id)
                        if taxi_id in self.taxi_passengers:
                            display_id = f"{taxi_id}{self.taxi_passengers[taxi_id]}"
                        text = font.render(display_id, True, WHITE)
                        text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                        screen.blit(text, text_rect)
                
                for customer_id, pos in self.customers.items():
                    if isinstance(pos, tuple) and len(pos) == 2:
                        x, y = pos
                        rect = pygame.Rect(x * cell_size, y * cell_size, cell_size, cell_size)
                        rect_interior = rect.inflate(-2, -2)
                        pygame.draw.rect(screen, YELLOW, rect_interior)
                        text = font.render(str(customer_id), True, BLACK)
                        text_rect = text.get_rect(center=(rect.centerx, rect.centery))
                        screen.blit(text, text_rect)
                    else:
                        print(f"Skipping customer {customer_id} with invalid position: {pos}")


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
            self.menu()
            self.configure_kafka()
            
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
    args = parser.parse_args()

    digital_engine = DigitalEngine(
        args.central_ip,
        args.central_port,
        args.kafka_bootstrap_servers,
        args.sensor_port
    )
    digital_engine.run()