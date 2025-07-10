from flask import Flask, jsonify, request, render_template_string
import requests
import threading

class CityTrafficControl:
    def __init__(self, api_key_file, default_city="Alicante"):
        self.api_key_file = api_key_file
        self.default_city = default_city
        self.lock = threading.Lock()  # Protege los datos compartidos

    def read_api_key(self):
        try:
            with open(self.api_key_file, 'r') as file:
                api_key = file.read().strip()
                if not api_key:
                    raise ValueError("El archivo de API Key es incorrecto.")
                return api_key
        except FileNotFoundError:
            print("Error: El archivo de API Key no esta disponible")
            return None
        except Exception as e:
            print(f"Error al leer archivo de API Key: {e}")
            return None

    def get_weather(self, city):
        api_key = self.read_api_key()
        if not api_key:
            return None, "No se pudo leer la API Key."

        url = f"http://api.openweathermap.org/data/2.5/weather?q={city}&appid={api_key}&units=metric"
        try:
            response = requests.get(url)
            if response.status_code == 200:
                data = response.json()
                temperature = data['main']['temp']
                return temperature, None
            elif response.status_code == 401:  # API Key inválida
                return None, "API Key inválida."
            else:
                return None, f"Error al obtener los datos del clima: {response.status_code}"
        except Exception as e:
            return None, f"Error: {e}"

# Flask API para EC_CTC
app = Flask(__name__)
traffic_control = CityTrafficControl(api_key_file="api_key.txt")

# Página principal con formulario
@app.route('/')
def home():
    html = '''
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>City Traffic Control</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f7f9fc;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .card {
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                width: 400px;
                text-align: center;
            }
            .card h3 {
                margin-bottom: 20px;
                font-size: 1.5em;
                color: #333;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group input {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 1em;
            }
            .form-group button {
                width: 100%;
                padding: 10px;
                background-color: #007bff;
                border: none;
                border-radius: 5px;
                color: white;
                font-size: 1em;
                cursor: pointer;
            }
            .form-group button:hover {
                background-color: #0056b3;
            }
            .alert {
                margin-top: 20px;
                padding: 10px;
                border: 1px solid #d4d4d4;
                background-color: #f8d7da;
                color: #721c24;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h3>Change City</h3>
            <form action="/change_city" method="post" class="form-group">
                <input type="text" id="city" name="city" placeholder="Enter city name" required>
                <button type="submit">Submit</button>
            </form>
        </div>
    </body>
    </html>
    '''
    return render_template_string(html)

@app.route('/traffic_status', methods=['GET'])
def traffic_status():
    """Consulta en tiempo real el clima y proporciona el estado del tráfico."""
    city = request.args.get('city', traffic_control.default_city)
    with traffic_control.lock:
        temperature, error = traffic_control.get_weather(city)
        if temperature is not None:
            status = "OK" if temperature >= 0 else "KO"
            return jsonify({
                "city": city,
                "temperature": temperature,
                "status": status
            })
        else:
            return jsonify({
                "city": city,
                "temperature": "N/A",
                "status": "UNKNOWN",
                "error": error
            }), 500

@app.route('/change_city', methods=['POST'])
def change_city():
    """Permite cambiar la ciudad desde la que se consulta el clima."""
    new_city = request.form.get('city')
    if new_city:
        with traffic_control.lock:
            traffic_control.default_city = new_city
        return f'''
        <div style="text-align: center; margin-top: 20px;">
            <h1 style="color: green;">City changed to {new_city}</h1>
            <a href="/" style="text-decoration: none; color: blue;">Go back</a>
        </div>
        ''', 200
    else:
        return '''
        <div style="text-align: center; margin-top: 20px;">
            <h1 style="color: red;">Error: No city provided</h1>
            <a href="/" style="text-decoration: none; color: blue;">Go back</a>
        </div>
        ''', 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)

