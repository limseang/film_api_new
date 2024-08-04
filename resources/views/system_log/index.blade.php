@extends('layouts.master')
@section('title')
{{__('sma.system_user_log')}}
@endsection
@section('content')
    <!-- Search field -->
    <div class="card">
        <div class="card-body">
            <form id="filter" autocomplete="off">
                <div class="mb-3 row">
                    <div class="col-lg-4">
                        <label class="form-label">{{ trans('sma.search_by_title') }}</label>
                        <input type="text" id="name" name="name" placeholder="{{ trans('sma.search_by_title') }}" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">{{ trans('sma.publish') }}</label>
                        <select id="publish" class="{{ config('setup.input_select2') }}">
                            <option value="">{{ __('sma.please_select') }}</option>
                            <option value="Y">{{ __('sma.publish_yes') }}</option>
                            <option value="N">{{ __('sma.publish_no') }}</option>
                        </select>
                    </div>
                </div>


                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-success">
                        <i class="ph-magnifying-glass me-2"></i>
                        {{ __('sma.search') }}
                    </button>
                </div>
            </form>
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
@include('system_log.modal_properties')
@section('scripts')
{!! $dataTable->scripts() !!}
    <script src="{{asset('assets/datatables/datatables_customize_'.app()->getLocale().'.js')}}"></script>
    <script src="{{asset('assets/js/core.js')}}"></script>
    @include('system_log.script')
@endsection
@endsection
