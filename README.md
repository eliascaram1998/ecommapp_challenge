# EcommApp Challenge

Este proyecto es una aplicación CRUD (Crear, Leer, Actualizar y Eliminar) destinada a la gestión de productos en un entorno de comercio electrónico. Utiliza Laravel como framework principal y ofrece una interfaz de usuario intuitiva para interactuar con los datos de los productos.

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Funcionalidades](#funcionalidades)
- [Uso](#uso)
- [Comandos útiles](#comandos-útiles)
- [Contribución](#contribución)
- [Licencia](#licencia)

## Requisitos

- PHP >= 7.3
- Composer
- Laravel >= 8.x
- Servidor web (Apache o Nginx)

## Instalación

1. Clona el repositorio:
   ```bash
   git clone https://github.com/eliascaram1998/ecommapp_challenge.git
   cd ecommapp_challenge
   composer install
   cp .env.example .env
   php artisan key:generate
Configuración
Asegúrate de configurar el archivo .env con tus credenciales y parámetros específicos antes de iniciar la aplicación.

Funcionalidades
CRUD de Productos: Permite crear, leer, actualizar y eliminar productos.
Sesiones de Usuario: Maneja las sesiones para asegurar que las acciones de creación y edición de productos solo se realicen si el usuario está autenticado.
Filtrado y Paginación: Filtra productos por título, precio y fecha de creación, con paginación de resultados.
