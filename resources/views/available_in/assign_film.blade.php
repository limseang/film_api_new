@extends('layouts.master')
@section('title')
{{__('sma.assign_film')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-video"></i>
              &nbsp;  &nbsp;<span>{{__('sma.assign_film')}}</span>
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form method="POST" action="{{route('available_in.store_film', $available_in->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control bg-primary bg-opacity-10 text-primary" name="name" value="{{$available_in->name}}" id="name" placeholder="Enter name" required readonly>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="film_id[]">{{ trans('sma.film') }}</label>
                  <select id="film_id" class="{{ config('setup.input_select2') }}" name="film_id[]" multiple  required data-placeholder="{{ __('global.please_select') }}">
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($films as $value)
                      <option value="{{ $value->id }}" 
                          @if(old('film_id') && in_array($value->id, old('film_id'))) 
                              selected 
                          @elseif(in_array($value->id, $available_film ?? [])) 
                              selected 
                          @endif
                      >
                          {{ $value->title }}
                      </option>
                  @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                </div>
                 </div>
                  <br>
                  <div class="d-flex align-items-center">
                    <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  </div>
             </form>
      </div>
      </div>
    </div>
  </div>
  @section('scripts')
  <script>
  </script>
  @endsection
  @endsection
