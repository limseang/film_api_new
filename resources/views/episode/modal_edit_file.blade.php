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
        <h5 class="modal-title">{{trans('sma.edit_subtitle')}} <span class="{{config('setup.badge_primary')}}">{{$subtitle->language->name ?? ''}}</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <br>
    <div class="row g-3 align-items-center p-2">
        <div class="col-6">
            <label class="form-label">{{trans('sma.subtitle')}}</label>
            <input type="text" id="language_id_show" class="form-control bg-primary bg-opacity-10 text-primary" value="{{$subtitle->language->name ?? ''}}" readonly>
            <span class="invalid-feedback-success">
                The valid language is success.
            </span>
            <input type="hidden" name="language_id" id="language_id" value="{{$subtitle->language_id ?? ''}}">
        </div>
    
        <div class="col-6">
            <label class="form-label" for="file">{{trans('sma.file_subtitle')}} <sup class="text-danger">*</sup></label>
            <input type="file" name="file" id="file" class="form-control" required placeholder="Custom error message" accept=".srt,.pdf,.docx,.xlsx,.txt,.csv,.zip">
            <span class="invalid-feedback">
                The field is required.
            </span>
        </div>
    </div>
    <br>
    <div class="modal-footer">
        <button type="button" class="btn-link {{config('setup.button_opacity_danger')}} mb-3" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3 button-submit" name="submit">
            <i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}
        </button>
    </div>
    
    <script>
        $(document).ready(function() {
            $('.button-submit').on('click', function(e) {
                e.preventDefault();
                
                // Get file input and language ID
                var fileInput = $('#file')[0].files[0]; // Get the actual file object
                var language_id = $('#language_id').val();
    
                // Validation: Check if the file is empty but language ID is not
                if (!fileInput && language_id != '') {
                    $('#language_id_show').addClass('is-not-invalid');
                    $('#file').addClass('is-invalid');
                    return false;
                } else {
                    $('#language_id_show').removeClass('is-invalid');
                    $('#file').removeClass('is-invalid');
                }
    
                // Prepare FormData object
                var formData = new FormData();
                formData.append('file', fileInput);
                formData.append('language_id', language_id);
                formData.append('_method', 'POST'); // If using PUT method for update
    
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
    
                // Perform AJAX request
                $.ajax({
                    url: "{{route('episode.update_subtitle', $subtitle->id)}}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.button-submit').attr('disabled', true);
                        $('.button-submit').html("<div class='spinner-border me-2' role='status'><span class='visually-hidden'>Loading...</span></div>");
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#modal_form_inline').modal('hide');
                            new Noty({
                                text: 'The subtitle has been updated successfully',
                                type: 'success',
                            }).show();
                        } else {
                            new Noty({
                                text: 'The subtitle has not been updated successfully',
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
                        $('.button-submit').html("<i class='{{ config('setup.edit_icon') }} me-2'></i> {{__('sma.update')}}");
                    }
                });
            });
        });
    </script>
    