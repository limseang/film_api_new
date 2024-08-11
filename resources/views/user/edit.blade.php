@extends('layouts.master')
@section('title')
{{__('global.edit_artist')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_artist')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <form action="{{route('user.update',$user->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$user->name}}" id="name" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="role_id">{{ trans('sma.role') }}</label>
                  <select id="role_id" class="{{ config('setup.input_select2') }}" name="role_id" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($role as $value)
                      <option value="{{ $value->id }}" {{$user->role_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                <div class="mb-3">
                  <label for="email">{{ __('sma.email')}} </label>
                  <input type="email" name="email" value="{{$user->email}}" class="form-control" id="email" required>
                  <span class="invalid-feedback">
                    {{trans('setting.the_field_is_required')}}
                    </span>
                </div>
                <div class="mb-3">
                  <label for="name" id="label_phone">{{__('sma.phone_number')}}</label>
                  <input type="text" name="phone_number" value="{{$user->phone_number}}" class="form-control" id="phone_number" >
                  <span class="invalid-feedback">
                      The field is required.
                    </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="user_type">{{ trans('sma.user_type') }}</label>
                  <select id="user_type" class="{{ config('setup.input_select2') }}" name="user_type" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($userType as $value)
                      <option value="{{ $value->id }}" {{$user->user_type == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="password">{{ trans('sma.password') }}</label>
                  <input type="password" class="form-control" name="password" id="password">
                    <small class="text-danger">{{__('sma.keep_it_blank_to_use_the_old_password')}}</small>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="password_confirmation">{{ trans('sma.confirm_password') }}</label>
                  <input type="password" class="form-control"  name="password_confirmation"  id="confirm_password">
                  <span class="confirm-message">
                  </span>

                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                  <div class="mb-3">
                    <label class="form-label" for="language">{{ trans('sma.language') }}</label>
                    <select id="language" class="{{ config('setup.input_select2') }} form-select" name="language" required="">
                        <option value="">{{ __('global.please_select') }}</option>
                        <option value="km" {{$user->language == 'km' ? 'selected':''}}>{{ __('sma.khmer') }}</option>
                        <option value="en" {{$user->status == 'en' ? 'selected':''}}>{{ __('sma.english') }}</option>
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>
                  <div class="mb-3">
                    <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                    <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required="">
                        <option value="">{{ __('global.please_select') }}</option>
                        <option value="1" {{$user->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                        <option value="2" {{$user->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>

                    <div class="mb-3">
                      <p class="fw-semibold">{{trans('sma.image')}}</p>
                      <div class="file-input preview-update mb-1" style="padding: 4px; border;border-style: dashed; border-color:#f1f4f9; border-radius:0.375rem; border-width: cal(1px *2); border-color-translucent: rgba(0, 0, 0, 0.125)">
                        <div class=" file-drop-zone clearfix">
                          <div class="file-preview-thumbnails clearfix">
                            <div class="file-preview-frame krajee-default  kv-preview-thumb rotatable" id="thumb-1rad2qred4-148090_Screenshot_202024-06-06_20152921.png" data-fileindex="0" data-fileid="148090_Screenshot_202024-06-06_20152921.png" data-filename="Screenshot 2024-06-06 152921.png" data-template="image" data-zoom="">
                              <div class="kv-file-content">
                              <img src="{{$user->avatar_url}}" class="file-preview-image kv-preview-data" title="Screenshot 2024-06-06 152921.png" alt="Screenshot 2024-06-06 152921.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
                            </div>
                          </div>
                          </div>
                        </div>
                        <br>
                      </div>
                      <p class="fw-semibold">{{trans('sma.image')}}</p>
                    <input type="file" class="file-input-caption2" name="avatar">
                    </div>
                  
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-success mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
  </div>
  @section('scripts')
  <script>
    $(document).ready(function() {
      // when upload file displan none in class preview-update
      $('.file-input-caption2').change(function() {
        $('.preview-update').css('display', 'none');
      });
    });
  </script>
  @endsection
  @endsection
