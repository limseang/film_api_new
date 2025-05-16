@extends('layouts.master')

@section('title')
{{__('global.artical')}}
@endsection

@if(authorize('can create artical'))
    @section('breadcrumb-topbar')
        <a href="{{route('artical.create')}}" data-toggle="tooltip" role="button" type="button" aria-haspopup="true" class="btn btn-primary d-flex align-items-center">
            <span class="{{config('setup.button_add')}}"> <i class="ph-plus"></i> </span> &nbsp; <span> {{__('global.add_new')}} </span>
        </a>
    @endsection
@endif

@section('content')
<div class="container-fluid py-4">
    <!-- Search field -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 font-weight-bold text-primary">
                        <i class="ph-funnel mr-2"></i>{{ __('global.filter_options') }}
                    </h5>
                    <a href="#" class="text-body" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="true">
                        <i class="ph-caret-down"></i>
                    </a>
                </div>
                
                <div class="collapse show" id="filterCollapse">
                    <div class="card-body">
                        <form id="filter" autocomplete="off">
                            <div class="mb-3 row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">{{ trans('global.search_by_title') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ph-text-t"></i></span>
                                        <input type="text" id="name" name="name" placeholder="{{ trans('global.search_by_title') }}" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">{{ trans('sma.action_record') }}</label>
                                    <select id="soft_delete" class="{{ config('setup.input_select2') }} form-select">
                                        <option value="active_records">{{ __('sma.active_records') }}</option>
                                        <option value="deleted">{{ __('sma.deleted') }}</option>
                                        <option value="all_records">{{ __('sma.all_records') }}</option>
                                    </select>
                                </div>
                                
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="origin">{{ trans('sma.origin') }}</label>
                                    <select id="origin" class="{{ config('setup.input_select2') }}" name="origin">
                                        <option value="">{{ __('global.please_select') }}</option>
                                     
                                        <div>{{ $article->origin->name ?? 'N/A' }}</div>
                                      
                                    </select>
                                </div>
                                
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="type">{{ trans('sma.type') }}</label>
                                    <select id="type" class="{{ config('setup.input_select2') }}" name="type">
                                        <option value="">{{ __('global.please_select') }}</option>
                                       
                                        <div>Type</div>
                                       
                                    </select>
                                </div>
                                
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="category">{{ trans('sma.category') }}</label>
                                    <select id="category" class="{{ config('setup.input_select2') }}" name="catgeory">
                                        <option value="">{{ __('global.please_select') }}</option>
                                      
                                        <div>Cateogry</div>
                                      
                                    </select>
                                </div>
                                
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">{{trans('sma.film')}}</label>
                                    <select id="film" class="{{ config('setup.input_select2') }}" name="film">
                                        <option value="">{{ __('global.please_select') }}</option>
                                     
                                        <div>Til</div>
                                      
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <button type="submit" class="{{config('setup.button_opacity_success')}} me-2">
                                    <i class="ph-magnifying-glass me-2"></i>
                                    {{ __('global.search') }}
                                </button>
                                <button type="reset" class="btn btn-light">
                                    <i class="ph-x me-2"></i>
                                    {{ __('global.reset') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /search field -->

    <!-- Articles List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0 font-weight-bold text-primary">
                        <i class="ph-newspaper mr-2"></i>{{__('global.artical')}}
                    </h3>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show py-1 px-3 mb-0" role="alert">
                            <i class="ph-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show py-1 px-3 mb-0" role="alert">
                            <i class="ph-x-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    <!-- DataTable Container -->
                    <div class="table-responsive">
                        {{ $dataTable->table(['class' => config('setup.card_datatable').' table-hover border-top', 'style' => 'width:100%']) }}
                    </div>
                    <!-- /DataTable Container -->
                </div>
            </div>
        </div>
    </div>
    <!-- /Articles List -->
</div>

@push('styles')
<style>
    /* Table styling */
    .table th, .table td {
        vertical-align: middle;
    }
    
    .table thead th {
        background-color: rgba(0,0,0,.03);
        font-weight: 600;
        border-bottom-width: 1px;
    }
    
    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,.125);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    /* Badge styling */
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
        border-radius: 0.25rem;
    }
    
    .badge-success {
        background-color: #28a745;
        color: #fff;
    }
    
    .badge-danger {
        background-color: #dc3545;
        color: #fff;
    }
    
    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }
    
    .badge-secondary {
        background-color: #6c757d;
        color: #fff;
    }
    
    /* Button styling */
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin-right: 0.25rem;
    }
    
    /* Empty state styling */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    
    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        padding-right: 0;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
    }
    
    /* Image styling */
    .img-thumbnail {
        max-height: 80px;
        border-radius: 0.25rem;
    }
    
    /* Input Group styling */
    .input-group-text {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('scripts')
{!! $dataTable->scripts() !!}
<script src="{{asset('assets/datatables/datatables_customize_'.app()->getLocale().'.js')}}"></script>
<script src="{{asset('assets/js/core.js')}}"></script>
<script>
$(document).ready(function() {
    // Initialize select2 if not already initialized by your setup
    if ($.fn.select2) {
        $('.{{ config('setup.input_select2') }}').select2({
            width: '100%',
            placeholder: '{{ __('global.please_select') }}'
        });
    }
    
    // Reset button functionality
    $('button[type="reset"]').on('click', function(e) {
        e.preventDefault();
        $('#filter')[0].reset();
        
        // Reset select2 dropdowns
        $('.{{ config('setup.input_select2') }}').val('').trigger('change');
        
        // Trigger search with empty values
        $('#filter').submit();
    });
    
    // Toggle filter section
    $('[data-toggle="collapse"]').on('click', function(e) {
        e.preventDefault();
        $(this).find('i').toggleClass('ph-caret-down ph-caret-up');
    });
    
    // Add custom class to DataTable elements
    setTimeout(function() {
        $('.dataTables_wrapper .btn').addClass('btn-sm');
        
        // Enhance DataTable appearance
        var dataTable = $('.dataTable');
        if (dataTable.length) {
            // Add responsive classes if not already present
            if (!dataTable.hasClass('table-responsive')) {
                dataTable.addClass('table-striped table-hover');
            }
            
            // Style the pagination
            $('.dataTables_paginate .paginate_button').addClass('btn btn-sm');
        }
    }, 100);
    
    // Add custom rendering for status column if needed
    // You can define your custom DataTable rendering functions here
});
</script>

<script>
// This script adds functionality to render DataTable cells with enhanced styling
$(document).ready(function() {
    // Define custom rendering functions that can be used in your DataTable
    
    // Image renderer for thumbnail column
    window.renderImage = function(data, type, row) {
        if (data) {
            return '<img src="' + data + '" class="img-thumbnail" style="max-height: 80px;" alt="Thumbnail">';
        } else {
            return '<div class="bg-light text-center py-3 rounded"><i class="ph-image text-muted"></i></div>';
        }
    };
    
    // Status renderer with badges
    window.renderStatus = function(data, type, row) {
        if (data == 1) {
            return '<span class="badge badge-success">Active</span>';
        } else {
            return '<span class="badge badge-danger">Inactive</span>';
        }
    };
    
    // Category renderer with badge
    window.renderCategory = function(data, type, row) {
        if (data) {
            return '<span class="badge badge-info">' + data + '</span>';
        } else {
            return '<span class="badge badge-secondary">No Category</span>';
        }
    };
    
    // Actions renderer with buttons group
    window.renderActions = function(data, type, row) {
        var buttons = '<div class="btn-group" role="group">';
        
        // Edit button
        buttons += '<a href="' + route('artical.edit', row.id) + '" class="btn btn-sm btn-outline-primary" title="Edit">';
        buttons += '<i class="ph-pencil"></i></a>';
        
        // Status toggle button
        buttons += '<a href="' + route('artical.status', row.id) + '" class="btn btn-sm btn-outline-warning" title="Toggle Status">';
        buttons += '<i class="ph-power"></i></a>';
        
        // Delete button with confirmation modal
        buttons += '<button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-toggle="modal" ';
        buttons += 'data-target="#deleteModal' + row.id + '">';
        buttons += '<i class="ph-trash"></i></button>';
        
        buttons += '</div>';
        
        // Modal HTML for delete confirmation
        buttons += '<div class="modal fade" id="deleteModal' + row.id + '" tabindex="-1" role="dialog" aria-hidden="true">';
        buttons += '<div class="modal-dialog" role="document"><div class="modal-content">';
        buttons += '<div class="modal-header"><h5 class="modal-title">Confirm Delete</h5>';
        buttons += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        buttons += '<span aria-hidden="true">&times;</span></button></div>';
        buttons += '<div class="modal-body">Are you sure you want to delete this article?</div>';
        buttons += '<div class="modal-footer">';
        buttons += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
        buttons += '<a href="' + route('artical.delete', row.id) + '" class="btn btn-danger">Delete</a>';
        buttons += '</div></div></div></div>';
        
        return buttons;
    };
});
</script>
@endsection
@endsection