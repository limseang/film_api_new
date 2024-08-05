<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="ltr" data-color-theme="light" >
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<title>@yield('title',config('app.name')) |CinemagicKH</title>
    <meta name="description" content="Limitless - Responsive Web Application Kit by Eugene Kopyov">
    <meta name="author" content="Eugene Kopyov">
    @include('layouts.includes.header')

</head>

<body class="{{ App::getLocale() == 'km' ? 'language_km': ''}}">

	<!-- Main navbar -->
	@include('layouts.includes.header-navbar')
	<!-- /main navbar -->


	<div class="page-content">

        <!-- Main sidebar -->
        @include('layouts.includes.right-sidebar')
        <!-- /main sidebar -->
    
    
        <!-- Main content -->
        <div class="content-wrapper">
    
            <!-- Inner content -->
            <div class="content-inner">
    
                <!-- Page header -->
                <div class="page-header page-header-light shadow">
                  @include('layouts.includes.breadcrumb')
                </div>
                <!-- /page header -->
    
                <div class="modal fade" id="view-img">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-body">
                          <div class="show-img"></div>
                        </div>
                      </div>
                      <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                  </div>
                <!-- Content area -->
                <div class="content">
                @include('layouts.includes.alert-message-exception')
                @yield('content')
                    <!-- /dashboard content -->
                </div>
                <!-- /content area -->
                <!-- Footer -->
                @include('layouts.includes.footer')
                <!-- /footer -->
    
            </div>
            <!-- /inner content -->
    
        </div>
        <!-- /main content -->
    
    </div>

	@include('layouts.includes.left-config')
	<!-- /demo config -->


      <!-- Core Message Alert -->
      @include('layouts.includes.alert-message')
      <!-- ./ Core Message Alert -->
  
      <!--Boostrap Modal -->
      @yield('modal-alert')

</body>
</html>