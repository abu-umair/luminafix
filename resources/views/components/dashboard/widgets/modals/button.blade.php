@props([
    'for'   => 'create',
    'icon'  => 'plus',
    'label' => 'Create Resource'
])
<div class="toggle-wrap nk-block-tools-toggle">
    <a
        {{ $attributes->merge(['class' => 'btn btn-primary']) }}
        data-toggle="modal"
        data-target="#{{ $for }}ResourceModal"
        style="color: white"
    >
        <em class="icon ni ni-{{ $icon }}"></em>
        <span>{{ $label }}</span>
    </a>
</div>
