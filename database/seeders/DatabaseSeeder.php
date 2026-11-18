<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $demo = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        Task::factory()->for($demo)->create([
            'title' => 'Buy groceries',
            'description' => 'Milk, eggs, and bread.',
            'due_date' => now()->addDay()->toDateString(),
        ]);

        Task::factory()->for($demo)->create([
            'title' => 'Finish the task list',
            'description' => null,
            'due_date' => null,
        ]);

        Task::factory()->for($demo)->completed()->create([
            'title' => 'Read the Laravel docs',
            'description' => 'Skim the Eloquent and validation chapters.',
            'due_date' => now()->subDay()->toDateString(),
        ]);
    }
}
