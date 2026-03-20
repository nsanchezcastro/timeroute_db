# 🕒 TimeRoute - Sistema de Gestión de Visitas Domiciliarias

TimeRoute es una solución backend robusta diseñada para gestionar la asistencia de trabajadores de campo. A diferencia de un sistema de fichaje estático, TimeRoute utiliza geolocalización dinámica para validar que el trabajador se encuentra en la ubicación del paciente antes de permitir el inicio de la jornada.

## Funcionalidades Principales

* **Validación GPS:** El sistema calcula la distancia real entre el móvil y el paciente. Solo permite fichar si el trabajador está a menos de 200 metros.
* **Gestión de Hojas de Ruta:** Los trabajadores reciben una lista ordenada de pacientes asignados para el día.
* **Cálculo Automático de Tiempos:** Registra el tiempo exacto (en minutos) dedicado a cada intervención.
* **Panel de Administración:** Endpoints dedicados para crear pacientes y asignar rutas diarias.

## 🛠️ Instalación y Configuración

1.  **Base de Datos:**
    * Crea una base de datos en MySQL llamada `timeroute_db`.
    * Importa el archivo `database.sql` ubicado en la raíz del proyecto.
    * *Nota: El SQL incluye un usuario administrador (`admin@timeroute.com`) y un trabajador (`juan@timeroute.com`) para pruebas.*

2.  **Conexión PHP:**
    * Configura tus credenciales en `backend/config/db.php`.

## 📡 Guía de la API (para Frontend)

### 👥 Usuarios y Rutas
* **Login:** `POST /api/login.php` (Retorna `id_usuario` y `rol`).
* **Obtener Ruta:** `GET /api/obtener_ruta.php?id={id_usuario}` (Retorna lista de pacientes del día).

### 📍 Gestión de Visitas
* **Iniciar Visita:** `POST /api/fichar.php`
    * Payload: `{"id_asignacion": X, "latitud": X, "longitud": X}`
* **Finalizar Visita:** `POST /api/fichar_fin.php`
    * Payload: `{"id_asignacion": X}`

### 🔑 Administración
* **Crear Paciente:** `POST /admin/crear_paciente.php`
* **Asignar Ruta:** `POST /admin/asignar_ruta.php`

## 📁 Estructura del Proyecto

```text
backend/
├── admin/          # Gestión para el administrador
├── api/            # Endpoints para la App móvil (Angular)
├── config/         # Conexión a DB
├── src/
│   └── Models/     # Lógica de negocio (GestionVisitas.php)
└── database.sql    # Script de creación de tablas