import socket
import time
import argparse
import threading
from functools import reduce
import keyboard


class Sensors:
    def __init__(self, digital_engine_ip, digital_engine_port):
        self.digital_engine_ip = digital_engine_ip
        self.digital_engine_port = digital_engine_port
        self.socket = None
        self.running = threading.Event()
        self.running.set()  # Activa el evento para iniciar el ciclo
        self.incident = False
        self.connected = False  # Estado de conexión con DE

    def connect_to_digital_engine(self):
        while not self.connected:
            try:
                self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                self.socket.connect((self.digital_engine_ip, self.digital_engine_port))
                self.connected = True
                print(f"Connected to Digital Engine at {self.digital_engine_ip}:{self.digital_engine_port}")
            except Exception as e:
                print(f"Failed to connect to Digital Engine: {e}. Retrying in 3 seconds...")
                time.sleep(3)  # Reintentar conexión después de 3 segundos

    def send_data(self):
        while self.running.is_set():
            if self.connected:
                status = "KO" if self.incident else "OK"
                message = f"<STX>{status}<ETX>"
                lrc = self.calculate_lrc(message)
                full_message = f"{message}{lrc}"

                try:
                    self.socket.sendall(full_message.encode())
                    print(f"Sent: {full_message}")
                except Exception as e:
                    print(f"Error sending data: {e}")
                    print("Lost connection to Digital Engine. Going to standby mode.")
                    self.connected = False
                    self.socket.close()  # Cerrar el socket
                    self.connect_to_digital_engine()  # Reintentar conexión

            time.sleep(1)  # Enviar mensajes cada segundo

    def calculate_lrc(self, message):
        return chr(reduce(lambda x, y: x ^ ord(y), message, 0))

    def toggle_incident(self):
        while self.running.is_set():
            if keyboard.is_pressed('i'):
                self.incident = True  # Enviar KO
                print("Incident activated: Sending KO")
                time.sleep(0.5)  # Evitar múltiples activaciones rápidas
            elif keyboard.is_pressed('q'):
                self.incident = False  # Enviar OK
                print("Incident deactivated: Sending OK")
                time.sleep(0.5)

    def run(self):
        # Conectar al Digital Engine antes de iniciar el envío de mensajes
        self.connect_to_digital_engine()

        send_thread = threading.Thread(target=self.send_data)
        input_thread = threading.Thread(target=self.toggle_incident)

        send_thread.start()
        input_thread.start()

        send_thread.join()
        input_thread.join()

        if self.socket:
            self.socket.close()
        print("Sensors stopped.")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="EC_S: Taxi Sensors Simulator")
    parser.add_argument("digital_engine_ip", help="IP address of the Digital Engine")
    parser.add_argument("digital_engine_port", type=int, help="Port of the Digital Engine")
    args = parser.parse_args()

    sensors = Sensors(args.digital_engine_ip, args.digital_engine_port)
    sensors.run()
