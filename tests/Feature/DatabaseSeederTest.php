<?php

use App\Models\Task;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('the database seeder can run more than once without duplicating records', function () {
    $this->seed();

    $this->seed();

    expect(User::count())->toBe(2);
    expect(Task::count())->toBe(3);
    $this->assertDatabaseHas('users', ['email' => 'demo@example.com']);
    $this->assertDatabaseHas('tasks', ['title' => 'Read the Laravel docs', 'completed' => true]);
});

test('the database seeder creates verified users even when mass assignment is guarded', function () {
    app(DatabaseSeeder::class)();

    expect(User::where('email', 'test@example.com')->sole()->hasVerifiedEmail())->toBeTrue();
    expect(User::where('email', 'demo@example.com')->sole()->hasVerifiedEmail())->toBeTrue();
});
