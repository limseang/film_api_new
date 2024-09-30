@if(Session::has('success'))
    <div class="alert alert-success border-0 alert-dismissible fade show" role="alert">
        <div>
            {{session('success')}}
        </div>
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close">
           
        </button>
    </div>

@endif
@if(Session::has('error'))
    <div class="alert alert-warning alert-icon-start alert-dismissible fade show">
        <span class="alert-icon bg-warning text-white">
            <i class="ph-warning-circle"></i>
        </span>
        {{session('error')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
  @if(Session::has('warning'))
    <div class="alert alert-warning border-0 alert-dismissible fade show" role="alert">
        <div>
            {{session('warning')}}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </button>
    </div>
@endif
@if(count($errors)>0 && $errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-warning border-0 alert-dismissible fade show" role="alert">
            <div>
                {{$error}}
            </div>
            <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="alert">
            </button>
        </div>
    @endforeach
@endif

@if(session()->get('type'))
    @if(!in_array(session()->get('type'),array("error", "warning", "success", "info")))
        <div class="p-2 shadow page-header page-header-light">
            <div class="page-header-content text-danger">
                <h6 class="mb-1"><i class="{{ session()->get('icon') }} me-1"></i> {{ session()->get('title') }}</h6>
                <label class="form-label">{{ session()->get('text') }}</label>
            </div>
        </div>
    @endif
@endif