@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => $page_route, 'url' => '#']];
@endphp
@extends('layouts.app')
@section('title', $page_title ?? 'No title')
@section('page-title', $page_title ?? 'No title')

@section('content')
    <div class="row">
        <div class="col-md-12 text-right py-2">

                <a href="{{ route($page_route . '.add') }}" class="btn btn-primary  py-3" style="float: right">Add/Update
                    {{ $page_title ?? 'Add New' }}</a>

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
                            <tr>
                                <td class="text-start">1</td>
                                 @foreach ($table_fields as $key => $value)
                                        <td class="text-start">
                                            @if ($key == 'action')
                                                @include('htmls.action', [
                                                    'action' => $actions,
                                                    'id' => $data->id ?? null,
                                                    'dropdown' => true,
                                                ])
                                            @else
                                             @foreach ($table_data as $data)
                                                  @if($data->key == $key)
                                                  {{ $data->value }}
                                                  @endif
                                              @endforeach
                                            @endif
                                        </td>
                                    @endforeach

                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--end::Body-->
            </div>
        </div>

    @endsection

    @section('js')

    @endsection
