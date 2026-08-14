@props(['name', 'label' => false, 'disabled' => false, 'isRequired' => false, 'hint' => null, 'searchable' => true])

@php
    $hasError = $errors->has($name);
    $selectClass = $searchable ? 'select-field' : '';
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

    <select name="{{ $name }}" id="{{ $name }}" @disabled($disabled)
        {{ $attributes->merge(['class' => trim("form-control {$selectClass} " . ($hasError ? 'is-invalid' : ''))]) }}
        style="width: 100%;">
        {{ $slot }}
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if ($hint)
        <small class="form-text text-muted">{{ $hint }}</small>
    @endif
</div>
