<?php

use App\Models\Task;
use App\Models\User;

test('the database seeder can run more than once without duplicating records', function () {
    $this->seed();

    $this->seed();

    expect(User::count())->toBe(2);
    expect(Task::count())->toBe(3);
    $this->assertDatabaseHas('users', ['email' => 'demo@example.com']);
    $this->assertDatabaseHas('tasks', ['title' => 'Read the Laravel docs', 'completed' => true]);
});
