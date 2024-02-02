@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Add Company credentials', 'url' => '#']];
@endphp
@extends('layouts.app')

@section('css')

@endsection
@section('title', 'Add Company credentials')
@section('page-title', 'Add Company credentials')
@section('content')

    <div class="row">
        <div class="col-md-8 mx-auto">
            <h4 class="py-5">
                Add Company's Smart Credentials
            </h4>
            <p class=" text-danger font-weight-bold"><strong class="text-danger">NOTE : </strong> Please use the correct
                credentials as these will be use to import the report from smart credit. wrong credentials
                will result in failure to import the report from smart credit.
            </p>
            @include('htmls.form', $form_fields, [
                'action' => route('setting.save'),
                'method' => 'POST',
            ])

        </div>
    </div>


@endsection

@section('js')

    <script>
        $(document).ready(function() {
            $('body').on('change', '.selectcompany', function() {
                var company_id = $(this).val();
                $('.loading').show();
                var url = "{{ route('getusercredentials', ':id') }}";
                url = url.replace(':id', company_id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        if (data.status == 'error') {
                            toastr.error("credentials not found");
                        }
                        $('[name="smart_credit_username"]').val(data.username);
                        $('[name="smart_credit_password"]').val(data.password);
                    },
                    complete: function() {
                        $('.loading').hide();
                    }
                });
            });
        });
    </script>

@endsection
