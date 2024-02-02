@php
    $name='';
@endphp
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
@foreach ($breadcrumb as $ar)

            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href={{ $ar['url'] }}>{{ $ar['name'] }}</a></li>
            @if($loop->last)
            @php
                $name = $ar['name']
            @endphp

            @endif
@endforeach
  </ol>
          <h6 class="font-weight-bolder mb-0">{{ $name }}</h6>
