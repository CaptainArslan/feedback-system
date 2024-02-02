@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Settings', 'url' => '#']];
@endphp
@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
@if(is_role() == 'admin' )
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
@else
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
                    $href='https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri=' . route('authorization.gohighlevel.callback') . '&client_id=' . supersetting('crm_client_id') . '&scope=calendars.readonly calendars/events.write calendars/groups.readonly calendars/groups.write campaigns.readonly conversations.readonly conversations.write conversations/message.readonly conversations/message.write contacts.readonly contacts.write forms.readonly forms.write links.write links.readonly locations.write locations.readonly locations/customValues.readonly locations/customValues.write locations/customFields.readonly locations/customFields.write locations/tasks.readonly locations/tasks.write locations/tags.readonly locations/tags.write locations/templates.readonly medias.readonly medias.write opportunities.readonly opportunities.write surveys.readonly users.readonly users.write workflows.readonly snapshots.readonly oauth.write oauth.readonly calendars/events.readonly calendars.write businesses.write businesses.readonly';
                        $description = 'Connect to CRM';
                        if(is_connected()){
                            $description  = 'Already Connected to CRM!';
                        }
                    @endphp
                    @include('htmls.elements.anchor', ['href' => $href,'description' => $description])
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
