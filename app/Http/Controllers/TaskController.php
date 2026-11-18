<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('tasks.index', [
            'tasks' => $request->user()->tasks()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
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
    public function show(Request $request, Task $task): View
    {
        return view('tasks.show', [
            'task' => $this->ownedTask($request, $task),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Task $task): View
    {
        return view('tasks.edit', [
            'task' => $this->ownedTask($request, $task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task = $this->ownedTask($request, $task);

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
    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $this->ownedTask($request, $task)->delete();

        return redirect()
            ->route('tasks.index')
            ->with('status', __('Task deleted.'));
    }

    /**
     * Mark the task complete or incomplete.
     */
    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $task = $this->ownedTask($request, $task);

        $task->update([
            'completed' => ! $task->completed,
        ]);

        return back()->with(
            'status',
            $task->completed ? __('Task marked complete.') : __('Task marked incomplete.'),
        );
    }

    /**
     * Find a task that belongs to the signed-in user.
     */
    private function ownedTask(Request $request, Task $task): Task
    {
        return $request->user()->tasks()->findOrFail($task->id);
    }
}
