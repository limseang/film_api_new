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
                    <div class="col-md-12 col-lg-6 p-10">
                        <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.film_name')}}:</span>&nbsp; {{$film->title}}</p>
                        <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode_name')}}:</span>&nbsp;{{$episode->title}}</p>
                        <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode')}}:</span>&nbsp;{{$episode->episode}}</p>
                    </div>
                    <div class="col-md-12  col-lg-6 p-10">
                        <table class="table table-borderless">
                            <thead class="table-primary bg-opacity-10 table-borderless">
                                <tr style="border: none">
                                    <th class="text-center text-primary">{{trans('sma.language')}}</th>
                                    <th class="text-center text-primary"> {{trans('sma.file_subtitle')}} </th>
                                </tr>
                            </thead>
                            <tbody class=" text-center">
                                @if($episode->subtitles->isEmpty())
                                <tr>
                                    <td colspan="2"> {{trans('sma.no_data_available_in_the_table')}}</td>
                                </tr>
                                @endif
                                @foreach($episode->subtitles as $subtitle)
                                <tr>
                                    <td> <span class="{{config('setup.badge_success')}}">{{$subtitle->language->name}}</span></td>
                                    <td>
                                        <a href="#" target="_blank" class="btn btn-flat-success btn-sm rounded-pill p-2 coming-soon" data-coming-soon={{$subtitle->id}}>
                                            <i class="ph-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            <form action="{{route('episode.store_subtitle')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
            <input type="hidden" name="episode_id" value="{{$episode->id}}">
            <input type="hidden" name="film_id" value="{{$film->id}}">
            <div class="row">
            <div class="col-12 col-lg-12 p-10">
                <p class="badge bg-light border-start border-width-3 text-body rounded-start-0 border-warning">Note: {{trans('sma.if_language_is_already_used_cannt_show_or_disable_at_dropdown_list')}}</p>
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
                        <tr class=" text-center">
                            <td>
                                <span class="badge bg-teal bg-opacity-20 text-teal rounded-pill p-1" data-value="table-one">1</span>
                            </td>
                            <td style="cursor: pointer;">
                                <select id="status" class="{{ config('setup.input_select2') }} form-select" name="language_id[]" required>
                                    <option value="">{{ __('global.please_select') }}</option>
                                    @foreach($country as $value)
                                    <option value="{{ $value->id }}" {{in_array($value->id, old('language_id', []))? 'selected':''}} >{{$value->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback">
                                    The field is required.
                                  </span>
                            </td>
                            <td>
                                {{-- <input type="file" data-file="file-input" data-show-preview="false" name="file[]" id="file" required> --}}
                                <input type="file" name="file[]" id="file" class="form-control" required placeholder="Custom error message" accept=".srt,.pdf, .docx, .xlsx, .txt, .csv, .zip">
                                <span class="invalid-feedback">
                                    The field is required.
                                  </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-flat-success btn-sm rounded-pill p-2 remove-first-index">
                                    <i class="ph-check"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </div>
          <br>
          <div class="d-flex align-items-center">
            <button type="submit" class="btn link-primary border-primary p-2" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
        </div>
        </form>
        </div>
        </div>
    </div>
  </div>
  @section('scripts')
  <script>
   $(document).ready(function() {
    // Initialize select2 for the first row
    $('.remove-first-index').click(function() {
        new Noty({
                    text: 'You can not remove the first row',
                    type: 'warning',
                }).show();
    });
    // coming-soon button
    $('.coming-soon').click(function(e) {
        e.preventDefault();
        new Noty({
                    text: 'Unavailable functionality coming soon.',
                    type: 'warning',
                }).show();
    });
    var count = 1;

    // Function to render a row for a subtitle
    function renderRow(subtitle, index) {
        var selectId = 'status_' + index;
        var fileId = 'file_' + index;
        var data_file = 'file-input_' + index;

        var countryOptions = `
            <option value="">{{ __('global.please_select') }}</option>
            @foreach($country as $value)
                <option value="{{ $value->id }}"> {{ $value->name }} </option>
            @endforeach
        `;

        var newRow = `
            <tr class="text-center">
                <td>
                    <span class="badge bg-teal bg-opacity-20 text-teal rounded-pill p-1" data-value="table-one">${index + 1}</span>
                </td>
                <td style="cursor: pointer;">
                    <select id="${selectId}" class="{{ config('setup.input_select2') }} form-select" name="language_id[]" required>
                        ${countryOptions}
                    </select>
                    <span class="invalid-feedback">
                        The field is required.
                    </span>
                </td>
                <td>
                    <input type="file" name="file[]" id="${fileId}" class="form-control" required placeholder="Custom error message" accept=".srt,.pdf,.docx,.xlsx,.txt,.csv,.zip">
                    <span class="invalid-feedback">
                        The field is required.
                    </span>
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

        // Initialize select2 for the new row
        $(`#${selectId}`).select2();
    }

    // Function to update dropdown options
    function updateDropdownOptions() {
        var selectedValues = [];

        // Collect selected values from all dropdowns
        $('select[name="language_id[]"]').each(function() {
            if ($(this).val()) {
                selectedValues.push($(this).val());
            }
        });

        // Update each dropdown to disable selected options in others
        $('select[name="language_id[]"]').each(function() {
            var currentVal = $(this).val(); // Current value of this select
            $(this).find('option').each(function() {
                $(this).prop('disabled', false); // Enable all options initially

                // Disable the option if it is selected in another dropdown
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
    $(document).on('change', 'select[name="language_id[]"]', function() {
        updateDropdownOptions();
    });

    // Remove row functionality
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        updateDropdownOptions();

        count--;
        // Update the row numbers after a row is removed
        $('.t-body tr').each(function(index) {
            $(this).find('td:first-child .badge').text(index + 1);
        });
    });
});


    </script>
    
  @endsection
@endsection