@props(['icon', 'class' => 'w-5 h-5'])

@php
    $type = 'lucide';
    $val = $icon;

    if (str_starts_with($icon, 'lucide:')) {
        $val = substr($icon, 7);
    } elseif (str_starts_with($icon, 'url:')) {
        $type = 'url';
        $val = substr($icon, 4);
    }
@endphp

@if($type === 'lucide')
    <i data-lucide="{{ strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $val)) }}" class="{{ $class }}"></i>
@elseif($type === 'url')
    <img src="{{ $val }}" alt="icon" class="{{ $class }} object-contain" />
@endif
