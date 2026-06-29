@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
        <p class="mt-1 text-sm text-slate-500">Your recent system alerts and intelligence feed.</p>
    </div>
    <div class="flex items-center gap-3">
        @if(Auth::user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-95">
                <i class="fas fa-check-double text-primary-500"></i>
                Mark All Read
            </button>
        </form>
        @endif
    </div>
</div>

<div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
    @if($notifications->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach($notifications as $notification)
                <div class="py-4 flex gap-4 {{ is_null($notification->read_at) ? 'bg-primary-50/50 -mx-6 px-6' : '' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ is_null($notification->read_at) ? 'bg-white text-primary-500 shadow-sm' : 'bg-slate-50 text-slate-400' }}">
                        <i class="fas {{ $notification->data['type'] === 'warning' ? 'fa-exclamation-triangle text-amber-500' : 'fa-info-circle' }}"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                        <p class="mt-1 text-sm text-slate-600">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->format('M d, Y H:i') }} ({{ $notification->created_at->diffForHumans() }})</p>
                    </div>
                    <div class="flex flex-col justify-center gap-2">
                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-primary-500 hover:underline">Mark Read</button>
                            </form>
                        @endif
                        @if(isset($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 hover:underline">View Details</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-400">
                <i class="far fa-bell-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No Notifications</h3>
            <p class="mt-1 text-sm text-slate-500">You don't have any notifications yet.</p>
        </div>
    @endif
</div>
@endsection
