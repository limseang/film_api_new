@extends('layouts.master')
@section('title')
{{__('global.add_user')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_user')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <form action="{{route('user.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{old('name')}}" id="name" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="role_id">{{ trans('sma.role') }}</label>
                  <select id="role_id" class="{{ config('setup.input_select2') }}" name="role_id" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($role as $value)
                      <option value="{{ $value->id }}" {{old('role_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                <div class="mb-3">
                  <label for="email">{{ __('sma.email')}} </label>
                  <input type="email" name="email" value="{{old('email')}}" class="form-control" id="email" required>
                  <span class="invalid-feedback">
                    {{trans('setting.the_field_is_required')}}
                    </span>
                </div>
                <div class="mb-3">
                  <label for="name" id="label_phone">{{__('sma.phone_number')}}</label>
                  <input type="text" name="phone_number" value="{{old('phone_number')}}" class="form-control" id="phone_number" >
                  <span class="invalid-feedback">
                      The field is required.
                    </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="user_type">{{ trans('sma.user_type') }}</label>
                  <select id="user_type" class="{{ config('setup.input_select2') }}" name="user_type" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($userType as $value)
                      <option value="{{ $value->id }}" {{old('user_type') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="password">{{ trans('sma.password') }}</label>
                  <input type="password" class="form-control" name="password" id="password" required>
                  <span class="invalid-feedback">
                    {{trans('setting.the_field_is_required')}}
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="password_confirmation">{{ trans('sma.confirm_password') }}</label>
                  <input type="password" class="form-control"  name="password_confirmation" required  id="confirm_password">
                  <span class="confirm-message">
                  </span>

                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                  <div class="mb-3">
                    <label class="form-label" for="language">{{ trans('sma.language') }}</label>
                    <select id="language" class="{{ config('setup.input_select2') }} form-select" name="language" required="">
                        <option value="">{{ __('global.please_select') }}</option>
                        <option value="km" {{old('language') == 'km' ? 'selected':''}}>{{ __('sma.khmer') }}</option>
                        <option value="en" {{old('status') == 'en' ? 'selected':''}}>{{ __('sma.english') }}</option>
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
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
                      <p class="fw-semibold">{{trans('sma.image')}}</p>
                    <input type="file" class="file-input-caption2" name="avatar">
                    </div>
                  
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-primary mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="btn btn-success mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
                </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
  </div>

  @endsection
