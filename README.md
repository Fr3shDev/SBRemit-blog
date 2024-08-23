# BlogAPI

## Description

This project is a RESTful API for a blog application. The API is built using the Laravel framework, leveraging Laravel Sail for development in a Dockerized environment. Sail simplifies the process of setting up a local development environment with Docker.

### Features

- Authentication
- CRUD Categories
- CRUD Blog Posts

## Getting Started

### Prerequisites

Ensure you have the following installed on your machine:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [PHP](https://www.php.net/) (8.2 or compatible)
- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)

### Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/Fr3shDev/SBRemit-blog.git
   cd SBRemit-blog
   ```

2. **Copy the `.env.example` file to `.env` and configure your environment variables:**

   ```bash
   cp .env.example .env
   ```

3. **Start the Laravel Sail environment:**

   Laravel Sail is used to manage the Docker containers for this project. To start the environment, use the following command:

   ```bash
   ./vendor/bin/sail up
   ```

   This command will start the Docker containers in the background. The application will be accessible at [http://localhost](http://localhost) and the API docs at [http://localhost/docs/api](http://localhost/docs/api).

4. **Install dependencies:**

   After starting the containers, install the PHP dependencies:

   ```bash
   ./vendor/bin/sail composer install
   ```

5. **Run database migrations:**

   Set up the database tables by running the migrations:

   ```bash
   ./vendor/bin/sail artisan migrate
   ```

6. **Seed the database (optional):**

   If you want to seed your database with initial data, you can run:

   ```bash
   ./vendor/bin/sail artisan db:seed
   ```

## Running Tests

To ensure everything is working as expected, you can run the included unit and feature tests.

1. **Run all tests:**

   ```bash
   ./vendor/bin/sail test
   ```

   This command will run all unit and feature tests in your project.

## Available API Endpoints

Here are some of the available endpoints for this API:

- **GET /api/resource**: Retrieve a list of resources.
- **POST /api/resource**: Create a new resource.
- **GET /api/resource/{id}**: Retrieve a specific resource by ID.
- **PUT /api/resource/{id}**: Update a specific resource by ID.
- **DELETE /api/resource/{id}**: Delete a specific resource by ID.

For detailed documentation on the available API endpoints, check out the [API Documentation](link_to_api_docs_if_available).

## Using Sail Commands

Here are some common Sail commands you might find useful:

- **Start the containers:**

  ```bash
  ./vendor/bin/sail up -d
  ```

- **Stop the containers:**

  ```bash
  ./vendor/bin/sail down
  ```

- **Access the application container:**

  ```bash
  ./vendor/bin/sail shell
  ```

- **Run Artisan commands:**

  ```bash
  ./vendor/bin/sail artisan <command>
  ```

- **Run Composer commands:**

  ```bash
  ./vendor/bin/sail composer <command>
  ```

## Contributing

If you'd like to contribute to this project, please fork the repository and use a feature branch. Pull requests are warmly welcome.

