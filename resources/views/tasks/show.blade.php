<x-layouts::app :title="$task->title">
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
        <div class="flex flex-col gap-3">
            <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('tasks.index')" wire:navigate class="self-start">
                {{ __('Back to tasks') }}
            </flux:button>

            <div class="flex flex-wrap items-center gap-2">
                <flux:heading size="xl" level="1" class="{{ $task->completed ? 'line-through' : '' }}">
                    {{ $task->title }}
                </flux:heading>

                @if ($task->completed)
                    <flux:badge color="green">{{ __('Completed') }}</flux:badge>
                @else
                    <flux:badge color="zinc">{{ __('Active') }}</flux:badge>
                @endif
            </div>

            @if ($task->due_date)
                <flux:text>{{ __('Due :date', ['date' => $task->due_date->format('M j, Y')]) }}</flux:text>
            @endif
        </div>

        @if (session('status'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('status') }}
            </flux:callout>
        @endif

        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            @if ($task->description)
                <flux:text class="whitespace-pre-wrap break-words">{{ $task->description }}</flux:text>
            @else
                <flux:text>{{ __('No description.') }}</flux:text>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                @csrf
                @method('PATCH')

                <flux:button type="submit" variant="outline">
                    {{ $task->completed ? __('Mark incomplete') : __('Mark complete') }}
                </flux:button>
            </form>

            <flux:button variant="primary" :href="route('tasks.edit', $task)" wire:navigate>
                {{ __('Edit') }}
            </flux:button>

            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('{{ __('Delete this task?') }}')">
                @csrf
                @method('DELETE')

                <flux:button type="submit" variant="ghost" color="red">
                    {{ __('Delete') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::app>
