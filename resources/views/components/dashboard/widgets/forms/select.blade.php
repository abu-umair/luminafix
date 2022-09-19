@props([
    'id'           => '',
    'label'        => 'Field',
    'wrapperClass' => 'col-md-12'
])
@php
    if (! CStr::isValidString($id))
        $id = sprintf(
            '%s__%s_',
            hash('crc32', rand(100, 1000)),
            hash('crc32b', microtime())
        );
@endphp
<div class="form-group {{ $wrapperClass }}">
    <label class="form-label" for="{{ $id }}InputField">{{ $label }}</label>
    <div class="form-control-wrap">
        <select
            id="{{ $id }}SelectField"
            {{ $attributes->merge(['class' => 'form-select']) }}
        >
            {{ $slot }}
        </select>
    </div>
</div>
