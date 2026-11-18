# Todo App

A full-stack task management application built with Laravel, Livewire, Flux UI, and Tailwind CSS.

## Features

- User registration and authentication
- Password reset, two-factor authentication, and passkeys
- Create, view, edit, and delete tasks
- Mark tasks as complete or incomplete
- Filter tasks by active or completed status
- Optional task descriptions and due dates
- Per-user task ownership and authorization
- Profile, password, appearance, and security settings
- Responsive light and dark interfaces

## Tech Stack

- PHP 8.3+
- Laravel 13
- Livewire 4
- Flux UI 2
- Tailwind CSS 4
- SQLite by default
- Pest 5

## Installation

Clone the repository and enter the project:

```bash
git clone https://github.com/tayyaba-123/todo-app.git
cd todo-app
```

Run the automated setup:

```bash
composer run setup
```

This installs the PHP and JavaScript dependencies, creates the `.env` file, generates the application key, runs the database migrations, and builds the frontend assets.

## Running the Application

Start the local development environment:

```bash
composer run dev
```

Open the URL shown in the terminal, register an account, and select **Tasks** from the sidebar.

## Demo Data

To rebuild the database and add sample users and tasks:

```bash
php artisan migrate:fresh --seed
```

The seeded accounts use the password `password`. The demo account is:

```text
Email: demo@example.com
Password: password
```

## Testing

Run the complete project checks:

```bash
composer test
```

Run only the task feature tests:

```bash
php artisan test --compact tests/Feature/TaskControllerTest.php tests/Feature/TaskTest.php
```

## Task Request Flow

Task requests pass through authentication middleware, controller or form-request authorization, validation, the task policy, and Eloquent persistence. Users can only access tasks that belong to their own account.

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
