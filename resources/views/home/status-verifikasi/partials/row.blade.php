<div class="verification-row">
    <div class="row-title"><span class="row-icon"><i class="fas {{ $icon }}"></i></span><div><b>{{ $title }}</b><small>{{ $subtitle }}</small></div></div>
    <span class="row-state {{ $step['state'] }}">{{ $step['label'] }}</span>
    <span class="row-date">
        @if($step['date'] instanceof \DateTimeInterface){{ $step['date']->translatedFormat('d M Y, H:i') }}@else - @endif
    </span>
    @if($url)<a href="{{ $url }}" class="row-link" title="Buka halaman"><i class="fas fa-chevron-right"></i></a>@else<span></span>@endif
    @if($step['note'])<div class="row-note"><i class="fas fa-exclamation-triangle mx-2"></i><b>Catatan Admin:</b> {{ $step['note'] }} @if($url)<a href="{{ $url }}" class="float-right">Perbaiki Sekarang</a>@endif</div>@endif
</div>
