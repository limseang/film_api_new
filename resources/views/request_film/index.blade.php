@extends('layouts.master')
@section('title')
Request Film
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            {!! $dataTable->table(['class' => 'table table-striped table-hover dataTable no-footer','style'=> "width:100%"]) !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}
@endpush