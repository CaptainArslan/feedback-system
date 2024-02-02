@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Settings', 'url' => '#']];
@endphp
@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    @include('htmls.form', $form_fields, [
                        'action' => route('setting.save'),
                        'method' => 'POST',
                    ])
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    <img src="{{ asset('crm.jpg') }}" alt="Logo" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    @php
                        $href = 'https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri=' . route('authorization.gohighlevel.callback') . '&client_id=' . setting('client_id',1) . '&scope=businesses.readonly businesses.write calendars.readonly calendars.write calendars/events.readonly calendars/events.write campaigns.readonly conversations.readonly conversations/message.readonly forms.readonly contacts.write contacts.readonly links.write links.readonly conversations/message.write locations.write locations.readonly locations/customValues.readonly locations/customValues.write locations/customFields.write locations/customFields.readonly locations/tasks.readonly locations/tasks.write locations/tags.readonly locations/tags.write locations/templates.readonly opportunities.readonly opportunities.write surveys.readonly users.readonly users.write workflows.readonly snapshots.readonly';
                        $description = 'Connect to GoHighLevel';
                        if(is_connected(1)){
                            $description  = 'Already Connected! Want to change?';
                        }
                    @endphp
                    @include('htmls.elements.anchor', ['href' => $href,'description' => $description])
                </div>
            </div>
        </div>
    </div>

@endsection
