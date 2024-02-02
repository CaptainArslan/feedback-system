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
            <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Mark Attendance</button>
        </div>
    </div>

    <div class="col-md-12 mx-auto showtable d-none">
        <div id="expbuttons">
            <a class="btn btn-success" onclick='selectAllPermissions(true)'> Check All</a>
            <a class="btn btn-danger" onclick='selectAllPermissions(false)'> Uncheck All</a>
        </div>
        <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
            <!--begin::Body-->
            <div class="card-body pt-5">
                <table id="kt_datatable" class="table table-row-bordered gy-5 ">
                    <thead>
                        <tr class="fw-semibold fs-6 text-muted">
                            <td class="text-start"> Id </td>
                            <td class="text-start"> Student Name</td>
                            <td class="text-start"> Current attendance Count</td>
                            <td class="text-start"> Last Attendance Date</td>
                        </tr>
                    </thead>
                    <tbody class="student-checkboxes">
                    </tbody>
                </table>
            </div>
            <!--end::Body-->
        </div>
    </div>
</form>
