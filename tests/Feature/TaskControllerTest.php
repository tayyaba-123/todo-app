<?php

use App\Models\Task;
use App\Models\User;

test('guests are redirected to the login page', function (string $method, string $routeName) {
    $task = Task::factory()->create();

    $parameters = in_array($routeName, ['tasks.index', 'tasks.create', 'tasks.store'], true)
        ? []
        : $task;

    $this->{$method}(route($routeName, $parameters))
        ->assertRedirect(route('login'));
})->with([
    'list' => ['get', 'tasks.index'],
    'create form' => ['get', 'tasks.create'],
    'store' => ['post', 'tasks.store'],
    'show' => ['get', 'tasks.show'],
    'edit' => ['get', 'tasks.edit'],
    'update' => ['put', 'tasks.update'],
    'delete' => ['delete', 'tasks.destroy'],
    'toggle' => ['patch', 'tasks.toggle'],
]);

test('the navigation links to tasks', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('tasks.index'), false);
});

test('a user can view the create, edit, and show pages', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['title' => 'Buy milk']);

    $this->actingAs($user)
        ->get(route('tasks.create'))
        ->assertOk()
        ->assertSee('New task');

    $this->actingAs($user)
        ->get(route('tasks.edit', $task))
        ->assertOk()
        ->assertSee('Buy milk');

    $this->actingAs($user)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->assertSee('Buy milk');
});

test('a user can create a task', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => 'Buy milk',
            'description' => 'Whole milk',
            'due_date' => '2026-11-20',
        ])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('status', 'Task created.');

    $task = Task::query()->where('title', 'Buy milk')->first();

    expect($task)->not->toBeNull();
    expect($task->user_id)->toBe($user->id);
    expect($task->description)->toBe('Whole milk');
    expect($task->due_date->toDateString())->toBe('2026-11-20');
    expect($task->completed)->toBeFalse();
});

test('a task is saved for the signed-in user when optional fields are omitted', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => 'Buy milk',
            'user_id' => $other->id,
        ])
        ->assertRedirect(route('tasks.index'));

    $task = Task::query()->sole();

    expect($task->user_id)->toBe($user->id);
    expect($task->description)->toBeNull();
    expect($task->due_date)->toBeNull();
    expect($task->completed)->toBeFalse();
});

test('creating a task requires a title', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => '',
            'description' => 'Details',
            'due_date' => '2026-11-20',
        ])
        ->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

    $this->assertDatabaseCount('tasks', 0);
});

test('creating a task rejects a title longer than 255 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 255 characters.',
        ]);

    $this->assertDatabaseCount('tasks', 0);
});

test('creating a task rejects an invalid due date', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => 'Buy milk',
            'due_date' => 'not-a-date',
        ])
        ->assertSessionHasErrors([
            'due_date' => 'The due date field must be a valid date.',
        ]);

    $this->assertDatabaseCount('tasks', 0);
});

test('a user can update their task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->completed()->create([
        'title' => 'Buy milk',
        'description' => 'Whole milk',
        'due_date' => '2026-11-20',
    ]);

    $this->actingAs($user)
        ->put(route('tasks.update', $task), [
            'title' => 'Buy bread',
            'description' => '',
            'due_date' => '',
        ])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('status', 'Task updated.');

    $task->refresh();

    expect($task->title)->toBe('Buy bread');
    expect($task->description)->toBeNull();
    expect($task->due_date)->toBeNull();
    expect($task->completed)->toBeTrue();
});

test('updating a task requires a title', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['title' => 'Buy milk']);

    $this->actingAs($user)
        ->put(route('tasks.update', $task), [
            'title' => '',
        ])
        ->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

    expect($task->refresh()->title)->toBe('Buy milk');
});

test('a user can delete their task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('status', 'Task deleted.');

    $this->assertModelMissing($task);
});

test('a user can mark a task complete and incomplete', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('tasks.index'))
        ->patch(route('tasks.toggle', $task))
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('status', 'Task marked complete.');

    expect($task->refresh()->completed)->toBeTrue();

    $this->actingAs($user)
        ->from(route('tasks.index', ['filter' => 'completed']))
        ->patch(route('tasks.toggle', $task))
        ->assertRedirect(route('tasks.index', ['filter' => 'completed']))
        ->assertSessionHas('status', 'Task marked incomplete.');

    expect($task->refresh()->completed)->toBeFalse();
});

test('a user only sees their own tasks', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->create(['title' => 'My task']);
    Task::factory()->create(['title' => 'Someone else task']);

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertSee('My task')
        ->assertDontSee('Someone else task');
});

test('a user cannot open or change another user\'s task', function (string $method, string $routeName) {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Private task',
        'completed' => false,
    ]);

    $this->actingAs($user)
        ->{$method}(route($routeName, $task))
        ->assertNotFound();

    $this->assertModelExists($task);

    $task->refresh();

    expect($task->title)->toBe('Private task');
    expect($task->completed)->toBeFalse();
})->with([
    'show' => ['get', 'tasks.show'],
    'edit' => ['get', 'tasks.edit'],
    'update' => ['put', 'tasks.update'],
    'delete' => ['delete', 'tasks.destroy'],
    'toggle' => ['patch', 'tasks.toggle'],
]);

test('task permissions follow ownership', function () {
    $user = User::factory()->create();
    $ownTask = Task::factory()->for($user)->create();
    $otherTask = Task::factory()->create();

    expect($user->can('viewAny', Task::class))->toBeTrue();
    expect($user->can('create', Task::class))->toBeTrue();
    expect($user->can('view', $ownTask))->toBeTrue();
    expect($user->can('update', $ownTask))->toBeTrue();
    expect($user->can('delete', $ownTask))->toBeTrue();
    expect($user->can('view', $otherTask))->toBeFalse();
    expect($user->can('update', $otherTask))->toBeFalse();
    expect($user->can('delete', $otherTask))->toBeFalse();
});

test('the task list filters by completion', function (string $filter, string $visible, string $hidden) {
    $user = User::factory()->create();
    Task::factory()->for($user)->create(['title' => 'Active task']);
    Task::factory()->for($user)->completed()->create(['title' => 'Finished task']);

    $this->actingAs($user)
        ->get(route('tasks.index', ['filter' => $filter]))
        ->assertOk()
        ->assertSee($visible)
        ->assertDontSee($hidden);
})->with([
    'active' => ['active', 'Active task', 'Finished task'],
    'completed' => ['completed', 'Finished task', 'Active task'],
]);

test('the task list shows every task when the filter is all or unrecognized', function (string $filter) {
    $user = User::factory()->create();
    Task::factory()->for($user)->create(['title' => 'Active task']);
    Task::factory()->for($user)->completed()->create(['title' => 'Finished task']);

    $this->actingAs($user)
        ->get(route('tasks.index', ['filter' => $filter]))
        ->assertOk()
        ->assertSee('Active task')
        ->assertSee('Finished task');
})->with([
    'all' => 'all',
    'unrecognized' => 'nope',
]);
