<div>
    <x-filament::dropdown align="right">
        <x-slot name="trigger">
            <x-filament::button icon="heroicon-o-bell" />
        </x-slot>

        <div class="max-h-60 overflow-y-auto">
            @foreach(auth()->user()->unreadNotifications as $notification)
                <div class="p-2 border-b">
                    <strong>{{ $notification->data['title'] }}</strong>
                    <p>{{ $notification->data['body'] }}</p>
                </div>
            @endforeach
        </div>
    </x-filament::dropdown>
</div>
