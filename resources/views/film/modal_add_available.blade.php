<style>
    .is-not-invalid{
        border: 1px solid #198754;
    }
    .is-not-invalid:focus{
        border: 1px solid #198754;
    }
    .is-not-invalid + .invalid-feedback-success{
        display: block;
        color: #198754;
        font-size: 12px;
    }
    .invalid-feedback-success{
        display: none;
    }
    </style>

    <div class="modal-header bg-success bg-opacity-10 text-success">
        <h5 class="modal-title">{{trans('sma.add_cinema')}} <span class="{{config('setup.badge_primary')}}">{{$film->title ?? ''}}</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <br>
    <div class="row g-3 align-items-center p-2">
        <div class="col-6">
            <label class="form-label">{{trans('sma.film')}}</label>
            <input type="text" id="film_id_show" class="form-control bg-primary bg-opacity-10 text-primary" value="{{$film->title ?? ''}}" readonly>
            <span class="invalid-feedback-success">
                The valid cinema is success.
            </span>
            <input type="hidden" name="film_id" id="film_id" value="{{$film->id ?? ''}}">
        </div>
    
        <div class="col-6">
        <label class="form-label" for="available_id">{{ trans('sma.cinema') }}</label>
        <select id="available_id" class="{{config('setup.input_select2') }}" name="available_id"  required placeholder="Please Select">
            <option value="">{{ __('global.please_select') }}</option>
            @if($availables)
                @foreach($availables as $value)
                <option value="{{ $value->id }}">
                    {{ $value->name }}
                </option>
                @endforeach
            @endif
        </select>
            <span class="invalid-feedback">
            The field is required.
            </span>
        </div>
    </div>
    <br>
    <div class="modal-footer">
        <button type="button" class="btn-link {{config('setup.button_opacity_danger')}} mb-3" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3 button-submit" name="submit">
            <i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.add')}}
        </button>
    </div>
    
    <script>
        $(document).ready(function() {
            var dataTables = $('.dataTable').DataTable();
            $('#available_id').select2({
                placeholder: "Please select", // Text shown when nothing is selected
                allowClear: true, // Allow clearing the selection
                width: '100%', // Full width of the container
                dropdownAutoWidth: true, // Make the dropdown auto width
                dropdownParent: $('#modal_form_inline')
            });
            $('.button-submit').on('click', function(e) {
                e.preventDefault();
                
                // Get file input and language ID
                var film_id = $('#film_id').val();
                var available_id = $('#available_id').val();
    
                // Validation: Check if the film_id array is empty but available ID is not
                if (film_id.length ===0 && available_id != '') {
                    $('#film_in_show').addClass('is-not-invalid');
                    $('#available_id').addClass('is-invalid');
                    return false;
                } else {
                    $('#film_in_show').removeClass('is-invalid');
                    $('#available_id').removeClass('is-invalid');
                }
    
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
    
                // Perform AJAX request
                $.ajax({
                    url: "{{route('film.store_available')}}",
                    type: "POST",
                    data: {
                        film_id: film_id,
                        available_id: available_id
                    },
                    beforeSend: function() {
                        $('.button-submit').attr('disabled', true);
                        $('.button-submit').html("<div class='spinner-border me-2' role='status'><span class='visually-hidden'>Loading...</span></div>");
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#modal_form_inline').modal('hide');
                            // reload the datatable
                            dataTables.ajax.reload();
                            new Noty({
                                text: 'The cinema has been added successfully',
                                type: 'success',
                            }).show();
                        } else {
                            new Noty({
                                text: 'The cinema has not been added successfully',
                                type: 'error',
                            }).show();
                        }
                    },
                    error: function(response) {
                        new Noty({
                            text: 'An error occurred. Please try again.',
                            type: 'error',
                        }).show();
                        $('.button-submit').attr('disabled', false);
                        $('.button-submit').html("<i class='{{ config('setup.edit_icon') }} me-2'></i> {{__('sma.add')}}");
                    }
                });
            });
        });
    </script>
    