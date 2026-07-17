<div class="verify-node {{ $step['state'] }}">
    <span class="node-icon">
        @if($step['state'] === 'verified')
            <i class="fas fa-check"></i>
        @else
            <i class="fas {{ $icon }}"></i>
        @endif
    </span>
    <small class="node-number">{{ $number }}</small>
    <b>{{ $title }}</b>
    <small class="node-badge">{{ $step['label'] }}</small>
</div>
