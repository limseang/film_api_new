@extends('layouts.master')
@section('title')
Dashboard
@endsection
@section('content')
<!-- Main charts -->
<div class="row">

    <!-- All runtimes -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{__('global.welcome_dashboard')}} </h5>
        </div>

        <div class="card-body">
            <p class="mb-3">
                {{__('global.welcome')}} <strong>{{Auth::user()->name}}</strong> {{__('global.come_back')}} CinemagicKH Admin..!
            </p>
        </div>
    </div>
</div>
@endsection