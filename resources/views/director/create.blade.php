@extends('layouts.master')
@section('title')
{{__('global.add_director')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_director')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('director.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{old('name')}}" id="name" placeholder="Enter name" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="know_for">{{__('sma.know_for')}}</label>
                  <input type="text" class="form-control" name="know_for" value="{{old('know_for')}}" id="know_for" placeholder="Enter know for">
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="nationality">{{ trans('sma.nationality') }}</label>
                  <select id="nationality" class="{{ config('setup.input_select2') }}" name="nationality" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($countries as $value)
                      <option value="{{ $value->id }}" {{old('nationality') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="birth_date">{{ trans('sma.birth_date') }}</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="ph-calendar"></i></span>
                      <input type="text" class="form-control datepicker-autohide" value="{{old('birth_date')}}" name="birth_date" placeholder="Please select date" required/>
                    </div>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="death_date">{{ trans('sma.death_date') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="ph-calendar"></i>
                    </span>
                    <input type="text" class="form-control datepicker-autohide2" value="{{old('death_date')}}" name="death_date" placeholder="Please select date">
                  </div>

                </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{old('status') == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{old('status') == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="biography">{{trans('sma.biography')}}</label>
                  <textarea rows="3" cols="3" name="biography" class="form-control" id="ckeditor_classic_prefilled2" required>
                    {{old('biography')}}
                  </textarea>
                </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.avatar_director')}}</p>
							  <input type="file" class="file-input-caption2" name="image">
                </div>
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="{{config('setup.button_opacity_success')}} mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  @endsection
