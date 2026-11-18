<?php

use App\Models\Task;
use App\Models\User;

test('a task belongs to its user and keeps typed due date and completion', function () {
    $user = User::factory()->create();

    $task = Task::factory()->for($user)->create([
        'title' => 'Buy milk',
        'description' => 'Whole milk',
        'due_date' => '2026-11-20',
        'completed' => 1,
    ]);

    expect($task->user->is($user))->toBeTrue();
    expect($user->tasks()->whereKey($task->id)->exists())->toBeTrue();
    expect($task->due_date->toDateString())->toBe('2026-11-20');
    expect($task->completed)->toBeTrue();
});

test('a task allows an empty description and due date and starts incomplete', function () {
    $user = User::factory()->create();

    $attributes = Task::factory()->for($user)->make([
        'description' => null,
        'due_date' => null,
    ])->attributesToArray();

    unset($attributes['completed']);

    $task = Task::query()->create($attributes);

    expect($task->description)->toBeNull();
    expect($task->due_date)->toBeNull();
    expect($task->refresh()->completed)->toBeFalse();
});

test('deleting a user deletes their tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $user->delete();

    $this->assertModelMissing($task);
});
