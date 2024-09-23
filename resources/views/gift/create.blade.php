@extends('layouts.master')
@section('title')
{{__('sma.add_gift')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_gift')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('gift.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{old('name')}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="code">{{__('sma.code')}}</label>
                  <input type="text" class="form-control" name="code" value="{{old('code')}}" id="code" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="phone">{{__('sma.noted')}}</label>
                  <input type="text" class="form-control" name="noted" value="{{old('noted')}}" id="noted" placeholder="{{trans('sma.please_input')}}">
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="point">{{__('sma.point')}}</label>
                  <input type="text" class="form-control" name="point" value="{{old('point')}}" id="point" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="show_type">{{__('sma.quantity')}}</label>
                  <input type="number" class="form-control" name="quantity" value="{{old('quantity')}}" id="quantity" placeholder="{{trans('sma.please_input')}}">
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
                    <label class="form-label" for="expired_date">{{ trans('sma.expired_date') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="ph-calendar"></i>
                      </span>
                      <input type="text" class="form-control daterange-single2" value="{{old('expired_date')}}" name="expired_date" placeholder="{{__('sma.please_select_date')}}">
                    </div>
  
                  </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.image')}}</p>
							  <input type="file" class="file-input-caption2" name="image">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">{{trans('sma.description')}}</label>
                    <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled3" required>
                      {{old('description')}}
                    </textarea>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="{{config('setup.button_opacity_success')}} mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
<script>
     $(document).ready(function() {
      ClassicEditor.create(document.querySelector('#ckeditor_classic_prefilled3'), 
        {
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'fontFamily',
                    'fontSize',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    'undo',
                    'redo'
                ]
            }
        })
    });
   $('#point').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

</script>
  @endsection
