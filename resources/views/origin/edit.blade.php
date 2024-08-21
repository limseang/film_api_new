@extends('layouts.master')
@section('title')
{{__('sma.edit_origin')}}
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
            
              <form method="POST" action="{{route('origin.update', $origin->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$origin->name}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="url">{{__('sma.page_url')}}</label>
                  <input type="text" class="form-control" name="url" value="{{$origin->url}}" id="url" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="page_id">{{__('sma.page_id')}}</label>
                  <input type="text" class="form-control" name="page_id" value="{{$origin->page_id}}" id="page_id" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                <label class="form-label">{{ trans('global.publish') }}</label>
                <select id="publish" class="{{ config('setup.input_select2') }}" name="status">
                    <option value="">{{ __('global.please_select') }}</option>
                    <option value="1" {{$origin->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                    <option value="2" {{$origin->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                </select>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="description">{{trans('sma.description')}}</label>
                  <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled2">
                    {{$origin->description}}
                  </textarea>
                </div>
                <div class="mb-3">
                  {{-- Preview imag --}}
                  <p class="fw-semibold">{{trans('sma.image')}}</p>
							    <input type="file" class="file-input-caption-edit" name="image">
                </div>
                </div>
                 </div>
                  <br>
                <button type="submit" class="btn btn-primary mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
             </form>
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
