@props([
    'title',
    'description' => '',
    'section' => null,
    'current' => null,
    'breadcrumbId' => null,
])

<div {{ $attributes->class(['recap-heading']) }}>
    <div>
        <div class="recap-breadcrumb">
            <a href="/dashboard">Dashboard</a>
            @if ($section)
                <i class="fas fa-chevron-right"></i>
                <span>{{ $section }}</span>
            @endif
            <i class="fas fa-chevron-right"></i>
            <span @if($breadcrumbId) id="{{ $breadcrumbId }}" @endif>{{ $current ?: $title }}</span>
        </div>
        <h1>{{ $title }}</h1>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="recap-heading-actions">
            {{ $actions }}
        </div>
    @endisset
</div>
