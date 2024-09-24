@extends('layouts.master')
@section('title')
{{__('sma.add_income_expense')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_income_expense')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('report_income_expense.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="created_at">{{ trans('sma.created_at') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="ph-calendar"></i>
                    </span>
                    <input type="text" class="form-control daterange-single2" value="{{ old('created_at', date('d/m/Y h:i:s')) }}" name="created_at" required placeholder="{{__('sma.please_select_date')}}">
                  </div>

                </div>
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{old('name')}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="reference">{{__('sma.reference')}}</label>
                  <input type="text" class="form-control" name="reference" value="{{old('reference')}}" id="reference" placeholder="{{trans('sma.please_input')}}">
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="type">{{ trans('sma.type') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="type" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{old('type') == '1' ? 'selected':''}}>{{ __('sma.expense') }}</option>
                      <option value="2" {{old('type') == '2' ? 'selected':''}}>{{ __('sma.income') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="currency">{{ trans('sma.currency') }}</label>
                    <select id="currency" class="{{ config('setup.input_select2') }} form-select" name="currency" required="">
                        <option value="USD" {{old('currency') == 'USD' ? 'selected':''}}>{{ __('sma.USD') }}($)</option>
                        <option value="KHR" {{old('currency') == 'KHR' ? 'selected':''}}>{{ __('sma.KHR') }}(៛)</option>
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>
                <div class="mb-3">
                  <label class="form-label" for="amount">{{__('sma.amount')}}</label>
                  <input type="text" class="form-control" name="amount" value="{{old('amount')}}" required id="amount" placeholder="{{trans('sma.please_input')}}">
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.attachment')}}</p>
							  <input type="file" class="file-input-caption2" name="attachment">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="noted">{{trans('sma.noted')}}</label>
                    <textarea rows="3" cols="3" name="noted" class="form-control" id="ckeditor_classic_prefilled3">
                      {{old('noted')}}
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
   $('#amount').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

</script>
  @endsection
