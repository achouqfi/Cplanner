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

## Features

- **Laravel Sail**: A light-weight command-line interface for interacting with Laravel's default Docker development environment. It provides a simple way to set up and manage a local development environment using Docker.
  - **Mailpit**: Local email testing.
  - **MySQL**: Database management.
  - **PhpMyAdmin**: Web interface for MySQL.
  - **Redis**: Advanced key-value store.
- **Spatie Packages**:
  - **Media Library**: Associate files with Eloquent models.
  - **Settings**: Manage application settings.
  - **Laravel Translatable**: Make Eloquent models translatable.
  - **Laravel Sluggable**: Generate slugs for Eloquent models.
  - **Permission**: Manage user permissions and roles.
  - **Activity Log**: Log activity in your application.
  - **Laravel Backup**: Backup your application.
  - **Image**: Manipulate images.
  - **Laravel Tags**: Add tags to Eloquent models.
  - **Laravel Searchable**: Make Eloquent models searchable.
  - **Enum**: Provides support for creating and using enums in PHP.

- **Filament PHP**: A beautiful, modern, and customizable admin panel for Laravel applications.
  - **User Management**: Manage users and roles.
  - **Settings**: Manage application settings.
  - **Activity Log**: View activity logs.
  - **Media Library**: Manage media files.
  - **Permissions**: Manage user permissions.
  - **SEO**: Manage SEO settings.
  - **Tags**: Manage tags.
  - **Translations**: Manage translations.
  - **Backup**: Backup the application.

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
