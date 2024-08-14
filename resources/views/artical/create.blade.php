@extends('layouts.master')
@section('title')
{{__('global.add_artical')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_artical')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <form action="{{route('artical.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
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
                  <label class="form-label" for="origin_id">{{ trans('sma.origin') }}</label>
                  <select id="origin_id" class="{{ config('setup.input_select2') }}" name="origin_id">
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($origins as $value)
                      <option value="{{ $value->id }}" {{old('origin_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="tag">{{ trans('sma.tag') }}</label>
                  <select id="tag" class="{{ config('setup.input_select2') }}" multiple name="tag[]" required data-placeholder="{{ __('global.please_select') }}">
                      @foreach($tag as $value)
                      <option value="{{ $value->id }}" {{in_array($value->id, old('tag', []))? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
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
                      <label class="form-label" for="film">{{ trans('sma.film') }}</label>
                      <select id="film" class="{{ config('setup.input_select2') }}" name="film">
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($film as $value)
                          <option value="{{ $value->id }}" {{old('film') == $value->id ? 'selected':''}} >{{$value->title }}</option>
                          @endforeach
                      </select>
                        <span class="invalid-feedback">
                          The field is required.
                        </span>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="category_id">{{ trans('sma.category') }}</label>
                        <select id="category_id" class="{{ config('setup.input_select2') }}" name="category_id" required>
                            <option value="">{{ __('global.please_select') }}</option>
                            @foreach($categories as $value)
                            <option value="{{ $value->id }}" {{old('category_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                            @endforeach
                        </select>
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">{{trans('sma.description')}}</label>
                            <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled" required>
                              {{old('description')}}
                            </textarea>
                          </div>
                    
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <p class="fw-semibold">{{trans('sma.image')}}</p>
                          <input type="file" class="file-input-caption2" name="image">
                          </div>
                    </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-success mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="btn btn-success mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
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
