@props([
    'title'       => null,
    'description' => null,
])
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        @if (CStr::isValidString($title) || CStr::isValidString($description))
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{ $title }}</h3>
                <div class="nk-block-des text-soft">
                    <p>{{ $description }}</p>
                </div>
            </div>
        @endif

        <div class="nk-block-head-content d-flex">
            {{ $slot }}
        </div>

    </div>
</div>
