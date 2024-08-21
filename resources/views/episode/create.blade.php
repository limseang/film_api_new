@extends('layouts.master')
@section('title')
{{__('global.add_cast')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-file-video"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_episode')}} (<span class="{{config('setup.badge_primary')}}">{{$film->title}}</span>)</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
            
              <form action="{{route('cast.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="film_id">{{ trans('sma.film') }}</label>
                  <input type="text" class="form-control" value="{{$film->title}}" id="film_id" disabled>
                  <input type="hidden" class="form-control" value="{{$film->id}}">
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="title">{{__('sma.title')}}</label>
                  <input type="text" class="form-control" name="title" value="{{old('title')}}" id="title" placeholder="{{trans('sma.please_input')}}" required>
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
                    <input type="text" class="form-control datepicker-autohide" value="{{old('release_date')}}" name="release_date" placeholder="Please select date">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="season">{{__('sma.season')}}</label>
                  <input type="text" class="form-control season" name="season" value="{{old('season')}}" id="season" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="episode">{{__('sma.episode')}}</label>
                  <input type="text" class="form-control episode" name="episode" value="{{old('episode')}}" id="episode" placeholder="{{__('sma.please_input')}}" required>
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
                    <label class="form-label" for="description">{{trans('sma.description')}}</label>
                    <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled2" required>
                      {{old('description')}}
                    </textarea>
                  </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.poster')}}</p>
							  <input type="file" class="file-input-caption2" name="poster">
                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                  <div class="mb-3">
                    <p class="fw-semibold">{{trans('sma.video')}}</p>
                    <input type="file" class="file-input-video" data-show-caption="true" name="video" data-show-upload="true" accept="video/*">
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-primary mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="btn btn-success mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
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
    $(document).ready(function() {
      $('.season').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('.episode').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('.file-input-video').fileinput({
            browseLabel: 'video',
            browseClass: 'btn btn-info',
            uploadUrl: "{{route('episode.upload_video')}}", // server upload action
            uploadAsync: true,
            maxFileCount: 5,
            initialPreview: [],
            browseIcon: '<i class="ph-file-plus me-2"></i>',
            uploadIcon: '<i class="ph-file-arrow-up me-2"></i>',
            removeIcon: '<i class="ph-x fs-base me-2"></i>',
            fileActionSettings: {
                removeIcon: '<i class="ph-trash"></i>',
                removeClass: '',
                uploadIcon: '<i class="ph-upload-simple"></i>',
                uploadClass: '',
                zoomIcon: '<i class="ph-magnifying-glass-plus"></i>',
                zoomClass: '',
                indicatorNew: '<i class="ph-file-plus text-success"></i>',
                indicatorSuccess: '<i class="ph-check file-icon-large text-success"></i>',
                indicatorError: '<i class="ph-x text-danger"></i>',
                indicatorLoading: '<i class="ph-spinner spinner text-muted"></i>',
            },
            layoutTemplates: {
                icon: '<i class="ph-check"></i>'
            },
            uploadClass: 'btn btn-light',
            removeClass: 'btn btn-light',
            initialCaption: 'No file selected',
            previewZoomButtonClasses: previewZoomButtonClasses,
            previewZoomButtonIcons: previewZoomButtonIcons,
            uploadExtraData: function() {
                return {
                    _token: $("input[name='_token']").val(),
                };
            }
        }).on('fileuploaded', function(event, previewId, index, fileId) {
             if(event.response.success){
                $('#video').val(event.response.success);
             }
        });
    });
</script>
  @endsection
  @endsection
