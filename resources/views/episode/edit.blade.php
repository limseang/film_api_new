@extends('layouts.master')
@section('title')
{{__('global.edit_episode')}}
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
          <div class="row">
            <div class="col-12">
              <div class="d-flex flex-wrap: wrap">
                <span>
                  <span><span class="{{config('setup.badge_primary')}}">{{trans('sma.season')}} : </span>
                  @php
                      $displayedSeasons = [];
                      $lastEpisode = 0;
                  @endphp

                  @foreach($episodes as $value)
                      @if(!in_array($value->season, $displayedSeasons))
                          <span class="{{ config('setup.badge_primary') }}">{{ $value->season }}</span>
                          @php
                              $displayedSeasons[] = $value->season;
                          @endphp
                      @endif
                  @endforeach
                  </span>
                </span>
             </div>
             <br>
            </div>
            <div class="col-12">
              <div class="d-flex flex-wrap: wrap">
                <span>
                  <span><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode')}} : </span>
                   @foreach($episodes as $value)
                    {{-- Last Episode --}}
                    @if($lastEpisode < $value->episode)
                      @php
                        $lastEpisode = $value->episode ?? 0;
                      @endphp
                    @endif
                     <span class="{{config('setup.badge_primary')}}">{{$value->episode}}</span>
                   @endforeach
                  </span>
                </span>
             </div>
             <br>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
            
              <form action="{{route('episode.update',$episode->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="film_id">{{ trans('sma.film') }}</label>
                  <input type="text" class="form-control" value="{{$film->title}}" id="film_id" disabled>
                  <input type="hidden" class="form-control" name="film_id" value="{{$episode->film_id}}">
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="title">{{__('sma.title')}}</label>
                  <input type="text" class="form-control" name="title" value="{{$film->title}}"  id="title" placeholder="{{trans('sma.please_input')}}" required>
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
                    <input type="text" class="form-control datepicker-autohide" value="{{$episode->release_date}}"  name="release_date" placeholder="Please select date">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="season">{{__('sma.season')}}</label>
                  <input type="text" class="form-control season" name="season" value="{{$episode->season}}" id="season" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="episode">{{__('sma.episode')}}</label>
                  <input type="text" class="form-control episode" name="episode" value="{{$episode->episode}}" id="episode" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{$episode->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{$episode->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="description">{{trans('sma.description')}}</label>
                    <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled2" required>
                      {{$episode->description}}
                    </textarea>
                  </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.poster')}}</p>
					<input type="file" class="file-input-caption-image" name="poster" accept="image/*">
                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                    <div class="col-12 mb-3">
                        <div class="mb-3">
                            <p class="fw-semibold">{{trans('sma.video')}}</p>
                            <input type="file" class="file-input-video" name="video" data-show-caption="true" data-show-upload="true" accept="video/*">
                        </div>
                        <label class="form-label"> <span class="badge bg-success bg-opacity-20 text-success">Video ID</span></label>
                        <input type="text" name="video_id" class="form-control" id="video_id" value='{{$episode->file}}' readonly>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <p class="fw-semibold">{{trans('sma.video_720')}}</p>
                            <input type="file" class="file-input-video-720" name="video_720" data-show-caption="true" data-show-upload="true" accept="video/*">
                        </div>
                        <label class="form-label"> <span class="badge bg-success bg-opacity-20 text-success">Video ID 720</span></label>
                        <input type="text" name="video_id_720" class="form-control" id="video_id_720" value='{{$episode->video_720}}' readonly>
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
   var initialPreviewVideo = [
          '<video class="kv-preview-data file-preview-video" controls="" style="width: 213px; height: 160px;">' +
              '<source src="{{$video["url"] ?? ''}}" type="video/mp4">' +
              '<div class="file-preview-other">' +
                  '<span class="file-other-icon"><i class="bi-file-earmark-fill"></i></span>' +
              '</div>' +
          '</video>'
      ];
    var nameVideo = "{{ $video['name'] ?? '' }}";

    var initialPreviewVideo720 = [
          '<video class="kv-preview-data file-preview-video" controls="" style="width: 213px; height: 160px;">' +
              '<source src="{{$video_720["url"] ?? ''}}" type="video/mp4">' +
              '<div class="file-preview-other">' +
                  '<span class="file-other-icon"><i class="bi-file-earmark-fill"></i></span>' +
              '</div>' +
          '</video>'
      ];
    var nameVideo720 = "{{ $video_720['name'] ?? '' }}"
  
    var initialPreviewPoster = [
        "<img src='{{ $poster['url'] ?? '' }}' class='file-preview-image kv-preview-data' alt='{{ $poster['name'] ?? '' }}' title='{{ $poster['name'] ?? '' }}'>"
      ];
    var namePoster = "{{ $poster['name'] ?? '' }}";
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
    $(document).ready(function() {
     // before submit form to server side check if video is value is empty or 0
        $('form').submit(function() {
            if($('#video_id').val() == 0 && $('#video_id_720').val() == '') {
                new Noty({
                    text: '<i class="fa fa-exclamation-circle text-danger"></i> Please upload a video file first',
                    type: 'warning'
                }).show();
                
                return false;
            }
        });
      $('.season').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('.episode').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });

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
            initialPreview: initialPreviewPoster,
            showCaption: true,
            dropZoneEnabled: true,
            showUpload: false,
            showRemove: false,
        });
        $('.file-input-video').fileinput({
          browseLabel: 'video',
          browseClass: 'btn btn-info',
          uploadUrl: "{{ route('episode.upload_video') }}", // server upload action
          uploadAsync: true,
          maxFileCount: 1,
          autoReplace: true,
          overwriteInitial: true,
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
          initialCaption: nameVideo,
          previewZoomButtonClasses: previewZoomButtonClasses,
          previewZoomButtonIcons: previewZoomButtonIcons,
          initialPreview: initialPreviewVideo,
          uploadExtraData: function() {
              return {
                  _token: $("input[name='_token']").val(),
              };
          }
      }).on('fileuploaded', function(event, previewId, index, response) {
           var result = previewId.response;
          if (result.success) {
                $('#video_id').val(result.file_id);
              new Noty({
                    text: '<i class="fa fa-check-circle text-success"></i> Upload successful',
                    type: 'success'
                }).show();
          } else {
              // Show the error message
              new Noty({
                    text: '<i class="fa fa-exclamation-circle text-danger"></i> Something went wrong, please try again',
                    type: 'warning'
                }).show();
          } 
      }).on('fileuploaderror', function(event, data, msg) {
          // Show the error message
          new Noty({
                    text: '<i class="fa fa-exclamation-circle text-danger"></i> Something went wrong, please try again',
                    type: 'warning'
                }).show();
      });

      $('.file-input-video-720').fileinput({
          browseLabel: 'video',
          browseClass: 'btn btn-info',
          uploadUrl: "{{ route('episode.upload_video_720') }}", // server upload action
          uploadAsync: true,
          maxFileCount: 1,
          autoReplace: true,
          overwriteInitial: true,
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
          initialCaption: nameVideo720,
          previewZoomButtonClasses: previewZoomButtonClasses,
          previewZoomButtonIcons: previewZoomButtonIcons,
          initialPreview: initialPreviewVideo720,
          uploadExtraData: function() {
              return {
                  _token: $("input[name='_token']").val(),
              };
          }
      }).on('fileuploaded', function(event, previewId, index, response) {
           var result = previewId.response;
          if (result.success) {
                $('#video_id_720').val(result.file_id);
              new Noty({
                    text: '<i class="fa fa-check-circle text-success"></i> Upload successful',
                    type: 'success'
                }).show();
          } else {
              // Show the error message
              new Noty({
                    text: '<i class="fa fa-exclamation-circle text-danger"></i> Something went wrong, please try again',
                    type: 'warning'
                }).show();
          } 
      }).on('fileuploaderror', function(event, data, msg) {
          // Show the error message
          new Noty({
                    text: '<i class="fa fa-exclamation-circle text-danger"></i> Something went wrong, please try again',
                    type: 'warning'
                }).show();
      });

    });
</script>
  @endsection
  @endsection
