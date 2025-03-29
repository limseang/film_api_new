@extends('layouts.master')
@section('title')
{{__('sma.assign_film')}}
@endsection
@section('content')
    <!-- Search field -->
    <div class="card">
        <div class="card-body">
            <form id="filter" autocomplete="off">
                <div class="mb-3 row">
                    <div class="col-lg-4">
                        <label class="form-label">{{ trans('global.search_by_title') }}</label>
                        <input type="text" id="name" name="name" placeholder="{{ trans('global.search_by_title') }}" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">{{ trans('sma.cinema') }}</label>
                        <input type="text" id="available" name="available" value="{{$available_in->name ?? ''}}" class="form-control bg-primary bg-opacity-10 text-primary">
                        <input type="hidden" id="available_id" name="available_id" value="{{$available_in->id ?? ''}}">
                    </div>
                </div>


                <div class="d-flex align-items-center">
                    <button type="submit" class="{{config('setup.button_opacity_success')}}">
                        <i class="ph-magnifying-glass me-2"></i>
                        {{ __('global.search') }}
                    </button>
                    &nbsp;
                    <a href="#" class="{{config('setup.button_opacity_info')}} add_available_film"><i class="ph-plus me-2"></i>{{__('sma.add_film')}}</a>
                </div>
            </form>
        </div>
    </div>

    <div id="modal_form_inline" class="modal fade"data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
  
          </div>
      </div>
    </div>
    <!-- /search field -->


    <!-- List Record blocks -->
            {{-- {{ $data['page_name'] }} --}}
    <div class="card">
        <div class="car-body">
            <div class="m-2">
            {{ $dataTable->table(['class' => config('setup.card_datatable'), true]) }}
            </div>
        </div>
    </div>
    <!-- /List Record blocks -->
@section('scripts')
{!! $dataTable->scripts() !!}
    <script src="{{asset('assets/datatables/datatables_customize_'.app()->getLocale().'.js')}}"></script>
    <script src="{{asset('assets/js/core.js')}}"></script>
<script>
   $(document).ready(function() {
     $('.add_available_film').on('click', function(e) {
        e.preventDefault();
        var available_id = $('#available_id').val();
        var $button = $(this);
        $.ajax({
            url: "{{ route('available_in.add_available_in_film') }}",
            type: 'GET',
            data: {
                available_id: available_id
            },
            beforeSend: function() {
                // show_properties  find data-language-id and add spinner
                $button.prop('disabled', true);
                $button.html("<div class='spinner-border me-2' role='status'><span class='visually-hidden'>Loading...</span></div>");
            },
            success: function(response) {
                $button.prop('disabled', false);
                $button.html(`<i class="ph-plus me-2"></i>{{__('sma.add_film')}}`);
                $('#modal_form_inline').modal('show');
                $('#modal_form_inline .modal-content').html(response).show();
            }, 
            error: function(response) {
                $button.prop('disabled', false);
                $button.html(`<i class="ph-plus me-2"></i>{{__('sma.add_film')}}`);
                        new Noty({
                            text: 'An error occurred. Please try again.',
                            type: 'error',
                        }).show();
                    }
        });
    });
  });
</script>
@endsection
@endsection
