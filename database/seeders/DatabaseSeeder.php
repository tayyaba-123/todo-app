<?php

namespace Database\Seeders;

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
        $test = User::firstOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => 'password',
        ]);

        $demo = User::firstOrCreate(['email' => 'demo@example.com'], [
            'name' => 'Demo User',
            'password' => 'password',
        ]);

        foreach ([$test, $demo] as $user) {
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
        }

        $demo->tasks()->firstOrCreate(['title' => 'Buy groceries'], [
            'description' => 'Milk, eggs, and bread.',
            'due_date' => now()->addDay()->toDateString(),
        ]);

        $demo->tasks()->firstOrCreate(['title' => 'Finish the task list'], [
            'description' => null,
            'due_date' => null,
        ]);

        $demo->tasks()->firstOrCreate(['title' => 'Read the Laravel docs'], [
            'description' => 'Skim the Eloquent and validation chapters.',
            'due_date' => now()->subDay()->toDateString(),
            'completed' => true,
        ]);
    }
}
