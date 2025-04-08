# Laboratorios - Universidad Mariana

Sistema de gestión de laboratorios desarrollado para la **Universidad Mariana**. Este repositorio es **privado** y de acceso exclusivo para el equipo de desarrollo autorizado.

## Tabla de Contenidos

1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Instalación](#instalación)
3. [Configuración del Entorno](#configuración-del-entorno)
4. [Uso](#uso)
5. [Estructura del Proyecto](#estructura-del-proyecto)
6. [Contribución](#contribución)
7. [Contacto](#contacto)

## Requisitos del Sistema

Asegúrate de tener instalados los siguientes componentes:

- **PHP** >= 8.3
- **Composer** (última versión estable)
- **MySQL** >= 8.0 o
- **Node.js** >= 18.0
- **npm** (última versión estable)

## Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/Jhontabo/Laboratorios-Alvernia.git
cd laboratorios
```

2. Instala las dependencias de PHP:

```bash
composer install
```

3. Instala las dependencias de Node.js:

```bash
npm install
```

4. Copia el archivo de configuración:

```bash
cp .env.example .env
```

5. Genera la clave de la aplicación:

```bash
php artisan key:generate
```

6. Si tienes problemas al cargar imágenes, ejecuta el siguiente comando:

```bash
php artisan storage:link
```
7. 

## Configuración del Entorno

### Variables de Entorno Requeridas

1. Crea un archivo `.env` en la raíz del proyecto. A continuación se muestra la configuración base necesaria.

### Notas Importantes sobre las Variables de Entorno

- **Seguridad**:
  - Nunca compartas tus credenciales privadas.
  - No subas el archivo `.env` al repositorio.
  - Mantén las claves de API y secretos seguros.

- **Configuración Local**:
  - Cada desarrollador debe crear su propio archivo `.env`.
  - Usa `.env.example` como plantilla.
  - Ajusta los valores según tu entorno local.

- **Base de Datos**:
  - Crea una base de datos local llamada `laboratorios`.
  - Configura las credenciales de tu base de datos local en el archivo `.env`.

## Uso

1. Ejecuta las migraciones:

```bash
php artisan migrate
```
2. 

```bash
php artisan migrate:refresh --seed
```
3. Inicia el servidor de desarrollo:

```bash
php artisan serve
```

4. En otra terminal, compila los assets:

```bash
npm run dev
```

## Estructura del Proyecto

La estructura principal del proyecto es la siguiente:

```
├── app/              # Lógica principal de la aplicación
├── config/           # Archivos de configuración
├── database/         # Migraciones y seeders
├── resources/        # Vistas, assets y traducciones
├── routes/           # Definición de rutas
└── tests/            # Pruebas automatizadas
```
