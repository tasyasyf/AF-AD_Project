<x-layouts.app title="Notifications">

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Notifications</h1>
    @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-check2-all"></i> Mark all as read
            </button>
        </form>
    @endif
</div>

<div class="card">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            @php $isUnread = is_null($notification->read_at); @endphp
            <a href="{{ route('notifications.read', $notification->id) }}"
               class="list-group-item list-group-item-action d-flex gap-3 py-3 {{ $isUnread ? 'bg-light' : '' }}">
                <span class="mt-1 fs-5">
                    @switch($notification->data['type'] ?? '')
                        @case('claim_submitted')
                            <i class="bi bi-file-earmark-plus text-primary"></i>
                            @break
                        @case('claim_approved')
                            <i class="bi bi-check-circle text-success"></i>
                            @break
                        @case('claim_rejected')
                            <i class="bi bi-x-circle text-danger"></i>
                            @break
                        @default
                            <i class="bi bi-info-circle text-secondary"></i>
                    @endswitch
                </span>
                <div class="flex-grow-1">
                    <div class="fw-semibold {{ $isUnread ? '' : 'text-muted' }}">
                        {{ $notification->data['title'] ?? 'Notification' }}
                        @if($isUnread)
                            <span class="badge bg-danger ms-1">New</span>
                        @endif
                    </div>
                    <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <i class="bi bi-chevron-right text-muted align-self-center"></i>
            </a>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-bell-slash d-block mb-2 fs-3"></i>
                You have no notifications yet.
            </div>
        @endforelse
    </div>
</div>

@if($notifications->hasPages())
    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endif

</x-layouts.app>
