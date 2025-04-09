<div class="btn-group dropdown">
    <a href="javascript:;" class="dropdown-toggle btn btn-light btn-sm btn-active-light-primary me-1" data-bs-toggle="dropdown">
         <i class="fa fa-bars"></i>
         <span class="visually-hidden">Toggle Dropdown</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ route('request_film.edit', $requestFilm->id) }}" class="dropdown-item">
            <i class="fa fa-edit"></i> {{ __('sma.edit') }}
        </a>
        <a href="{{ route('request_film.delete', $requestFilm->id) }}" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this request?');">
            <i class="fa fa-trash"></i> {{ __('sma.delete') }}
        </a>
    </div>
</div>