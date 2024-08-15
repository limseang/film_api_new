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
                  <div class="file-input preview-update mb-1" style="padding: 4px; border;border-style: dashed; border-color:#f1f4f9; border-radius:0.375rem; border-width: cal(1px *2); border-color-translucent: rgba(0, 0, 0, 0.125)">
                    <div class=" file-drop-zone clearfix">
                      <div class="file-preview-thumbnails clearfix">
                        <div class="file-preview-frame krajee-default  kv-preview-thumb rotatable" id="thumb-1rad2qred4-148090_Screenshot_202024-06-06_20152921.png" data-fileindex="0" data-fileid="148090_Screenshot_202024-06-06_20152921.png" data-filename="Screenshot 2024-06-06 152921.png" data-template="image" data-zoom="">
                          <div class="kv-file-content">
                          <img src="{{$film->poster_image}}" class="file-preview-image kv-preview-data" title="Screenshot 2024-06-06 152921.png" alt="Screenshot 2024-06-06 152921.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
                        </div>
                      </div>
                      </div>
                    </div>
                    <br>
                  </div>
                <input type="file" class="file-input-caption2" name="poster">
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
                      <div class="file-input preview-update3 mb-1" style="padding: 4px; border;border-style: dashed; border-color:#f1f4f9; border-radius:0.375rem; border-width: cal(1px *2); border-color-translucent: rgba(0, 0, 0, 0.125)">
                        <div class=" file-drop-zone clearfix">
                          <div class="file-preview-thumbnails clearfix">
                            <div class="file-preview-frame krajee-default  kv-preview-thumb rotatable" id="thumb-1rad2qred4-148090_Screenshot_202024-06-06_20152921.png" data-fileindex="0" data-fileid="148090_Screenshot_202024-06-06_20152921.png" data-filename="Screenshot 2024-06-06 152921.png" data-template="image" data-zoom="">
                              <div class="kv-file-content">
                              <img src="{{$film->cover_image}}" class="file-preview-image kv-preview-data" title="Screenshot 2024-06-06 152921.png" alt="Screenshot 2024-06-06 152921.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
                            </div>
                          </div>
                          </div>
                        </div>
                        <br>
                      </div>
                    <input type="file" class="file-input-caption3" name="cover">
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
      // when upload file displan none in class preview-update
      $('.file-input-caption2').change(function() {
        $('.preview-update').css('display', 'none');
      });
      $('.file-input-caption3').change(function() {
        $('.preview-update3').css('display', 'none');
      });

      $('.running_time').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
  </script>
  @endsection
  @endsection
