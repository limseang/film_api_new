@extends('layouts.master')
@section('title')
{{__('sma.add_origin')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_origin')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('origin.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{old('name')}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="url">{{__('sma.page_url')}}</label>
                  <input type="text" class="form-control" name="url" value="{{old('url')}}" id="url" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="page_id">{{__('sma.page_id')}}</label>
                  <input type="text" class="form-control" name="page_id" value="{{old('page_id')}}" id="page_id" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                <select id="publish" class="{{ config('setup.input_select2') }}" name="status" required>
                    <option value="">{{ __('global.please_select') }}</option>
                    <option value="1" {{old('status') == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                    <option value="2" {{old('status') == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                </select>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="description">{{trans('sma.description')}}</label>
                  <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled2">
                    {{old('description')}}
                  </textarea>
                </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.image')}}</p>
							  <input type="file" class="file-input-caption2" name="image">
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
  @endsection
