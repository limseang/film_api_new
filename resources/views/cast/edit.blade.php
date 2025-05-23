@extends('layouts.master')
@section('title')
{{__('global.edit_cast')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_cast')}}</span>
          </h6>

        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">

              <form action="{{route('cast.update', $cast->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="character">{{__('sma.character')}}</label>
                  <input type="text" class="form-control" name="character" value="{{$cast->character}}" id="character" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="position">{{__('sma.position')}}</label>
                  <input type="text" class="form-control" name="position" value="{{$cast->position}}" id="position" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="actor_id">{{ trans('sma.artist') }}</label>
                  <select id="actor_id" class="{{ config('setup.input_select2') }}" name="actor_id" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($artist as $value)
                      <option value="{{ $value->id }}" {{$cast->actor_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="film_id">{{ trans('sma.film') }}</label>
                    <select id="film_id" class="{{ config('setup.input_select2') }}" name="film_id" required>
                        <option value="">{{ __('global.please_select') }}</option>
                     @if(!empty($cast->film))
                        <option value="{{ $cast->film->id }}" selected>{{ $cast->film->title }}</option>
                    @endif
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{$cast->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{$cast->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
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


         $('#film_id').select2({
            allowClear: true,
            placeholder: "{{ __('global.please_select') }}",
            // translate
            language: {
                noResults: function () {
                return 'មិនមានទិន្នន័យដែលបានរកឃើញ';
                },
                searching: function() {
                    return "កំពុងស្វែងរក...";
                }
            },
            ajax: {
                url: "{{route('cast.get_film_cast')}}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term, // Search term
                        page: params.page || 1, // Pagination page
                    };
                },
                processResults: function(data, params) {
                    let results = $.map(data.data, function(item) {
                        return {
                            id: item.id,
                            text: item.title
                        };
                    });

                    // If no results found, create a new selectable option
                    if (results.length === 0 && params.term) {
                        results.push({
                            id: params.term, // Temporary ID
                            text: params.term,
                        });
                    }

                    return {
                        results: results
                    };
                },
                //
            },
        });
    });


  </script>
  @endsection
  @endsection
