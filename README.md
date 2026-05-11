# Wally Street - API de Simulación Financiera

Este proyecto es una API REST desarrollada para  **Seminario de Lenguajes (PHP)** de la Facultad de Informática - UNLP. La aplicación permite la gestión de activos financieros, registro de usuarios y simulación de trading en tiempo real.

## Tecnologías Utilizadas

### Backend
* **PHP 8.x**: Lenguaje principal del lado del servidor.
* **Slim Framework 4**: Micro-framework para la creación de APIs REST.
* **Composer**: Gestor de dependencias (encargado del Autoload PSR-4).
* **PDO (PHP Data Objects)**: Interfaz para una conexión segura y eficiente con la base de datos.

### Base de Datos
* **MySQL**: Sistema de gestión de bases de datos relacionales.
* **XAMPP**: Entorno de desarrollo local (servidor Apache + MariaDB/MySQL).

### Herramientas y Estándares
* **Postman**: Suite de herramientas para el testeo y documentación de los endpoints.
* **Arquitectura MVC**: Organización del código separando Modelos, Vistas y Controladores.
* **PSR-7 / PSR-15**: Estándares de interfaces HTTP y Middlewares.
* **JSON**: Formato de intercambio de datos entre cliente y servidor.



## Características
* **Autenticación:** Sistema de seguridad basado en **Bearer Tokens** con expiración de 5 minutos.
* **Mercado Dinámico:** Algoritmo de fluctuación de precios (Admin).
* **Persistencia:** Base de datos MySQL mediante PDO (Singleton).
* **CORS:** Configurado para integración con frontend en React.

---

## 📁 Estructura del Proyecto

```text
wally-street/
├── public/                 # Punto de entrada (index.php) y .htaccess
├── routes/                 # Definición de rutas (api.php)
├── src/
│   ├── Controllers/        # Lógica de negocio (User, Asset, Trade, Portfolio)
│   ├── Middleware/         # Filtros de seguridad (AuthMiddleware)
│   └── Models/             # Conexión DB y Clases (User, Asset, DB)
├── composer.json           # Dependencias y Autoload PSR-4
└── .gitignore              # Archivos excluidos (vendor, .env)