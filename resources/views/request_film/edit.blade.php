@extends('layouts.master')
@section('title')
Edit Request Film
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Edit Request Film</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('request_film.update', $requestFilm->id) }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Film Name</label>
                        <input type="text" class="form-control" value="{{ $requestFilm->film_name }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Requested By</label>
                        <input type="text" class="form-control" value="{{ $requestFilm->user ? $requestFilm->user->name : 'Unknown' }}" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Film Link</label>
                        <input type="text" class="form-control" value="{{ $requestFilm->film_link }}" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Film Description</label>
                        <textarea class="form-control" rows="3" readonly>{{ $requestFilm->film_description }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $requestFilm->status == 1 ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ $requestFilm->status == 2 ? 'selected' : '' }}>Completed</option>
                            <option value="3" {{ $requestFilm->status == 3 ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Notes (Required if rejecting the request)</label>
                        <textarea name="noted" class="form-control" rows="3">{{ $requestFilm->noted }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('request_film.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection