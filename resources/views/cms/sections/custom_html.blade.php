@php
    $c = $section->content_json ?? [];
@endphp
<section class="py-4">
    <div class="container">
        {!! $c['html'] ?? '' !!}
    </div>
</section>
