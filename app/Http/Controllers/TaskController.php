<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Task::class);

        return view('tasks.index', [
            'tasks' => $request->user()->tasks()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Task::class);

        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->safe()->only([
            'title',
            'description',
            'due_date',
        ]));

        return redirect()
            ->route('tasks.index')
            ->with('status', __('Task created.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        Gate::authorize('view', $task);

        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->safe()->only([
            'title',
            'description',
            'due_date',
        ]));

        return redirect()
            ->route('tasks.index')
            ->with('status', __('Task updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('status', __('Task deleted.'));
    }

    /**
     * Mark the task complete or incomplete.
     */
    public function toggle(Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $task->update([
            'completed' => ! $task->completed,
        ]);

        return back()->with(
            'status',
            $task->completed ? __('Task marked complete.') : __('Task marked incomplete.'),
        );
    }
}
