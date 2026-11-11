@php
    $email = config('portfolio.profile.email');
@endphp

<a class="postcard" href="#contact">
    <img
        class="postcard__stamp"
        src="{{ asset('images/postcard-stamp.jpg') }}"
        alt=""
        width="36"
        height="56"
        decoding="async"
    >
    <span>
        <span class="postcard__eyebrow">postcard</span>
        <span class="postcard__title">say hello</span>
        <span class="postcard__hint">{{ $email }}</span>
    </span>
</a>
