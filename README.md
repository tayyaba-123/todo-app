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

## What I learned

- **Keep authorization in one place.** A policy plus `Gate::authorize()` and form-request `authorize()` keeps ownership checks consistent, so one user can never see or change another user's tasks.
- **Form requests keep controllers thin.** Moving validation into `StoreTaskRequest` and `UpdateTaskRequest` left the controller focused on the request flow.
- **Query scopes make filtering reusable.** The `filter` scope on `Task` lets the controller chain `->filter($filter)` onto the user's tasks relationship instead of repeating `where` clauses.
- **Static analysis catches what tests miss.** PHPStan at level 7 flagged relationships and scopes without generic types. Adding `@return HasMany<Task, $this>` and similar annotations also let PHPStan understand scope calls made through a relationship.
- **Seeders should be idempotent.** Using `firstOrCreate()` means `php artisan db:seed` can be run repeatedly without failing on unique columns like `email`.
- **Process matters as much as code.** Work belongs on a branch, in small Conventional Commits, behind a pull request with a clear description, and it only merges after CI is green and someone else has reviewed it.

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
