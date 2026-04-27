# Wally Street - API de Simulación Financiera

Este proyecto es una API REST desarrollada para  **Seminario de Lenguajes (PHP)** de la Facultad de Informática - UNLP. La aplicación permite la gestión de activos financieros, registro de usuarios y simulación de trading en tiempo real.

## Tecnologías Utilizadas

* **PHP 8.x**: Lenguaje core del proyecto.
* **Slim Framework 4**: Micro-framework para el manejo de rutas y peticiones HTTP.
* **Eloquent ORM (Illuminate/Database)**: Para el mapeo de objetos a la base de datos MySQL.
* **Composer**: Gestor de dependencias de PHP.
* **MySQL**: Motor de base de datos relacional.
* **XAMPP**: Entorno de desarrollo local (Apache/MySQL).
* **Postman**: Herramienta para el testeo de los endpoints.

## 📂 Estructura del Proyecto

Siguiendo el patrón de diseño **MVC (Modelo-Vista-Controlador)** y las recomendaciones de la cátedra:

```text
wally-street/
├── config/             # Configuración de base de datos y credenciales
├── public/             # Único punto de entrada (index.php) y .htaccess
├── routes/             # Definición de rutas (api.php)
├── src/                # Lógica de la aplicación
│   ├── Controllers/    # Manejo de la lógica de las peticiones
│   ├── Models/         # Representación de las tablas de la DB
│   └── Middleware/     # Filtros de seguridad (Próximamente JWT)
├── vendor/             # Dependencias instaladas por Composer
└── composer.json       # Definición de paquetes y autoloading