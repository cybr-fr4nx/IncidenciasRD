# Incidencias RD

Sistema web para el reporte, validación y visualización de incidencias en la RD.

## Características principales

- Reporte de incidencias con ubicacion en mapa, foto y detalles.
- Visualización de incidencias en mapa interactivo (Leaflet.js + clustering).
- Filtros por provincia, tipo, fecha y titulo.
- Autenticacion con Office 365 (OAuth2) y login tradicional para validadores.
- Panel de validacion para validadores/admins: aprobar, rechazar y eliminar incidencias.
- Panel de administración de caalogos (provincias, municipios, barrios, tipos de incidencia).
- Gestion de usuarios y roles (reportero, validador, admin).
- Responsive y moderno (Bootstrap 5).

## Estructura del proyecto

```
incidencias-rd/
├── api/                # Endpoints para AJAX y autenticacion
├── assets/
│   ├── css/            # Estilos personalizados
│   └── js/             # Scripts JS (mapa)
├── db/                 # Script SQL de la base de datos
├── includes/           # Archivos comunes (header, footer, db, funciones)
├── super/              # Panel de validadores/admins
├── uploads/            # Fotos de incidencias
├── index.php           # Mapa publico y filtros
├── report.php          # Formulario de reporte de incidencia
├── login.php           # Login de usuarios
├── logout.php          # Logout de usuarios
└── ...
```

## Instalacion y configuracion

1. Clona el repositorio en tu servidor local (XAMPP recomendado).
2. Importa el script SQL desde `db/script.db` en tu MySQL.
3. Configura la conexión a la base de datos en `includes/db.php`.
4. Configura las credenciales de Office 365 en `includes/config.php`.
5. Accede a `http://localhost/incidencias-rd/` para usar la app.

## Requisitos

- PHP 7.4+
- MySQL/MariaDB
- XAMPP/WAMP/LAMP recomendado
- Navegador moderno

---

Desarrollado por cybr-fr4nx.
