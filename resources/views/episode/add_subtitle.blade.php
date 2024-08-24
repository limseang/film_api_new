@extends('layouts.master')
@section('title')
{{__('sma.add_subtitle')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
            <div class="d-flex justify-content-between align-content-center">
                <h6 class="card-title text-success text-bold">
                    <i class="ph-file-plus"></i>
                    &nbsp;  &nbsp;<span>{{__('sma.add_subtitle')}}
                </h6>
                <button type="button" class="btn btn-flat-success btn-sm rounded-pill p-2">
                    <i class="ph-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-12 p-10">
                <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.film_name')}}:</span>&nbsp; {{$film->title}}</p>
                <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode_name')}}:</span>&nbsp;{{$episode->title}}</p>
                <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode')}}:</span>&nbsp;{{$episode->episode}}</p>
            </div>
            <div class="col-12 col-lg-12 p-10">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th> {{trans('sma.no')}} </th>
                            <th> {{trans('sma.subtitle')}} </th>
                            <th> {{trans('sma.file')}} </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
          </div>
        </div>
        </div>
    </div>
  </div>
@endsection