# Laboratorios - Universidad Mariana

Sistema de gestión de laboratorios desarrollado para la Universidad Mariana. Este repositorio es **privado** y de acceso exclusivo para el equipo de desarrollo autorizado.

## Tabla de Contenidos
1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Instalación](#instalación)
3. [Configuración del Entorno](#configuración-del-entorno)
4. [Uso](#uso)
5. [Estructura del Proyecto](#estructura-del-proyecto)
6. [Contribución](#contribución)
7. [Contacto](#contacto)

## Requisitos del Sistema

Asegúrate de tener instalado:

- PHP >= 8.0
- Composer (última versión estable)
- MySQL >= 5.7 o MariaDB
- Node.js >= 16.0
- npm o yarn (última versión estable)

## Instalación

1. Clona el repositorio:
```bash
git clone <https://github.com/Jhontabo/Laboratorios-Alvernia.git>
cd laboratorios
```

2. Instala las dependencias de PHP:
```bash
composer install
```

3. Instala las dependencias de Node.js:
```bash
npm install
# o si usas yarn:
yarn install
```

4. Copia el archivo de configuración:
```bash
cp .env.example .env
```

5. Genera la clave de la aplicación:
```bash
php artisan key:generate
```

## Configuración del Entorno

### Variables de Entorno Requeridas

Crea un archivo `.env` en la raíz del proyecto. A continuación se muestra la configuración base necesaria:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:4crm5cI4hGqraotJNjjPz6t0eGHg272S7TU8IZ2Q2XU=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laboratorios
DB_USERNAME=root
DB_PASSWORD=277353

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

GOOGLE_CLIENT_ID=206158634484-n33qdthgmja2ts664a2va4u7jplpe2i6.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT=http://127.0.0.1:8000/auth/google/callback
```

### Notas Importantes sobre las Variables de Entorno

1. **Seguridad**: 
   - Nunca compartas tus credenciales privadas
   - No subas el archivo `.env` al repositorio
   - Mantén las claves de API y secretos seguros

2. **Configuración Local**:
   - Cada desarrollador debe crear su propio archivo `.env`
   - Usa `.env.example` como plantilla
   - Ajusta los valores según tu entorno local

3. **Base de Datos**:
   - Crea una base de datos local llamada `laboratorios`
   - Configura las credenciales de tu base de datos local

## Uso

1. Ejecuta las migraciones:
```bash
php artisan migrate
```

2. Inicia el servidor de desarrollo:
```bash
php artisan serve
```

3. En otra terminal, compila los assets:
```bash
npm run dev
# o con yarn:
yarn dev
```

## Estructura del Proyecto

Descripción de las carpetas principales del proyecto:

```
├── app/                # Lógica principal de la aplicación
├── config/            # Archivos de configuración
├── database/          # Migraciones y seeders
├── resources/         # Vistas, assets y traducciones
├── routes/            # Definición de rutas
└── tests/             # Pruebas automatizadas
```

## Contribución

1. Crea una rama para tu feature: `git checkout -b feature/nombre-feature`
2. Haz commit de tus cambios: `git commit -m 'Añade nueva feature'`
3. Empuja a la rama: `git push origin feature/nombre-feature`
4. Envía un pull request

## Contacto

Para dudas o sugerencias, contacta al equipo de desarrollo:
- Email: <correo_del_equipo>
- Gestor de Proyectos: <nombre_del_pm>