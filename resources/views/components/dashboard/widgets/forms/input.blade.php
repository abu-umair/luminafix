@props([
    'id'           => '',
    'label'        => 'Field',
    'type'         => 'text',
    'wrapperClass' => 'col-md-12',
    'widgets'      => null
])
@php
    if (! CStr::isValidString($id))
        $id = sprintf(
            '%s__%s_',
            hash('crc32', rand(100, 1000)),
            hash('crc32b', microtime())
        );

    $acceptable_widgets = '/(password-(visibility))/';
    $loaded_widgets = collect([]);

    // Any unacceptable widget will not be included
    if (CStr::isValidString($widgets)): // `lorem|ipsum|dolor`
      // Split the widgets expression into multiple single widget names
      foreach (explode('|', $widgets) as $widget):
        // Only load available widgets and also make sure only one occurrence
        // of a widget is loaded
        if (
            preg_match($acceptable_widgets, $widget) &&
            !in_array($widget, $loaded_widgets->toArray())
        )
          $loaded_widgets->push($widget);
      endforeach;
    endif;

    $__wrapper_classes = [
        $wrapperClass => true,
        'has-widgets' => $loaded_widgets->count() > 0,
    ];

    $loaded_widgets
        ->each(function (string $widget) use (&$__wrapper_classes) {
            $__wrapper_classes[$widget . '-widget'] = true;
        });
@endphp
<div class="form-group {{ CStr::classes($__wrapper_classes) }}">
    <label class="form-label" for="{{ $id }}InputField">{{ $label }}</label>
    <div class="form-control-wrap">
        @if ($type === 'textarea')
            <textarea
                {{ $attributes->merge(['class' => 'form-control']) }}
                id="{{ $id }}TextareaField"
            >{{ $slot }}</textarea>
        @else
        <input
            {{ $attributes->merge(['class' => 'form-control']) }}
            type="{{ $type }}"
            id="{{ $id }}InputField"
        >
        @endif
         @if (preg_match('/(text|password)/', $type) && $loaded_widgets->contains('password-visibility'))
            <span
                class="password-visibility-widget"
                style="
                    position: absolute;
                    top: calc(50% + 2px);
                    right: 10px;
                    cursor: pointer;
                    transform: translateY(-50%);
                "
                data-toggle="password"
                data-target="#{{ $id }}InputField"
                title="Show password"
            >
            <i class="fas fa-eye" aria-hidden="true"></i>
        </span>
        @endif
    </div>
</div>
