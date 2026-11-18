@php
    $task ??= null;
    $method ??= 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="flex flex-col gap-6">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <flux:input
        name="title"
        :label="__('Title')"
        type="text"
        :value="old('title', $task?->title ?? '')"
        required
        autofocus
    />

    <flux:textarea name="description" :label="__('Description')" rows="4">{{ old('description', $task?->description ?? '') }}</flux:textarea>

    <flux:input
        name="due_date"
        :label="__('Due date')"
        type="date"
        :value="old('due_date', isset($task) ? $task->due_date?->toDateString() : '')"
    />

    <div class="flex items-center gap-3">
        <flux:button variant="primary" type="submit">
            {{ isset($task) ? __('Save changes') : __('Create task') }}
        </flux:button>

        <flux:button variant="ghost" :href="route('tasks.index')" wire:navigate>
            {{ __('Cancel') }}
        </flux:button>
    </div>
</form>
