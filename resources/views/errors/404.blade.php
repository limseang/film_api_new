@extends('layouts.master-without-content')
@section('content')

<!-- Content area -->
<div class="content d-flex justify-content-center align-items-center">

    <!-- Container -->
    <div class="flex-fill">

        <!-- Error title -->
        <div class="mb-4 text-center">
            <img src="{{URL::asset('img/404.svg')}}" class="mb-3" height="230" alt="">
            <h6 class="w-md-25 mx-md-auto">Oops, an error has occurred. <br> The resource requested could not be found on this server.</h6>
        </div>
        <!-- /error title -->


        <!-- Error content -->
        <div class="text-center">
            <a href="#" class="btn btn-primary">
                <i class="ph-house me-2"></i>
                @lang('global.abort404')
            </a>
        </div>
        <!-- /error wrapper -->

    </div>
    <!-- /container -->

</div>
<!-- /content area -->

@endsection
