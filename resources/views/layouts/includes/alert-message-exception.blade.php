@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div>
            {{session('success')}}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if(Session::has('error'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div>
        {{session('error')}}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
  @if(Session::has('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div>
            {{session('warning')}}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if(count($errors)>0 && $errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div>
                {{$error}}
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endforeach
@endif