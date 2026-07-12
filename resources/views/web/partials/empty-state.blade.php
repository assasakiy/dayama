@props(['message' => 'Nothing found.', 'icon' => null])
<div class="flex flex-col items-center justify-center py-16 text-center">
    @if ($icon)
        <div class="w-14 h-14 mb-4 rounded-full bg-surface-muted flex items-center justify-center">
            {!! $icon !!}
        </div>
    @endif
    <p class="text-muted-foreground text-sm">{{ $message }}</p>
</div>
