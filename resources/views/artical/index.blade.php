@extends('layouts.master')
@section('title')
{{__('global.artical')}}
@endsection
@section('breadcrumb-topbar')
<a href="{{route('artical.create')}}" data-toggle="tooltip" role="button" type="button" aria-haspopup="true" class="d-flex align-items-center text-body">
    <span class="{{config('setup.button_add')}}"> <i class="ph-plus"></i> </span> &nbsp; <span> {{__('global.add_new')}} </span>
  </a>
  

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
                        <label class="form-label">{{ trans('sma.action_record') }}</label>
                        <select id="soft_delete" class="{{ config('setup.input_select2') }}">
                            <option value="active_records">{{ __('sma.active_records') }}</option>
                            <option value="deleted">{{ __('sma.deleted') }}</option>
                            <option value="all_records">{{ __('sma.all_records') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="type">{{ trans('sma.origin') }}</label>
                      <select id="origin" class="{{ config('setup.input_select2') }}" name="origin" >
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($origins as $value)
                          <option value="{{ $value->id }}" >{{$value->name }}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="type">{{ trans('sma.type') }}</label>
                      <select id="type" class="{{ config('setup.input_select2') }}" name="type" >
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($type as $value)
                          <option value="{{ $value->id }}" >{{$value->name }}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="catgeory">{{ trans('sma.category') }}</label>
                      <select id="category" class="{{ config('setup.input_select2') }}" name="catgeory" >
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($categories as $value)
                          <option value="{{ $value->id }}" >{{$value->name }}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">{{trans('sma.film')}}</label>
                        <select id="film" class="{{ config('setup.input_select2') }}" name="film" >
                            <option value="">{{ __('global.please_select') }}</option>
                            @foreach($film as $value)
                            <option value="{{ $value->id }}">{{$value->title }}</option>
                            @endforeach
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
