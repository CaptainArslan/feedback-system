@extends('layouts.app')
@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => $page_route, 'url' => '#']];
@endphp

@section('title', $page_title ?? 'No title')
@section('page-title', $page_title ?? 'No title')

@section('content')

    <div class="row">
        <div
            class="col-md-{{ isset($card_info['col']) ? $card_info['col'] : '12' }} {{ isset($card_info['extraclass']) ? $card_info['extraclass'] : '' }} mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    @include('htmls.form', $form_fields, [
                        'action' => route($page_route.'.save', ['id' => $id ?? null]),
                        'method' => 'POST',
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
