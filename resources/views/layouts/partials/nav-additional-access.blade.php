@php
    $user = auth()->user();
    $granted = collect(\App\Support\ViewPermissions::catalog())
        ->filter(fn ($meta, $key) => $user->hasPermission($key));
@endphp

@if($user->hasAnyAdditionalAccess() && $granted->isNotEmpty())
    <div class="nav-section">Additional Access</div>
    @foreach($granted as $key => $meta)
        <a href="{{ route($meta['route']) }}" class="nav-link {{ request()->routeIs(str_replace('.index', '.*', $meta['route'])) ? 'active' : '' }}">
            <i class="bi bi-{{ $meta['icon'] }}"></i> {{ $meta['label'] }}
        </a>
    @endforeach
@endif
