@php
    $name = $user->name ?? '-';
    $initial = collect(explode(' ', $name))->filter()->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
@endphp
<div class="d-flex align-items-center gap-3">
    <span class="jemaah-avatar">{{ $initial ?: 'J' }}</span>
    <div>
        <b>{{ $name }}</b>
        <small class="d-block text-muted">{{ $user->email ?? '-' }}</small>
    </div>
</div>
