@extends('layouts.master')
@section('title')
{{__('sma.edit_category_film')}}
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
            
              <form method="POST" action="{{route('category.update', $category->id)}}" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$category->name}}" id="name" placeholder="Enter name" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                <label class="form-label">{{ trans('global.publish') }}</label>
                <select id="publish" class="{{ config('setup.input_select2') }}" name="status">
                    <option value="">{{ __('global.please_select') }}</option>
                    <option value="1" {{$category->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                    <option value="2" {{$category->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                </select>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="description">{{trans('sma.description')}}</label>
                  <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled2">
                    {{$category->description}}
                  </textarea>
                </div>
                </div>
                 </div>
                  <br>
                <button type="submit" class="btn btn-primary mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
             </form>
      </div>
    </div>
  </div>
  @endsection
