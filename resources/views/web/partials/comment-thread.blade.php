@props(['comments'])
@if ($comments->count())
    <div class="space-y-4">
        @foreach ($comments as $comment)
            @include('web.partials.comment-item', ['comment' => $comment])
        @endforeach
    </div>
@endif
