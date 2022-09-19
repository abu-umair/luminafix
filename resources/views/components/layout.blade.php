@props([
    'title'      => '',    // Site name and saperator will be appended
    'full_title' => '',    // Has precedence over `title` and will override it
    'has_header' => true,  // Wether to show default header, set false while using `header` slot
    'has_footer' => true,  // Wether to show default footer, set false while using `footer` slot
])
@php
    $__page_title = (CStr::isValidString($title) ? $title . ' &mdash; ' : '') . config('app.name', 'Laravel');
    $__full_title = CStr::isValidString($full_title) ? $full_title : $__page_title;
@endphp
<!DOCTYPE html>
<html lang="en_US" class="no-js">
<head>
    <meta charset="utf-8">
    <title>{!! $__full_title !!}</title>
    <meta name="author" content="Azzaz Khan" />
    <meta name="description" content="{{ config('app.description') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css?ver=2.9.0') }}" />
    <link rel="stylesheet" id="skin-default" href="{{ asset('assets/css/theme.css?ver=2.9.0') }}" />
    <link href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" rel="stylesheet" />
    {{ $styles ?? null }}
    <link rel="shortcut icon" href="{{ asset('images/cogni_green.webp') }}" />
</head>
<body {{ $attributes->merge(['class' => 'nk-body npc-general']) }}>
    <div class="nk-app-root">
        <div class="nk-main">
            {{ $slot }}
        </div>
    </div>
    <script src="{{ asset('assets/js/bundle.js?ver=2.9.0') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=2.9.0') }}"></script>
    {{ $scripts ?? null }}
</html>
