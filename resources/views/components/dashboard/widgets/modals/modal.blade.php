@props([
    'title'      => "Create Resource",
    'url'        => '#',
    'method'     => 'POST',
    'action'     => 'Create',
    'id'         => 'create',
    'noHeader'   => false,
    'formMethod' => 'POST'
])
@php
    if (! CStr::isValidString($id))
        $id = sprintf(
            '%s__%s_',
            hash('crc32', rand(100, 1000)),
            hash('crc32b', microtime())
        );
@endphp
<div
    class="modal fade zoom"
    id="{{ $id }}ResourceModal"
    tabindex="-1"
    aria-modal="true"
    role="dialog"
>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Header -->
            @unless ($noHeader)
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <a
                        href="#"
                        class="close"
                        data-dismiss="modal"
                        data-target="#{{ $id }}ResourceModal"
                        aria-label="Close"
                    >
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
            @endunless

            <!-- Modal content -->
            <div class="modal-body">
                <form
                    action="{{ $url }}"
                    method="{{ $formMethod }}"
                    enctype="multipart/form-data"
                    id="{{ $id }}ResourceModalForm"
                    class="form-validate is-alter"
                    novalidate="novalidate"
                >
                    @if (is_string($formMethod) && strtolower($formMethod) !== 'get')
                        @csrf
                        @method($method)
                    @endif
                    <div class="row">

                        <!-- Input fields -->
                        {{ $slot }}

                        <div class="form-group col-md-12 text-right">
                            <button
                                type="buton"
                                class="btn btn-lg"
                                data-dismiss="modal"
                                data-target="#{{ $id }}ResourceModal"
                            >
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-lg btn-primary">
                                {{ $action }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
