@php
    $required = $field['required'] ?? false;
    if ($required) {
        $required = 'required';
    }

    $dropify = $field['type'] === 'file' ? 'inputimage' : '';
@endphp

@if ($field['type'] == 'select')
    @php
        $options = $field['options'] ?? [];
        $sel_type = $field['is_select2'] ? 'select2' : '';
        $is_multiple = $field['is_multiple'] ? 'multiple' : '';
    @endphp
    <div class="col-lg-{{ $field['col'] ?? '12' }} fv-row fv-plugins-icon-container my-2">
        <label for="{{ $field['id'] ?? '' }}" class="py-2">{{ $field['label'] ?? '' }}</label>
        <select name="{{ $field['name'] }}"
            class="form-control form-control-lg form-control-solid  {{ $field['class'] ?? '' }}  mb-3 mb-lg-0"
            {{ $required }} data-control="{{ $sel_type }}" {{ $is_multiple }}>
            @foreach ($options as $key => $option)
                <option value="{{ $key }}" {{ $key == $field['value'] ? 'selected' : '' }}>
                    {{ $field['name'] == 'company' ? full_name($key) : $option }}
                </option>
            @endforeach
        </select>
    </div>
@elseif($field['type'] == 'textarea')
    <textarea name="{{ $field['name'] }}"
        class="form-control form-control-lg form-control-solid  {{ $field['class'] ?? '' }}  mb-3 mb-lg-0"
        {{ $required }}>{{ $field['value'] }}</textarea>
@else
    <div
        class="col-lg-{{ $field['col'] ?? '12' }} fv-row fv-plugins-icon-container my-2 @if ($field['name'] == 'service_id') d-none @endif">
        <label for="{{ $field['id'] ?? '' }}" class="py-2">{{ $field['label'] ?? '' }}</label>
        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
            @if (!is_null($field['id'] ?? null)) id="{{ $field['class'] }}" @endif
            class="form-control form-control-lg form-control-solid  {{ $field['class'] ?? '' }} {{ $dropify }} mb-3 mb-lg-0"
            placeholder="{{ $field['placeholder'] }}" value="{{ $field['value'] }}" {{ $field['extra'] ?? '' }}
            {{ $required }} data-default-file="{{ asset($field['value']) }}">
        <div class="fv-plugins-message-container invalid-feedback"></div>
    </div>
    @if ($field['type'] === 'file')
<img class="previewimage" src="{{ asset($field['value']) ?? asset('assets/media/svg/avatars/blank.svg') }}"
    style="width: 180px; height: 180px; object-fit: contain; border-radius: 18%; margin-bottom: 0.8%;">

    @endif
@endif
