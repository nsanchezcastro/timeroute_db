# Timeroute 
### Gestión de Rutas y Fichaje Geolocalizado

Este es el **Backend** del proyecto Timeroute, una solución integral para la gestión de jornadas laborales de operadores de ruta. La API permite el seguimiento en tiempo real, control de pausas e inicios/cierres de jornada con validación de ubicación GPS.

---

## Características Principales

* **Autenticación:** Login seguro con `password_verify` y gestión de roles.
* **Gestión de Jornadas:** Endpoint dinámico para obtener la "Hoja de Ruta" diaria.
* **Fichaje Inteligente:** Registro de llegada/salida con **validación de distancia (Haversine)** de un máximo de 200 metros.
* **Módulo de Reporting:** Generación de historiales diarios para supervisión administrativa.

---

## Stack Tecnológico

* **Lenguaje:** PHP 8.x (Nativo)
* **Base de Datos:** MySQL (Esquema unificado `timeroute`)
* **Seguridad:** CORS configurado para Angular y cifrado de contraseñas.

---

## Estructura del Proyecto

```text
├── admin/               # Generación de informes (Admin)
├── api/
│   ├── auth/            # Login y sesiones
│   ├── mis-jornadas/    # Endpoint principal (Hoja de Ruta)
│   ├── jornadas/        # Iniciar/Finalizar jornada
│   └── visitas/         # Fichaje de llegada/salida (lat/lng)
├── config/              # db.php y cors.php
└── src/
    └── Models/          # Informe.php, GestionVisitas.php, Usuario.php

## Endpoints Destacados

1. Historial Diario (Admin)
URL: /admin/obtener_informes.php?fecha=YYYY-MM-DD

Devuelve un desglose detallado de quién trabajó, qué clientes visitó y cuánto tiempo dedicó.

2. Hoja de Ruta (Trabajador)
URL: /api/mis-jornadas/index.php?id_usuario=ID

Muestra la jornada activa de hoy, incluyendo paradas ordenadas y coordenadas.

## Instalación Rápida
Clona el repositorio en tu servidor local.

Importa la base de datos timeroute.

Configura config/db.php con tus credenciales.

¡Listo! El Backend responderá en formato JSON.