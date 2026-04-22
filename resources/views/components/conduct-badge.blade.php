@props(['flag' => 'clean'])
@php
$map = [
    'clean'        => ['class' => 'badge-clean',       'icon' => 'shield-check',      'label' => 'Clean Record'],
    'minor_issue'  => ['class' => 'badge-pending',     'icon' => 'exclamation-circle', 'label' => 'Minor Issue'],
    'major_issue'  => ['class' => 'badge-rejected',    'icon' => 'exclamation-triangle','label' => 'Major Issue'],
    'blacklisted'  => ['class' => 'badge-blacklisted', 'icon' => 'ban',               'label' => 'Blacklisted'],
];
$info = $map[$flag] ?? $map['clean'];
@endphp
<span class="badge-nfer {{ $info['class'] }}">
    <i class="bi bi-{{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
</span>