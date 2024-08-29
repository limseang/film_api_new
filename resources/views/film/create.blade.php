@extends('layouts.master')
@section('title')
{{__('global.add_film')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_film')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <form action="{{route('film.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
                <div class="mb-3">
                  <label class="form-label" for="title">{{__('sma.title')}}</label>
                  <input type="text" class="form-control" name="title" value="{{old('title')}}" id="name" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="director_id">{{ trans('sma.director') }}</label>
                  <select id="director_id" class="{{ config('setup.input_select2') }}" name="director_id">
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($director as $value)
                      <option value="{{ $value->id }}" {{old('director_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
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
                      <input type="text" class="form-control datepicker-autohide" value="{{old('release_date')}}" name="release_date" placeholder="Please select date">
                    </div>
  
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="category">{{ trans('sma.category_film') }}</label>
                  <select id="category" class="{{ config('setup.input_select2') }}" multiple name="category[]" required data-placeholder="{{ __('global.please_select') }}">
                      @foreach($category as $value)
                      <option value="{{ $value->id }}" {{in_array($value->id, old('category', []))? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="running_time">{{__('sma.running_time')}}</label>
                    <input type="text" class="form-control running_time" name="running_time" value="{{old('running_time')}}" id="running_time" placeholder="{{__('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="tag">{{ trans('sma.tag') }}</label>
                    <select id="tag" class="{{ config('setup.input_select2') }}" name="tag" required>
                        <option value="">{{ __('global.please_select') }}</option>
                        @foreach($tag as $value)
                        <option value="{{ $value->id }}" {{old('tag') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                        @endforeach
                    </select>
                      <span class="invalid-feedback">
                        The field is required.
                      </span>
                    </div>
                  <div class="mb-3">
                    <label class="form-label" for="overview">{{trans('sma.overview')}}</label>
                    <textarea rows="10" cols="10" name="overview" class="form-control" id="ckeditor_classic_prefilled2" required>
                      {{old('overview')}}
                    </textarea>
                  </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.poster')}}</p>
                <input type="file" class="file-input-caption2" name="poster">
                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                    <div class="mb-3">
                      <label class="form-label" for="type">{{ trans('sma.type') }}</label>
                      <select id="type" class="{{ config('setup.input_select2') }}" name="type" required>
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($type as $value)
                          <option value="{{ $value->id }}" {{old('type') == $value->id ? 'selected':''}} >{{$value->name }}</option>
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
                            <option value="{{ $value->id }}" {{old('language') == $value->id ? 'selected':''}} >{{$value->name }}</option>
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
                            <option value="{{ $value->id }}" {{old('genre_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                            @endforeach
                        </select>
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="trailer">{{__('sma.trailer')}}</label>
                          <input type="url" class="form-control" name="trailer" value="{{old('trailer')}}" id="trailer" placeholder="https://youtube.com">
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                    <div class="mb-3">
                      <p class="fw-semibold">{{trans('sma.cover')}}</p>
                    <input type="file" class="file-input-caption2" name="cover">
                    </div>
                  
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="{{config('setup.button_opacity_success')}} mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
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
    });
</script>
  @endsection 
  @endsection
