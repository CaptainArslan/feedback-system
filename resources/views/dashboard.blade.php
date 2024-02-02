@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')]]
@endphp
@extends('layouts.app')

@section('css')

@endsection
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')
      <div class="row ">
        <div class="col-md-4">
            <button type="button" class="btn bg-gradient-success btn-block mb-3" data-bs-toggle="modal" data-bs-target="#exampleModalMessage">
                Add FeedBack
              </button>
        </div>
        <div id="feedback-container">

        </div>
            <div class="modal fade" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Feedback to Admin</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action={{route('save.feedback.form')}} method="POST">
                        @csrf
                        <div class="form-group">
                          <label for="title">Title</label>
                          <input type="text" name='title'  class="form-control" id="title" placeholder="Please add Title" required>
                        </div>
                        <div class="form-group">
                          <label for="category_id">Category</label>
                          <select class="form-control" name="category_id" id="category_id" required>
                            <option value=''>Choose</option>
                            @foreach($categories as $cat)
                            <option value={{$cat->id}}>{{$cat->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="description">Description</label>
                          <textarea class="form-control" name="description" id="description" rows="3"></textarea>

                        </div>
      
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn bg-gradient-primary">Send</button>
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </form>
                </div>
              </div>
            </div>

        
      </div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>  
 $(document).ready(function() {
    // Perform AJAX request
    $.ajax({
      url: "{{route('get.feedback')}}",
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        $.each(data.feedback, function(index, feedback) {
            console.log(data);
        console.log(feedback)
          var cardHtml = `
            <div class="card">
              <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">

              </div>
              <div class="card-body pt-2">
                <span class="text-gradient text-primary text-uppercase text-xs font-weight-bold my-2">${feedback.category.name}</span>
                <a href="javascript:;" class="card-title h5 d-block text-darker">${feedback.title}</a>
                <p class="card-description mb-4">${feedback.description}</p>
                <div class="author align-items-center">
                    ${feedback.user.image ? `<img src="${feedback.user.image}" alt="..." class="avatar shadow">`: 
                  
            `<svg xmlns="http://www.w3.org/2000/svg" style="width: 45px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xodm="http://www.corel.com/coreldraw/odm/2003" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 846.66 1058.325" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><defs><style type="text/css">
            
            .fil0 {fill:black}
            
            </style></defs><g><path class="fil0" d="M533.88 399.99c-28.3,28.29 -67.38,45.79 -110.55,45.79 -43.16,0 -82.25,-17.5 -110.54,-45.79 -28.29,-28.29 -45.79,-67.37 -45.79,-110.54 0,-43.17 17.5,-82.25 45.79,-110.54 28.29,-28.29 67.38,-45.79 110.54,-45.79 43.17,0 82.25,17.5 110.55,45.79 28.29,28.29 45.79,67.37 45.79,110.54 0,43.17 -17.5,82.25 -45.79,110.54zm-384.71 249.36l0 -6.58c0,-46.34 18.94,-88.46 49.46,-118.97 30.51,-30.52 72.63,-49.46 118.97,-49.46l29.98 0c8.98,0 16.78,5.05 20.71,12.46l55.04 83.72 56.26 -85.56c4.47,-6.81 11.91,-10.51 19.49,-10.52l0 -0.1 29.99 0c46.34,0 88.45,18.94 118.97,49.46 30.51,30.51 49.46,72.63 49.46,118.97l0 6.58c50.69,-61.42 81.14,-140.17 81.14,-226.02 0,-98.12 -39.77,-186.95 -104.07,-251.24 -64.29,-64.3 -153.12,-104.07 -251.24,-104.07 -98.11,0 -186.94,39.77 -251.24,104.07 -64.29,64.29 -104.06,153.12 -104.06,251.24 0,85.85 30.45,164.6 81.14,226.02zm308.95 174.64l-2.3 0.19c-1.17,0.1 -2.35,0.19 -3.53,0.27l-1.58 0.11 -2.97 0.19 -2.86 0.16 -1.73 0.09c-1.23,0.06 -2.46,0.12 -3.7,0.16l-1.31 0.05c-1.3,0.05 -2.6,0.09 -3.9,0.13l-0.94 0.02c-1.46,0.04 -2.92,0.06 -4.37,0.09l-0.63 0c-1.66,0.02 -3.31,0.04 -4.97,0.04 -1.65,0 -3.31,-0.02 -4.96,-0.04l-0.63 0c-1.46,-0.03 -2.92,-0.05 -4.37,-0.09l-0.95 -0.02c-1.3,-0.04 -2.6,-0.08 -3.9,-0.13l-1.31 -0.05c-1.23,-0.04 -2.46,-0.1 -3.69,-0.16l-1.74 -0.09 -2.85 -0.16 -2.97 -0.19 -1.59 -0.11c-1.17,-0.08 -2.35,-0.17 -3.53,-0.27l-2.3 -0.19c-97.17,-8.32 -184.48,-51.2 -249.57,-116.3 -72.78,-72.77 -117.8,-173.32 -117.8,-284.36 0,-111.05 45.02,-211.59 117.8,-284.37 72.77,-72.78 173.31,-117.79 284.36,-117.79 111.05,0 211.59,45.01 284.37,117.79 72.78,72.78 117.79,173.32 117.79,284.37 0,111.04 -45.01,211.59 -117.79,284.36 -65.1,65.1 -152.41,107.98 -249.58,116.3zm-63.75 0.46c-1.17,-0.08 -2.35,-0.17 -3.53,-0.27l3.53 0.27zm125.64 -151.74c-12.94,0 -23.43,-10.49 -23.43,-23.43 0,-12.94 10.49,-23.43 23.43,-23.43l69.42 0c12.94,0 23.42,10.49 23.42,23.43 0,12.94 -10.48,23.43 -23.42,23.43l-69.42 0z"/></g><text x="0" y="861.66" fill="#000000" font-size="5px" font-weight="bold" font-family="'Helvetica Neue', Helvetica, Arial-Unicode, Arial, Sans-serif">Created by Tsundere Project</text><text x="0" y="866.66" fill="#000000" font-size="5px" font-weight="bold" font-family="'Helvetica Neue', Helvetica, Arial-Unicode, Arial, Sans-serif">from the Noun Project</text></svg>`}
                  <div class="name ps-3">
                    <span>${feedback.user.first_name + feedback.user.last_name }</span>
                    <div class="stats">
                      <small>Posted on ${new Date(feedback.created_at).toLocaleString(undefined, { day: 'numeric', month: 'long'})}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;

          // Append the card HTML to a container (replace '#feedback-container' with your actual container selector)
          $('#feedback-container').append(cardHtml);
        });
      },
      error: function(xhr, status, error) {
        console.error('Error fetching feedback:', error);
      }
    });
  });
    </script>
@endsection
