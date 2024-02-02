@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Users', 'url' => '#']];
@endphp
@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <div class="row">
        <div class="col-md-12 text-right py-2">
            <a href="{{ route('user.add') }}" class="btn btn-primary  py-3" style="float: right">Add User</a>
        </div>
        <div class="col-md-12 mx-auto">
            <div id="expbuttons"></div>
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <!--begin::Body-->
                <div class="card-body pt-5">
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
                            @foreach ($table_data as $data)
                                <tr>
                                    <td class="text-start">{{ $loop->iteration }}</td>
                                    @foreach ($table_fields as $key => $value)
                                        <td class="text-start">
                                            @if ($key == 'action')
                                                @include('htmls.action', [
                                                    'action' => $actions,
                                                    'id' => $data->id??null,
                                                    'dropdown' => true,
                                                ])
                                            @else
                                                {{ $data->$key }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Body-->
            </div>
        </div>

    @endsection

    @section('js')

        <script>
            $(document).ready(function() {

                $('body').on('click', '.importreport', function(e) {
                    e.preventDefault();
                    $('.loading').show();
                    var url = $(this).attr('href');
                    var data = '';
                    sendAjax(url).then(t => {
                        console.log(t);
                        if (t.status == 'success') {
                            toastr.success(t.message);
                        } else {
                            toastr.error(t.message);
                        }
                    }).catch(t => {
                        toastr.error('Something went wrong');
                    }).finally(t => {
                        $('.loading').hide();
                    });


                })
            })
        </script>

        {{-- <script>
            $("#kt_datatable").DataTable();
            var buttons = new $.fn.dataTable.Buttons('#kt_datatable', {
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            }).container().appendTo($('#expbuttons'));

            myworker = null;
            if ('serviceWorker' in navigator) {
                //{{ public_path() }}
                navigator.serviceWorker.register('../worker/worker.js');

                myworker = new Worker('../worker/worker.js');

                myworker.onmessage = function(e) {
                    if (typeof e.data == 'object') {
                        if (e.data.action == 'message') {
                            $('.loader-message').html(e.data.text);
                        }
                    }
                }
            }
            window.addEventListener('beforeunload', function(e) {

                if (myworker) {
                    myworker.terminate();
                }
            });
        </script> --}}
    @endsection
