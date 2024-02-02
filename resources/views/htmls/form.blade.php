@php
    $fields = get_fields(get_defined_vars());
@endphp

<form class="form-control" method="{{ $fields['method'] }}" action="{{ $fields['action'] }}" enctype="multipart/form-data">
    @if ($fields['method'] !== 'GET')
        @csrf
    @endif
    <div class="row">

        @foreach ($fields as $field)
            @if (is_array($field))
                @include('htmls.form-input', $field)
            @endif
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-12" style="text-align: right !important">
            <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Save Changes</button>
        </div>
    </div>
</form>
