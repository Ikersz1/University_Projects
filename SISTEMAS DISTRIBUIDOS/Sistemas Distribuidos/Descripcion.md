# 🚕 EasyCab (2024/2025)

**EasyCab** es un proyecto académico dividido en dos versiones, cuyo objetivo es desarrollar un sistema distribuido para la gestión de una flota de taxis autónomos en una ciudad simulada.

## 🧩 Versión 1
En la primera versión se implementan los fundamentos del sistema utilizando:
- Sockets TCP
- Colas de eventos (Kafka)
- Diseño modular distribuido

Incluye componentes como:
- Clientes que solicitan taxis
- Taxis autónomos con sensores simulados
- Una central de control que gestiona y coordina los servicios

## 🔐 Versión 2
La segunda versión amplía la funcionalidad incorporando:
- Servicios RESTful
- Mecanismos de seguridad:
  - Autenticación segura
  - Cifrado bidireccional basado en AES
  - Registro de auditoría
- Integración con APIs externas como **OpenWeather**
- Un **frontend web** para visualizar en tiempo real el estado del sistema (taxis, usuarios, tráfico)

## 🛠 Tecnologías utilizadas
- **Lenguaje principal:** Python
- **Mensajería y eventos:** Apache Kafka
- **Cifrado:** AES (Advanced Encryption Standard)
- **API REST**
- **Sockets**
- **Frontend web público**

---

Este proyecto fue desarrollado como parte de la asignatura de *Sistemas Distribuidos* en el curso 2024/2025.
