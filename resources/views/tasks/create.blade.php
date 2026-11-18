<x-layouts::app :title="__('New task')">
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl" level="1">{{ __('New task') }}</flux:heading>
            <flux:subheading>{{ __('Add something you want to get done.') }}</flux:subheading>
        </div>

        @include('tasks.partials.form', ['action' => route('tasks.store')])
    </div>
</x-layouts::app>
