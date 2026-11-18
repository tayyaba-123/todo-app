<x-layouts::app :title="__('Tasks')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-1">
                <flux:heading size="xl" level="1">{{ __('Tasks') }}</flux:heading>
                <flux:subheading>{{ __('Keep track of what you need to do.') }}</flux:subheading>
            </div>

            <flux:button variant="primary" icon="plus" :href="route('tasks.create')" wire:navigate>
                {{ __('New task') }}
            </flux:button>
        </div>

        @if (session('status'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('status') }}
            </flux:callout>
        @endif

        @if ($tasks->isEmpty())
            <div class="rounded-xl border border-zinc-200 p-8 text-center dark:border-zinc-700">
                <flux:heading>{{ __('No tasks yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Create a task to get started.') }}</flux:text>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($tasks as $task)
                    <article class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-4 sm:flex-row sm:items-start sm:justify-between dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex min-w-0 flex-1 flex-col gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('tasks.show', $task) }}" class="min-w-0 break-words font-medium text-zinc-800 dark:text-white {{ $task->completed ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }}" wire:navigate>
                                    {{ $task->title }}
                                </a>

                                @if ($task->completed)
                                    <flux:badge color="green" size="sm">{{ __('Completed') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('Active') }}</flux:badge>
                                @endif
                            </div>

                            @if ($task->description)
                                <flux:text class="line-clamp-2 break-words">{{ $task->description }}</flux:text>
                            @endif

                            @if ($task->due_date)
                                <flux:text size="sm">{{ __('Due :date', ['date' => $task->due_date->format('M j, Y')]) }}</flux:text>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                                @csrf
                                @method('PATCH')

                                <flux:button type="submit" size="sm" variant="outline">
                                    {{ $task->completed ? __('Mark incomplete') : __('Mark complete') }}
                                </flux:button>
                            </form>

                            <flux:button size="sm" variant="ghost" :href="route('tasks.edit', $task)" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>

                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('{{ __('Delete this task?') }}')">
                                @csrf
                                @method('DELETE')

                                <flux:button type="submit" size="sm" variant="ghost" color="red">
                                    {{ __('Delete') }}
                                </flux:button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
