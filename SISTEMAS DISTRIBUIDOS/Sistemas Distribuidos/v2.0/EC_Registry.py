from flask import Flask, request, jsonify, render_template
import sqlite3
import ssl

app = Flask(__name__)

DATABASE = "central.db"  # Ruta de la base de datos SQLite

# Función auxiliar para conectar con la base de datos
def connect_db():
    conn = sqlite3.connect(DATABASE)
    conn.row_factory = sqlite3.Row  # Devuelve resultados como diccionarios
    return conn

# Ruta para la página principal (front)
@app.route('/')
def home():
    return '''
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EC Registry</title>
        <style>
            /* Global Styles */
            body {
                font-family: 'Roboto', sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                color: #333;
            }

            h1 {
                font-size: 3.5em;
                color: #0d47a1;
                text-align: center;
                margin-bottom: 50px;
                text-transform: uppercase;
                font-weight: bold;
                letter-spacing: 2px;
            }

            /* Main container */
            .container {
                width: 100%;
                max-width: 750px;
                padding: 35px;
                background-color: #ffffff;
                border-radius: 20px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                border: 2px solid #e0e0e0;
            }

            .card {
                margin-bottom: 30px;
                padding: 30px;
                background-color: #fafafa;
                border-radius: 12px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                border-left: 5px solid #0d47a1;
                transition: all 0.4s ease;
            }

            .card:hover {
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                transform: translateY(-8px);
            }

            .card-header {
                font-size: 1.5em;
                font-weight: bold;
                color: #0d47a1;
                margin-bottom: 20px;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
                letter-spacing: 1px;
            }

            .form-label {
                font-size: 1.2em;
                margin-bottom: 12px;
                color: #555;
                font-weight: 500;
            }

            .form-control {
                width: 100%;
                padding: 16px;
                font-size: 1.1em;
                border: 2px solid #0d47a1;
                border-radius: 10px;
                margin-bottom: 20px;
                background-color: #f9f9f9;
                transition: all 0.3s ease-in-out;
                box-sizing: border-box;
            }

            .form-control:focus {
                border-color: #2196f3;
                background-color: #fff;
                outline: none;
                box-shadow: 0 0 8px rgba(33, 150, 243, 0.4);
            }

            .btn {
                width: 100%;
                padding: 14px;
                font-size: 1.2em;
                text-align: center;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                color: white;
                font-weight: 600;
            }

            .btn-success {
                background-color: #4caf50;
            }

            .btn-danger {
                background-color: #e53935;
            }

            .btn:hover {
                opacity: 0.9;
                transform: scale(1.05);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            /* Alert styles */
            .alert {
                padding: 18px;
                margin-top: 25px;
                background-color: #ffcc80;
                color: #d32f2f;
                border-radius: 12px;
                text-align: center;
                font-size: 1.2em;
                border-left: 6px solid #d32f2f;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease;
            }

            .alert:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Additional styling for input fields and buttons */
            .form-control:focus {
                border-color: #0288d1;
                box-shadow: 0 0 12px rgba(33, 150, 243, 0.4);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .container {
                    padding: 25px;
                }
                .card-header {
                    font-size: 1.4em;
                }
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate__animated {
                animation-duration: 1s;
                animation-fill-mode: both;
            }

            .animate__fadeInUp {
                animation-name: fadeInUp;
            }
        </style>
    </head>
    <body>
        <div class="container animate__animated animate__fadeInUp">
            <h1>EC Registry</h1>

            <div class="card">
                <div class="card-header">Registrar Taxi</div>
                <form action="/register_taxi" method="post">
                    <div class="mb-3">
                        <label for="taxi_id" class="form-label">Taxi ID</label>
                        <input type="text" id="taxi_id" name="taxi_id" class="form-control" placeholder="Enter Taxi ID" required>
                    </div>
                    <button type="submit" class="btn btn-success">Registrar Taxi</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header">Dar de Baja Taxi</div>
                <form action="/delete_taxi" method="post">
                    <div class="mb-3">
                        <label for="taxi_id" class="form-label">Taxi ID</label>
                        <input type="text" id="taxi_id" name="taxi_id" class="form-control" placeholder="Enter Taxi ID" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Dar de Baja</button>
                </form>
            </div>
        </div>
    </body>
    </html>
    '''

# Ruta para registrar un taxi desde el formulario o JSON
@app.route('/register_taxi', methods=['POST'])
def register_taxi():
    taxi_id = request.form.get('taxi_id') or request.get_json().get('id')
    if not taxi_id:
        return jsonify({"error": "Taxi ID is required"}), 400

    try:
        conn = connect_db()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM taxis WHERE id = ?", (taxi_id,))
        if cursor.fetchone():
            return jsonify({"error": "Taxi already registered"}), 400
        cursor.execute("INSERT INTO taxis (id, status, position_x, position_y) VALUES (?, 'IDLE', 0, 0)", (taxi_id,))
        conn.commit()
        return jsonify({"message": f"Taxi {taxi_id} registered successfully"}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

# Ruta para dar de baja un taxi desde el formulario o JSON
@app.route('/delete_taxi', methods=['POST','DELETE'])
def delete_taxi():
    taxi_id = request.form.get('taxi_id') or request.get_json().get('id')
    if not taxi_id:
        return jsonify({"error": "Taxi ID is required"}), 400

    try:
        conn = connect_db()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM taxis WHERE id = ?", (taxi_id,))
        if not cursor.fetchone():
            return jsonify({"error": "Taxi not found"}), 404
        cursor.execute("DELETE FROM taxis WHERE id = ?", (taxi_id,))
        conn.commit()
        return jsonify({"message": f"Taxi {taxi_id} deleted successfully"}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

if __name__ == "__main__":
    #certificicado autofirmado
    context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
    context.load_cert_chain(certfile='ec_registry.crt', keyfile='ec_registry.key')
    app.run(host='0.0.0.0', port=5001, ssl_context=context)
