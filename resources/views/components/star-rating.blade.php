@props(['value' => 0, 'max' => 5])
<span class="stars">
    @for($i = 1; $i <= $max; $i++)
        <i class="bi bi-star-fill {{ $i <= round($value) ? '' : 'empty' }}"></i>
    @endfor
    <small class="text-muted ms-1">{{ number_format($value, 1) }}</small>
</span>