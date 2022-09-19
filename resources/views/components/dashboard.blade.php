@props([
    'pageTitle'   => null,
    'title'       => null,
    'description' => null,
])
<x-layout {{$attributes->merge(['class' => 'bg-lighter has-sidebar'])}} :title="$pageTitle">
    <x-slot name="styles">
        {{ $styles ?? null }}
    </x-slot>

    <x-dashboard.sidebar />

    <div class="nk-wrap">
        <x-dashboard.navbar />


        <div class="nk-content">
            <div class="nk-content pt-0">
                <div class="container-fluid">
                    <div class="nk-content-inner">
                        <x-dashboard.widgets.alert />

                        <x-dashboard.header :title="$title" :description="$description">
                            {{ $header ?? null }}
                        </x-dashboard.header>

                        <div class="nk-content-body">
                            {{ $slot }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{ $modals ?? null }}

        <x-dashboard.footer />
    </div>


    <x-slot name="scripts">
        <script src="{{ asset('assets/js/charts/gd-default.js?ver=2.9.0') }}"></script>
        <script src="{{ asset('assets/js/app.js') }}"></script>
        {{ $scripts ?? null }}
    </x-slot>
</x-layout>
