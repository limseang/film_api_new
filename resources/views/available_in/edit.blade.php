@extends('layouts.master')
@section('title')
{{__('sma.edit_cinema')}}
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
            
              <form method="POST" action="{{route('available_in.update', $available_in->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$available_in->name}}" id="name" placeholder="Enter name" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="url">{{__('sma.url')}}</label>
                  <input type="url" class="form-control" name="url" value="{{$available_in->url}}" id="url" placeholder="Enter url" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="type">{{__('sma.type')}}</label>
                  <input type="text" class="form-control" name="type" value="{{$available_in->type}}" id="type" placeholder="Enter type">
                </div>
                <div class="mb-3">
                  {{-- Preview imag --}}
                  <p class="fw-semibold">{{trans('sma.image')}}</p>
							    <input type="file" class="file-input-caption-edit" name="image">
                </div>
                </div>
                 </div>
                  <br>
                <div class="d-flex align-items-center">
                  <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                </div>
             </form>
      </div>
    </div>
  </div>
  @section('scripts')
  <script>
    $(document).ready(function() {
      var initialPreview = [
        "<img src='{{ $image['url'] ?? '' }}' class='file-preview-image kv-preview-data' alt='{{ $image['name'] ?? '' }}' title='{{ $image['name'] ?? ''}}'>"
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