from flask import Flask, jsonify, send_from_directory
from flask_cors import CORS
import os
import json
import threading
import time

app = Flask(__name__)
CORS(app)

port = 4000
cached_data = None  # Variable para almacenar los datos en memoria
file_path = os.path.join(os.path.dirname(__file__), 'mapa.json')

def update_cached_data():
    global cached_data
    while True:
        try:
            with open(file_path, 'r', encoding='utf-8') as file:
                cached_data = json.load(file)  # Leer y parsear el archivo JSON
        except Exception as e:
            print(f"Error con JSON: {e}")
            cached_data = None
        time.sleep(0.4)  # Intervalo de actualización

@app.route('/')
def index():
    return "API_Central"

@app.route('/map', methods=['GET'])
def get_map():
    if cached_data:
        return jsonify(cached_data)
    else:
        return jsonify({"error": "Datos no disponibles"}), 503

@app.route('/customers', methods=['GET'])
def get_customers():
    if cached_data and 'customers' in cached_data:
        return jsonify(cached_data['customers'])
    else:
        return jsonify({"error": "Customer data not available"}), 503
    
@app.route('/taxis', methods=['GET'])
def get_taxis():
    if cached_data and 'taxis' in cached_data:
        return jsonify(cached_data['taxis'])
    else:
        return jsonify({"error": "Taxi data not available"}), 503

if __name__ == '__main__':
    threading.Thread(target=update_cached_data, daemon=True).start()
    app.run(host='0.0.0.0', port=port)

