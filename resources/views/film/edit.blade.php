@extends('layouts.master')
@section('title')
{{__('global.edit_film')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_film')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <form action="{{route('film.update',$film->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
                <div class="mb-3">
                  <label class="form-label" for="title">{{__('sma.title')}}</label>
                  <input type="text" class="form-control" name="title" value="{{$film->title}}" id="name" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="director_id">{{ trans('sma.director') }}</label>
                  <select id="director_id" class="{{ config('setup.input_select2') }}" name="director_id">
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($director as $value)
                      <option value="{{ $value->id }}" {{$film->director == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="release_date">{{ trans('sma.release_date') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="ph-calendar"></i>
                      </span>
                      <input type="text" class="form-control datepicker-autohide" value="{{$film->release_date}}" name="release_date" placeholder="Please select date">
                    </div>
  
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="category">{{ trans('sma.category_film') }}</label>
                  <select id="category" class="{{ config('setup.input_select2') }}" multiple name="category[]" required data-placeholder="{{ __('global.please_select') }}">
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($category as $value)
                      <option value="{{ $value->id }}" {{in_array($value->id,$multiCategory ?? []) ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="tag">{{ trans('sma.tag') }}</label>
                    <select id="tag" class="{{ config('setup.input_select2') }}" name="tag" required>
                        <option value="">{{ __('global.please_select') }}</option>
                        @foreach($tag as $value)
                        <option value="{{ $value->id }}" {{$film->tag == $value->id ? 'selected':''}} >{{$value->name }}</option>
                        @endforeach
                    </select>
                      <span class="invalid-feedback">
                        The field is required.
                      </span>
                    </div>
                  <div class="mb-3">
                    <label class="form-label" for="running_time">{{__('sma.running_time')}}</label>
                    <input type="text" class="form-control running_time" name="running_time" value="{{$film->running_time}}" id="running_time" placeholder="{{__('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="overview">{{trans('sma.overview')}}</label>
                    <textarea rows="10" cols="10" name="overview" class="form-control" id="ckeditor_classic_prefilled2" required>
                      {{$film->overview}}
                    </textarea>
                  </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.poster')}}</p>
                <input type="file" class="file-input-caption-image" name="poster">
                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                    <div class="mb-3">
                      <label class="form-label" for="type">{{ trans('sma.type') }}</label>
                      <select id="type" class="{{ config('setup.input_select2') }}" name="type" required>
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($type as $value)
                          <option value="{{ $value->id }}" {{$film->type == $value->id ? 'selected':''}} >{{$value->name }}</option>
                          @endforeach
                      </select>
                        <span class="invalid-feedback">
                          The field is required.
                        </span>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="language">{{ trans('sma.language') }}</label>
                        <select id="language" class="{{ config('setup.input_select2') }}" name="language" required>
                            <option value="">{{ __('global.please_select') }}</option>
                            @foreach($countries as $value)
                            <option value="{{ $value->id }}" {{$film->language == $value->id ? 'selected':''}} >{{$value->name }}</option>
                            @endforeach
                        </select>
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                      <div class="mb-3">
                        <label class="form-label" for="tag">{{ trans('sma.genre') }}</label>
                        <select id="genre" class="{{ config('setup.input_select2') }}" name="genre_id">
                            <option value="">{{ __('global.please_select') }}</option>
                            @foreach($genre as $value)
                            <option value="{{ $value->id }}" {{$film->genre_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                            @endforeach
                        </select>
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="trailer">{{__('sma.trailer')}}</label>
                          <input type="url" class="form-control" name="trailer" value="{{$film->trailer}}" id="trailer" placeholder="https://youtube.com">
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                    <div class="mb-3">
                      <p class="fw-semibold">{{trans('sma.cover')}}</p>
                    <input type="file" class="file-input-caption-cover" name="cover">
                    </div>
                  
                </div>
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
  @section('scripts')
  <script>
    $(document).ready(function() {
      $('.running_time').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
      var initialPreviewImage = [
        "<img src='{{ $image['url'] ?? '' }}' class='file-preview-image kv-preview-data' alt='{{ $image['name']  ?? ''}}' title='{{ $image['name'] ?? '' }}'>"
      ];
      var initialPreviewCover = [
        "<img src='{{ $cover['url'] ?? '' }}' class='file-preview-image kv-preview-data' alt='{{ $cover['name'] ?? '' }}' title='{{ $cover['name'] ?? '' }}'>"
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
      $('.file-input-caption-image').fileinput({
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
            initialPreview: initialPreviewImage,
            showCaption: true,
            dropZoneEnabled: true,
            showUpload: false,
            showRemove: false,
        });
        $('.file-input-caption-cover').fileinput({
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
            initialPreview: initialPreviewCover,
            showCaption: true,
            dropZoneEnabled: true,
            showUpload: false,
            showRemove: false,
        });
    });
  </script>
  @endsection
  @endsection
