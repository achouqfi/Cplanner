# Laravel Starter Project

This is a Laravel starter project that includes a comprehensive setup with Docker, Laravel Sail, and various packages to get you started quickly on any project. It includes:

- Laravel Sail with Docker support
- Mailpit for email testing
- MySQL for database
- PhpMyAdmin for database management
- Redis for caching
- Spatie packages for settings, SEO, media library, and permissions
- Filament PHP for admin panel

## Features

- **Laravel Sail**: Easy Docker setup for Laravel.
- **Mailpit**: Local email testing.
- **MySQL**: Database management.
- **PhpMyAdmin**: Web interface for MySQL.
- **Redis**: Advanced key-value store.
- **Spatie Packages**:
  - Settings
  - SEO
  - Media Library
  - Permissions
- **Filament PHP**: Admin panel.

## Installation

### Prerequisites

- Docker
- Docker Compose
- Composer
- Node.js and npm

### Steps

1. **Clone the repository**:
    ```sh
    git clone https://github.com/your-username/laravel-starter.git
    cd laravel-starter
    ```

2. **Copy the example environment file and configure it**:
    ```sh
    cp .env.example .env
    ```

3. **Install PHP dependencies**:
    ```sh
    composer install
    ```

4. **Install Node.js dependencies**:
    ```sh
    npm install
    ```

5. **Generate application key**:
    ```sh
    php artisan key:generate
    ```

6. **Start Docker containers**:
    ```sh
    ./vendor/bin/sail up -d
    ```

7. **Run database migrations**:
    ```sh
    ./vendor/bin/sail artisan migrate
    ```

8. **Install Filament PHP**:
    ```sh
    ./vendor/bin/sail artisan filament:install
    ```

## Usage

- **Access the application**: [http://localhost](http://localhost)
- **PhpMyAdmin**: [http://localhost:8080](http://localhost:8080)
- **Mailpit**: [http://localhost:8025](http://localhost:8025)

## Additional Configuration

### Spatie Packages

- **Settings**: Configure settings in `config/settings.php`.
- **SEO**: Configure SEO settings in `config/seo.php`.
- **Media Library**: Configure media library in `config/media-library.php`.
- **Permissions**: Configure permissions in `config/permission.php`.

### Filament PHP

- Access the admin panel at [http://localhost/admin](http://localhost/admin).

## Contributing

Feel free to submit issues or pull requests.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
