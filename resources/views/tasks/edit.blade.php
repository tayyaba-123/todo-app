<x-layouts::app :title="__('Edit task')">
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl" level="1">{{ __('Edit task') }}</flux:heading>
            <flux:subheading>{{ __('Update the details of this task.') }}</flux:subheading>
        </div>

        @include('tasks.partials.form', [
            'action' => route('tasks.update', $task),
            'method' => 'PUT',
            'task' => $task,
        ])
    </div>
</x-layouts::app>
