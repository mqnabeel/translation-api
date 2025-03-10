# Project Name

A Laravel-based application with API documentation using Swagger.

## Features

- RESTful API architecture
- Interactive API documentation with Swagger UI
- Laravel framework for robust backend development

## Requirements

- PHP >= 8.0
- Composer
- MySQL or PostgreSQL
- Node.js & NPM (for frontend assets)

## Installation

1. Clone the repository
   ```bash
   git clone https://github.com/yourusername/project-name.git
   cd project-name
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Install JavaScript dependencies
   ```bash
   npm install && npm run dev
   ```

4. Create environment file
   ```bash
   cp .env.example .env
   ```

5. Generate application key
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. Run database migrations
   ```bash
   php artisan migrate
   ```

8. Start the development server
   ```bash
   php artisan serve
   ```

## API Documentation

The API documentation is available at `/api-docs` endpoint. This provides an interactive Swagger UI to explore and test the API endpoints.

To access the documentation:
1. Start the development server
2. Navigate to `http://localhost:8000/api-docs` in your browser

## Usage

- The main application is accessible at `http://localhost:8000`
- API documentation is available at `http://localhost:8000/api-docs`

## Development

### Adding New API Endpoints

1. Define routes in `routes/api.php`
2. Create controllers in `app/Http/Controllers`
3. Document your API using Swagger annotations
4. Update the Swagger JSON file with `php artisan l5-swagger:generate`

## Testing

Run the automated tests with:

php artisan test

## Deployment

1. Set up your production environment
2. Configure environment variables for production
3. Run migrations
4. Compile assets for production
   ```bash
   npm run build
   ```

## License

[MIT](LICENSE)
