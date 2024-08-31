@extends('layouts.master')
@section('title')
{{__('sma.edit_version')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.update')}}</span>
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form method="POST" action="{{route('version.update', $version->id)}}" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="version">{{__('sma.version')}}</label>
                  <input type="text" class="form-control" name="version" value="{{$version->version}}" id="version" placeholder="Enter version" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="platform">{{__('sma.platform')}}</label>
                  <input type="text" class="form-control" name="platform" value="{{$version->platform}}" id="platform" placeholder="Enter platform" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                <label class="form-label">{{ trans('global.publish') }}</label>
                <select id="publish" class="{{ config('setup.input_select2') }}" name="status">
                    <option value="">{{ __('global.please_select') }}</option>
                    <option value="1" {{$version->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                    <option value="2" {{$version->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                </select>
                </div>
                  <br>
                  <div class="d-flex align-items-center">
                    <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                  </div>
             </form>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
  @endsection
