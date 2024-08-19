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
                    <input type="file" class="file-input-caption-edit" name="avatar">
                    </div>
                  
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-primary mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
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
      var initialPreview = [
        "<img src='{{ $image['url'] }}' class='file-preview-image kv-preview-data' alt='{{ $image['name'] }}' title='{{ $image['name'] }}'>"
      ];
          // Buttons inside zoom modal
          const previewZoomButtonClasses = {
            rotate: 'btn btn-light btn-icon btn-sm',
            toggleheader: 'btn btn-light btn-icon btn-header-toggle btn-sm',
            fullscreen: 'btn btn-light btn-icon btn-sm',
            borderless: 'btn btn-light btn-icon btn-sm',
            close: 'btn btn-light btn-icon btn-sm'
        };

        // Icons inside zoom modal classes
        const previewZoomButtonIcons = {
            prev: document.dir == 'rtl' ? '<i class="ph-arrow-right"></i>' : '<i class="ph-arrow-left"></i>',
            next: document.dir == 'rtl' ? '<i class="ph-arrow-left"></i>' : '<i class="ph-arrow-right"></i>',
            rotate: '<i class="ph-arrow-clockwise"></i>',
            toggleheader: '<i class="ph-arrows-down-up"></i>',
            fullscreen: '<i class="ph-corners-out"></i>',
            borderless: '<i class="ph-frame-corners"></i>',
            close: '<i class="ph-x"></i>'
        };

        // File actions
        const fileActionSettings = {
            zoomClass: '',
            zoomIcon: '<i class="ph-magnifying-glass-plus"></i>',
            dragClass: 'p-2',
            dragIcon: '<i class="ph-dots-six"></i>',
            removeClass: '',
            removeErrorClass: 'text-danger',
            indicatorNew: '<i class="ph-file-plus text-success"></i>',
            indicatorSuccess: '<i class="ph-check file-icon-large text-success"></i>',
            indicatorError: '<i class="ph-x text-danger"></i>',
            indicatorLoading: '<i class="ph-spinner spinner text-muted"></i>'
        };
      // when upload file displan none in class preview-update
      $('.file-input-caption-edit').fileinput({
           browseLabel: 'Browse',
            browseIcon: '<i class="ph-file-plus me-2"></i>',
            removeIcon: '<i class="ph-x fs-base me-2"></i>',
            layoutTemplates: {
                icon: '<i class="ph-check"></i>'
            },
            // uploadClass: 'btn btn-light',
            browseClass: 'btn btn-info opacity-10',
            removeClass: 'btn btn-light',
            initialCaption: "No file selected",
            previewZoomButtonClasses: previewZoomButtonClasses,
            previewZoomButtonIcons: previewZoomButtonIcons,
            fileActionSettings: fileActionSettings,
            initialPreview: initialPreview,
            showCaption: true,
            dropZoneEnabled: true,
            showUpload: false,
            showRemove: false,
        });
    });
  </script>
  @endsection
  @endsection
