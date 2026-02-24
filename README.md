# TimeRoute - Backend

Este repositorio contiene la lógica de servidor y la base de datos para la aplicación TimeRoute.

## Tecnologías
* **PHP 8.x** (Estructura orientada a objetos)
* **MySQL** (Base de datos relacional)
* **PDO** (Para conexiones seguras)

## Funcionalidades implementadas
- [x] Conexión a DB centralizada.
- [x] Autenticación de usuarios con **password_hash** (Bcrypt).
- [x] Registro de inicio de jornada con coordenadas GPS.
- [x] Registro de fin de jornada y cálculo automático de horas.

## Instalación
1. Clonar el repositorio.
2. Importar el archivo `database.sql` en phpMyAdmin.
3. Configurar credenciales en `config/db.php`.

## Guía para mis compañeros 

Si acabas de clonar el repositorio o necesitas actualizar tu base de datos local, sigue estos pasos:

1. **Actualizar código local:**
   ```bash
   git pull origin main
   
2. **Configurar la Base de Datos:**

  Abre phpMyAdmin en tu navegador.
  Crea una nueva base de datos llamada timeroute_db.
  Selecciona la base de datos recién creada.
  Ve a la pestaña Importar.
  Selecciona el archivo database.sql que se encuentra en la raíz de este proyecto.
  Haz clic en Importar al final de la página.

3. **Configuración de conexión:**

  Asegúrate de que tu archivo config/db.php tiene las credenciales correctas de tu entorno local (usuario y contraseña de MySQL).
