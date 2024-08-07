@extends('layouts.master')
@section('title')
{{__('global.user')}}
@endsection
@section('breadcrumb-topbar')
<span data-toggle="tooltip" role="button" type="button" aria-haspopup="true" title="{{__('global.add_new')}}"> 
    <a href="{{route('user.create')}}" type="button" class="{{config('setup.button_add')}}">
     <i class="ph-plus"></i>
  </a>
  </span>

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
                    <button type="submit" class="btn btn-success">
                        <i class="ph-magnifying-glass me-2"></i>
                        {{ __('global.search') }}
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
@section('scripts')
{!! $dataTable->scripts() !!}
    <script src="{{asset('assets/datatables/datatables_customize_'.app()->getLocale().'.js')}}"></script>
    <script src="{{asset('assets/js/core.js')}}"></script>
<script>

</script>
@endsection
@endsection
