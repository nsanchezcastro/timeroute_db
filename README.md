Timeroute API - Gestión de Rutas y Fichaje Geolocalizado
Este es el Backend del proyecto Timeroute, una solución integral para la gestión de jornadas laborales de operadores de ruta. La API permite el seguimiento en tiempo real, control de pausas e inicios/cierres de jornada con validación de ubicación GPS.

-Características Principales:
Autenticación: Sistema de login seguro con password_hash y verificación de roles (Admin/Trabajador).

Gestión de Jornadas: Endpoint dinámico para obtener la "Hoja de Ruta" diaria del trabajador.

Fichaje Inteligente: Registro de llegada y salida de visitas con validación de distancia (Haversine) de un máximo de 200 metros respecto al cliente.

Control de Tiempos: Cálculo automático de minutos dedicados por visita y gestión de pausas activas.

Arquitectura: PHP nativo bajo patrón de modelos, siguiendo principios REST.

Módulo de Reporting: Capacidad de generar historiales diarios agregados por fecha para supervisión administrativa.

-Stack Tecnológico:
Lenguaje: PHP 8.x

Base de Datos: MySQL (Base de datos unificada: timeroute)

Formato de Intercambio: JSON

Seguridad: CORS integrado para consumo desde aplicaciones Angular.

-Estructura del Proyecto:

├── api/
│   ├── auth/                # Login y gestión de sesiones
│   ├── mis-jornadas/        # Endpoint principal de la hoja de ruta
│   ├── jornadas/            # Iniciar, finalizar e incidencias
│   ├── pausas/              # Control de descansos
│   └── visitas/             # Fichaje de llegada/salida (lat/lng)
├── config/
│   ├── db.php               # Conexión PDO
│   └── cors.php             # Configuración de cabeceras HTTP
└── src/
    └── Models/              # Lógica de negocio (GestionVisitas.php, Usuario.php, Informe.php)

-Requisitos de la Base de Datos:
El sistema utiliza el esquema unificado timeroute. Asegúrate de tener las siguientes tablas clave:

usuarios: Credenciales y roles.

jornadas: Cabecera de la ruta diaria.

rutas: Nombres y definiciones de trayectos.

clientes: Datos maestros y geolocalización (lat, lng).

visitas: Detalle de paradas y tiempos reales.

-Instalación y Uso:
Clona el repositorio en tu servidor local (XAMPP/Laragon).

Importa el script SQL de la base de datos timeroute.

Configura tus credenciales en config/db.php.

Apunta tu Frontend de Angular a la URL base de esta API.

-Nota para los colaboradores:
Para probar el sistema de fichaje, asegúrate de enviar las coordenadas del dispositivo en el cuerpo del JSON (lat, lng). El sistema devolverá un error 400 si el usuario se encuentra a más de 200 metros del destino.