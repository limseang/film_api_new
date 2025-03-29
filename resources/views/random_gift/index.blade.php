@extends('layouts.master')
@section('title')
{{__('sma.gift')}}
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
                        <label class="form-label">{{ trans('global.publish') }}</label>
                        <select id="publish" class="{{ config('setup.input_select2') }}" >
                            <option value="">{{ __('global.please_select') }}</option>
                            <option value="Y">{{ __('global.publish_yes') }}</option>
                            <option value="N">{{ __('global.publish_no') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">{{ trans('sma.action_record') }}</label>
                        <select id="soft_delete" class="{{ config('setup.input_select2') }}">
                            <option value="active_records">{{ __('sma.active_records') }}</option>
                            <option value="deleted">{{ __('sma.deleted') }}</option>
                            <option value="all_records">{{ __('sma.all_records') }}</option>
                        </select>
                    </div>
                </div>


                <div class="d-flex align-items-center">
                    <button type="submit" class="{{config('setup.button_opacity_success')}}">
                        <i class="ph-magnifying-glass me-2"></i>
                        {{ __('global.search') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- /search field -->

    <div id="modal_form_inline" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
    
            </div>
        </div>
      </div>
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
    $(document).ready(function(){
        $('body').on('click','.show_detail_cinema_branch',function(e){
                e.preventDefault();
                var cinema_branch_id = $(this).data('cinema-branch-id');
                var $button = $(this);
                var span = $button.find('span');
                $.ajax({
                    url: "{{ route('cinema_branch.show_detail') }}",
                    type: 'GET',
                    data: {
                        cinema_branch_id: cinema_branch_id
                    },
                    beforeSend: function() {
                        // after span
                        span.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(data) {
                        $('#modal_form_inline').find('.modal-content').html(data);
                        $('#modal_form_inline').modal('show');
                        span.html('<i class="fa fa-eye" style="font-size:14px"></i>');
                    },
                    error: function(data) {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
            
    });
</script>
@endsection
@endsection
