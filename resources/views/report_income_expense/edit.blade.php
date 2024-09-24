@extends('layouts.master')
@section('title')
{{__('global.edit_income_expense')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_income_expense')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('report_income_expense.update',$reportIncomeExpense->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="created_at">{{ trans('sma.created_at') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="ph-calendar"></i>
                    </span>
                    <input type="text" class="form-control daterange-single2" value="{{  date('d/m/Y H:i:s', strtotime($reportIncomeExpense->date_at)) }}" name="created_at" required placeholder="{{__('sma.please_select_date')}}">
                  </div>

                </div>
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$reportIncomeExpense->name}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="reference">{{__('sma.reference')}}</label>
                  <input type="text" class="form-control" name="reference" value="{{$reportIncomeExpense->reference}}" id="reference" placeholder="{{trans('sma.please_input')}}">
                </div>
                <div class="mb-3">
                  <label class="form-label" for="type">{{ trans('sma.type') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="type" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{$reportIncomeExpense->type == '1' ? 'selected':''}}>{{ __('sma.expense') }}</option>
                      <option value="2" {{$reportIncomeExpense->type == '2' ? 'selected':''}}>{{ __('sma.income') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="currency">{{ trans('sma.currency') }}</label>
                    <select id="currency" class="{{ config('setup.input_select2') }} form-select" name="currency" required="">
                        <option value="USD" {{$reportIncomeExpense->currency == 'USD' ? 'selected':''}}>{{ __('sma.USD') }}($)</option>
                        <option value="KHR" {{$reportIncomeExpense->currency == 'KHR' ? 'selected':''}}>{{ __('sma.KHR') }}(៛)</option>
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>
                <div class="mb-3">
                  <label class="form-label" for="amount">{{__('sma.amount')}}</label>
                  <input type="text" class="form-control" name="amount" value="{{$reportIncomeExpense->amount}}" required id="amount" placeholder="{{trans('sma.please_input')}}">
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.attachment')}}</p>
							  <input type="file" class="file-input-caption-edit" name="attachment">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="noted">{{trans('sma.noted')}}</label>
                    <textarea rows="3" cols="3" name="noted" class="form-control" id="ckeditor_classic_prefilled3">
                      {{$reportIncomeExpense->noted}}
                    </textarea>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                  </div>
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
   $('#amount').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
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
    });
  </script>
  @endsection
  @endsection
