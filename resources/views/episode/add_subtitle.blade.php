@extends('layouts.master')
@section('title')
{{__('sma.add_subtitle')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
            <div class="d-flex justify-content-between align-content-center">
                <h6 class="card-title text-success text-bold">
                    <i class="ph-file-plus"></i>
                    &nbsp;  &nbsp;<span>{{__('sma.add_subtitle')}}
                </h6>
                <button type="button" class="btn btn-flat-success btn-sm rounded-pill p-2 add-subtitle">
                    <i class="ph-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <form action="{{route('episode.store_subtitle')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
            <div class="col-12 col-lg-12 p-10">
                <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.film_name')}}:</span>&nbsp; {{$film->title}}</p>
                <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode_name')}}:</span>&nbsp;{{$episode->title}}</p>
                <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode')}}:</span>&nbsp;{{$episode->episode}}</p>
            </div>
            <input type="hidden" name="episode_id" value="{{$episode->id}}">
            <input type="hidden" name="film_id" value="{{$film->id}}">
            <div class="col-12 col-lg-12 p-10">
                <table class="table table-borderless">
                    <thead class="table-primary bg-opacity-10 table-borderless">
                        <tr style="border: none">
                            <th class="text-center text-primary"><i class="ph-list-numbers"></i> {{trans('sma.n_o')}} </th>
                            <th class="text-center text-primary"><i class="ph-identification-card"></i> {{trans('sma.subtitle')}} </th>
                            <th class="text-center text-primary"> <i class="ph-file"></i>{{trans('sma.file_subtitle')}} </th>
                            <th class="text-center text-primary">{{trans('sma.action')}} </th>
                        </tr>
                    </thead>
                    <tbody style="maigin:4px" class="t-body">
                        @if(empty($subtitles))
                        <tr class=" text-center">
                            <td>
                                <span class="badge bg-teal bg-opacity-20 text-teal rounded-pill p-1" data-value="table-one">1</span>
                            </td>
                            <td style="cursor: pointer;">
                                <select id="status" class="{{ config('setup.input_select2') }} form-select" name="language[]" required>
                                    <option value="">{{ __('global.please_select') }}</option>
                                    @foreach($country as $value)
                                    <option value="{{ $value->id }}" {{in_array($value->id, old('language', []))? 'selected':''}} >{{$value->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="file" data-file="file-input" data-show-preview="false" name="file[]" id="file" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-flat-success btn-sm rounded-pill p-2">
                                    <i class="ph-check"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
          </div>
          <br>
          <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
        </div>
        </form>
        </div>
        </div>
    </div>
  </div>
  @section('scripts')
  <script>
    $(document).ready(function() {
        // Subtitles data from the server
        var subtitles = @json($subtitles); // Convert PHP array to JavaScript array
        console.log(subtitles);
        var count = subtitles.length; // Start with the count of existing subtitles
    
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
        // Function to render a row for a subtitle
        function renderRow(subtitle, index) {
            var selectId = 'status_' + index;
            var fileId = 'file_' + index;
            var data_file = 'file-input_' + index;
    
            var countryOptions = `
                <option value="">{{ __('global.please_select') }}</option>
                @foreach($country as $value)
                    <option value="{{ $value->id }}" ${subtitle && subtitle.language_id == {{ $value->id }} ? 'selected' : ''}>{{ $value->name }}</option>
                @endforeach
            `;
    
            var newRow = `
                <tr class="text-center">
                    <td>
                        <span class="badge bg-teal bg-opacity-20 text-teal rounded-pill p-1" data-value="table-one">${index + 1}</span>
                    </td>
                    <td style="cursor: pointer;">
                        <select id="${selectId}" class="{{ config('setup.input_select2') }} form-select" name="language[]" required>
                            ${countryOptions}
                        </select>
                    </td>
                    <td>
                        <input type="file" data-file="${data_file}" class="file-input caption-edit" data-show-preview="false" name="file[]" id="${fileId}" ${subtitle ? `value="${subtitle.file_path}"` : ''} required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-flat-danger btn-sm rounded-pill p-2 remove-row">
                            <i class="ph-x"></i>
                        </button>
                    </td>
                </tr>
            `;
    
            // Append the new row to the tbody
            $('.t-body').append(newRow);
    
            // Initialize select2 and file input plugins for new row
            $(`#${selectId}`).select2();
            $(`[data-file="${data_file}"]`).fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="ph-file-plus me-2"></i>',
                uploadIcon: '<i class="ph-file-arrow-up me-2"></i>',
                removeIcon: '<i class="ph-x fs-base me-2"></i>',
                layoutTemplates: {
                    icon: '<i class="ph-check"></i>'
                },
                removeClass: 'btn btn-light',
                initialCaption: "No file selected",
                previewZoomButtonClasses: previewZoomButtonClasses,
                previewZoomButtonIcons: previewZoomButtonIcons,
                fileActionSettings: fileActionSettings,
                showUpload: false,
            });
        }
    
        // Render initial subtitles if any
        if (subtitles.length > 0) {
            subtitles.forEach((subtitle, index) => {
                renderRow(subtitle, index);
            });
        }
    
        // Function to update dropdown options
        function updateDropdownOptions() {
            var selectedValues = [];
    
            $('select[name="language[]"]').each(function() {
                if ($(this).val()) {
                    selectedValues.push($(this).val());
                }
            });
    
            $('select[name="language[]"]').each(function() {
                var currentVal = $(this).val();
                $(this).find('option').each(function() {
                    $(this).prop('disabled', false);
                    if (selectedValues.includes($(this).val()) && $(this).val() !== currentVal) {
                        $(this).prop('disabled', true);
                    }
                });
            });
        }
    
        // Add new row on button click
        $('.add-subtitle').click(function() {
            renderRow(null, count);
            count++;
            updateDropdownOptions();
        });
    
        // Event listener for dropdown changes
        $(document).on('change', 'select[name="language[]"]', function() {
            updateDropdownOptions();
        });
    
        // Remove row functionality
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateDropdownOptions();
    
            // Update the row numbers after a row is removed
            $('.t-body tr').each(function(index) {
                $(this).find('td:first-child .badge').text(index + 1);
            });
        });
    });
    </script>
    
  @endsection
@endsection