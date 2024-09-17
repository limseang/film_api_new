@extends('layouts.master')
@section('title')
{{__('global.edit_cinema_branch')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_cinema_branch')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
            
              <form action="{{route('cinema_branch.update',$cinemaBranch->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$cinemaBranch->name}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="address">{{__('sma.address')}}</label>
                  <input type="text" class="form-control" name="address" value="{{$cinemaBranch->address}}" id="address" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="phone">{{__('sma.phone')}}</label>
                  <input type="text" class="form-control" name="phone" value="{{$cinemaBranch->phone}}" id="phone" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="show_type">{{__('sma.show_type')}}</label>
                  <input type="text" class="form-control" name="show_type" value="{{$cinemaBranch->show_type}}" id="show_type" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="show_type">{{__('sma.email')}}</label>
                  <input type="email" class="form-control" name="email" value="{{$cinemaBranch->email}}" id="email" placeholder="{{trans('sma.please_input')}}">
                </div>
                  <div class="mb-3">
                    <label class="form-label" for="cinema_id">{{ trans('sma.cinema') }}</label>
                    <select id="cinema_id" class="{{ config('setup.input_select2') }}" name="cinema_id" required>
                        <option value="">{{ __('global.please_select') }}</option>
                        @foreach($cinema as $value)
                        <option value="{{ $value->id }}" {{$cinemaBranch->cinema_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                        @endforeach
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{$cinemaBranch->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{$cinemaBranch->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                  <div class="mb-3">
                    {{-- Preview imag --}}
                    <p class="fw-semibold">{{trans('sma.image')}}</p>
                    <input type="file" class="file-input-caption-edit" name="image">
                  </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                  <div class="mb-3">
                    <label class="form-label" for="link">{{__('sma.link')}}</label>
                    <input type="text" class="form-control" name="link" value="{{$cinemaBranch->link}}" id="link" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="map_link">{{__('Map Link')}}</label>
                    <input type="text" class="form-control" name="map_link" value="{{$cinemaBranch->map_link}}" id="map_link" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="lat">{{__('Lat')}}</label>
                    <input type="text" class="form-control" name="lat" value="{{$cinemaBranch->lat}}" id="lat" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="lng">{{__('Lng')}}</label>
                    <input type="text" class="form-control" name="lng" value="{{$cinemaBranch->lng}}" id="lng" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="ticket_price">{{__('sma.ticket_price')}}</label>
                    <input type="text" class="form-control" name="ticket_price" value="{{$cinemaBranch->ticket_price}}" id="ticket_price" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="facebook">{{__('Facebook')}}</label>
                    <input type="url" class="form-control" name="facebook" value="{{$cinemaBranch->facebook}}" id="facebook" placeholder="{{trans('sma.please_input')}}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="instagram">{{__('Instagram')}}</label>
                    <input type="url" class="form-control" name="instagram" value="{{$cinemaBranch->instagram}}" id="instagram" placeholder="{{trans('sma.please_input')}}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="youtube">{{__('Youtube')}}</label>
                    <input type="url" class="form-control" name="youtube" value="{{$cinemaBranch->youtube}}" id="youtube" placeholder="{{trans('sma.please_input')}}">
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                  </div>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  @section('scripts')
  <script>
    $(document).ready(function() {
      var initialPreview = [
        "<img src='{{ $image['url'] ?? '' }}' class='file-preview-image kv-preview-data' alt='{{ $image['name'] ?? '' }}' title='{{ $image['name'] ?? '' }}'>"
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
