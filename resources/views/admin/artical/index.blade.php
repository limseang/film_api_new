@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0 font-weight-bold text-primary">
                        <i class="fas fa-newspaper mr-2"></i>Articles Management
                    </h3>
                    <a href="{{ route('artical.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> New Article
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Image</th>
                                    <th width="20%">Title</th>
                                    <th width="15%">Category</th>
                                    <th width="10%">Type</th>
                                    <th width="10%">Origin</th>
                                    <th width="10%">Status</th>
                                    <th width="15%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr>
                                        <td>{{ $article->id }}</td>
                                        <td>
                                            @if($article->image)
                                                <img src="{{ $article->image }}" alt="{{ $article->title }}" 
                                                     class="img-thumbnail" style="max-height: 80px;">
                                            @else
                                                <div class="bg-light text-center py-3 rounded">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="font-weight-medium">{{ Str::limit($article->title, 40) }}</td>
                                        <td>
                                            @if($article->category)
                                                <span class="badge badge-info">{{ $article->category->name }}</span>
                                            @else
                                                <span class="badge badge-secondary">No Category</span>
                                            @endif
                                        </td>
                                        <td>{{ $article->type->name ?? 'N/A' }}</td>
                                        <td>{{ $article->origin->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($article->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('artical.edit', $article->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('artical.status', $article->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Delete" data-toggle="modal" 
                                                        data-target="#deleteModal{{ $article->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Confirmation Modal -->
                                            <div class="modal fade" id="deleteModal{{ $article->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Delete</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete the article "{{ $article->title }}"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <a href="{{ route('artical.delete', $article->id) }}" class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                                <h5>No Articles Found</h5>
                                                <p class="text-muted">Get started by creating your first article</p>
                                                <a href="{{ route('artical.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus mr-1"></i> Create New Article
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($articles->count() > 0)
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $articles->firstItem() ?? 0 }} to {{ $articles->lastItem() ?? 0 }} of {{ $articles->total() }} entries
                        </div>
                        <div>
                            {{ $articles->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
    
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin-right: 0.25rem;
    }
</style>
@endpush
@endsection