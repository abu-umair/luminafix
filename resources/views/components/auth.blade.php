@props([
    'title'       => null,
    'pageTitle'   => null,
    'description' => null,
    'url'         => '#',
    'action'      => 'Submit'
])
<x-layout {{ $attributes->merge(['class' => 'bg-white pg-auth']) }} :title="$pageTitle">
    <div class="nk-wrap nk-wrap-nosidebar pt-3">
        <div class="nk-content">
            <!-- Auth content -->
            <div class="mx-auto nk-block-area nk-block-area-column nk-auth-container bg-white">

                <!-- Info button (mobile only) -->
                <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                    <a
                        href="#"
                        class="toggle btn-white btn btn-icon btn-light"
                        data-target="athPromo"
                    >
                        <em class="icon ni ni-info"></em>
                    </a>
                </div>

                <div class="nk-block nk-block-middle nk-auth-body">
                    <!-- Form header -->
                    @if (CStr::isValidString($title) || CStr::isValidString($description))
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h5 class="nk-block-title">{{ $title }}</h5>
                                <div class="nk-block-des">
                                    <p>{{ $description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Authentication form -->
                    <form
                        action="{{ $url }}"
                        method="POST"
                        class="form-validate is-alter"
                        autocomplete="off">

                        @csrf
                        {{ $slot }}

                        <div class="form-group">
                            <button class="btn btn-lg btn-primary btn-block">{{ $action }}</button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="nk-block nk-auth-footer">
                    <div class="mt-3">
                        <p>
                            &copy;
                            <a href="{{ url('/') }}" target="_blank">{{ config('app.name') }}</a>
                            &mdash; All rights reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
