# Proyecto Symfony

[![Symfony](https://img.shields.io/badge/Symfony-7.2-blue.svg)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://www.mysql.com/)

Aplicación web construida con el framework **Symfony**.

## Requisitos

- PHP >= 8.1
- Composer
- Symfony CLI (opcional, pero recomendado)
- Base de datos (MySQL, PostgreSQL, etc.)

## Instalación

1. Una vez clonado el repo, instala las dependencias PHP:

   ```bash
   composer install
   ```

3. Copia el archivo de entorno y configura tu entorno local:

   ```bash
   cp .env .env.local
   ```

4. Crea la base de datos:

   ```bash
   php bin/console doctrine:database:create
   ```

5. Ejecuta las migraciones:

   ```bash
   php bin/console doctrine:migrations:migrate
   ```

## Servidor de desarrollo

Con Symfony CLI:

```bash
symfony server:start
```

O usando PHP directamente:

```bash
php -S 127.0.0.1:8000 -t public
```