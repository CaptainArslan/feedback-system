@php
$breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => $page_title, 'url' => $page_route]];
@endphp
@extends('layouts.app')
@section('title', $page_title ?? 'No title')
@section('page-title', $page_title ?? 'No title')

@section('content')
<div class="row">
    <div class="col-md-12 text-right py-2">
        <a href="{{ route($page_route . '.add') }}" class="btn btn-primary  py-3" style="float: right">Add
            {{ $page_title ?? 'Add New' }}</a>
    </div>
    <div class="col-md-12 mx-auto">
        <div id="expbuttons"></div>
        <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
            <!--begin::Body-->
            <div class="card-body pt-5">
                <div class="table-responsive">
                    <table id="kt_datatable" class="table table-row-bordered gy-5">
                        <thead>
                            <tr class="fw-semibold fs-6 text-muted">
                                <td class="text-start"> Id </td>
                                @foreach ($table_fields as $field)
                                <td class="text-start">{{ $field }}</td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>

                    </table>
                </div>
            </div>
            <!--end::Body-->
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#kt_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($page_route . '.list') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                @foreach($table_fields as $key => $value) {
                    data: '{{ $key }}',
                    name: '{{ $key }}',
                    @if($key === 'action' || $key === 'Action')
                    searchable: false,
                    orderable: false
                    @endif
                },
                @endforeach
            ]
        });



        $('body').on('click', '.add_comment', function(e) {
            e.preventDefault();
            var data = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
            var id = data.id; // Assuming your row data has an 'id' property

            // Create your form HTML
            var formHtml = `<form class="form-inline" action="{{ route('save.comment') }}" method="post">
                                <input type="hidden" name="feedback_id" class="feedback_id" value="${id}">
                                <div class="form-group mx-sm-3 mb-2">
                                    <label for="inputPassword2" class="sr-only">comment</label>
                                    <input type="comment" class="form-control" id="inputcomment2" placeholder="comment">
                                </div>
                                <button type="submit" class="btn btn-primary mb-2">comment</button>
                            </form>`;

            // Add the form HTML to the current row
            var currentRow = $(this).closest('tr');
            var tdCount = $(currentRow).children('td').length;
            $(currentRow).after(`<tr class="comment-row"><td colspan="${tdCount}">${formHtml}</td></tr>`);
        });

    });
</script>
@endsection