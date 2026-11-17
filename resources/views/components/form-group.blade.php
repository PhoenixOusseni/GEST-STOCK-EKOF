{{-- Form Group Component --}}
<div class="form-group {{ $class ?? '' }}">
    @if ($label ?? null)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if ($required ?? false)
                <span class="required">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
            @if ($required ?? false) required @endif
            @if ($rows ?? null) rows="{{ $rows }}" @endif
            placeholder="{{ $placeholder ?? '' }}"
            {!! $attributes->merge([]) !!}
        >{{ old($name, $value ?? '') }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-select{{ $errors->has($name) ? ' is-invalid' : '' }}"
            @if ($required ?? false) required @endif
            {!! $attributes->merge([]) !!}
        >
            @if ($placeholder ?? null)
                <option value="">{{ $placeholder }}</option>
            @endif
            @if ($options ?? null)
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @if (old($name, $value ?? null) == $optionValue) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @endif
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type ?? 'text' }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
            value="{{ old($name, $value ?? '') }}"
            placeholder="{{ $placeholder ?? '' }}"
            @if ($required ?? false) required @endif
            {!! $attributes->merge([]) !!}
        >
    @endif

    @if ($errors->has($name))
        <div class="form-text text-danger">
            {{ $errors->first($name) }}
        </div>
    @elseif ($help ?? null)
        <div class="form-text">
            {{ $help }}
        </div>
    @endif
</div>
