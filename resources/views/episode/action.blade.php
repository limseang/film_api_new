<div class="d-inline-flex"> 
    <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
        data-action="{{ route('episode.status', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="{{$table->status == 1 ? 'fa fa-toggle-on
        text-success danger-success' : 'fa fa-toggle-off text-danger'}} text-opacity-10" style="font-size: 25px"></i>
    </a>
    &nbsp;
    <a href="{{route('episode.edit', $table->id)}}" class="dropdown-item">
        <span style="white-space: nowrap">
            <i class="fas fa-edit text-success" style="font-size:14px"></i>
        </span>
    </a>
    &nbsp;
    <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete dropdown-item" style=""
        data-action="{{ route('episode.delete', $table->id) }}" 
        data-html="true" data-placement="left">
        <span style="white-space: nowrap">
            <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
        </span>
    </a>
    
    </div>