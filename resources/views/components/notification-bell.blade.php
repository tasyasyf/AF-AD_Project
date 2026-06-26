@php
    $user = auth()->user();
    $unread = $user->unreadNotifications;
    $unreadCount = $unread->count();
    $recent = $user->notifications()->take(8)->get();
@endphp

<div class="dropdown">
    <button class="btn btn-sm btn-light position-relative" type="button" data-bs-toggle="dropdown"
            data-bs-auto-close="outside" aria-expanded="false" aria-label="Notifications">
        <i class="bi bi-bell fs-5"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                <span class="visually-hidden">unread notifications</span>
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow" style="width: 340px; max-width: 92vw;">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <span class="fw-semibold">Notifications</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Mark all read</button>
                </form>
            @endif
        </div>

        <div style="max-height: 360px; overflow-y: auto;">
            @forelse($recent as $notification)
                @php $isUnread = is_null($notification->read_at); @endphp
                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="dropdown-item d-flex gap-2 py-2 px-3 border-bottom text-wrap {{ $isUnread ? 'bg-light' : '' }}">
                    <span class="mt-1">
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
                    <span class="flex-grow-1">
                        <span class="d-block small fw-semibold {{ $isUnread ? '' : 'text-muted' }}">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </span>
                        <span class="d-block small text-muted">{{ $notification->data['message'] ?? '' }}</span>
                        <span class="d-block text-muted" style="font-size: 0.72rem;">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                    @if($isUnread)
                        <span class="badge bg-danger rounded-pill align-self-start" style="font-size: 0.55rem;">&nbsp;</span>
                    @endif
                </a>
            @empty
                <div class="text-center text-muted small py-4">
                    <i class="bi bi-bell-slash d-block mb-1 fs-5"></i>
                    No notifications yet
                </div>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small py-2 fw-semibold">
            View all notifications
        </a>
    </div>
</div>
