@props(['name', 'disabled' => false, 'label' => false, 'isRequired' => false, 'icon' => null, 'hint' => null])

@php
    $hasError = $errors->has($name);
@endphp

<div class="form-group">
    @if ($label)
        <label for="{{ $name }}">
            {{ $label }}
            @if ($isRequired)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if ($icon)
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-{{ $icon }}"></i></span>
            </div>
            <input @disabled($disabled) name="{{ $name }}" id="{{ $name }}"
                {{ $attributes->merge(['class' => 'form-control ' . ($hasError ? 'is-invalid' : '')]) }}>
            @if ($hasError)
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endif
        </div>
    @else
        <input @disabled($disabled) name="{{ $name }}" id="{{ $name }}"
            {{ $attributes->merge(['class' => 'form-control ' . ($hasError ? 'is-invalid' : '')]) }}>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif

    @if ($hint)
        <small class="form-text text-muted">{{ $hint }}</small>
    @endif
</div>
