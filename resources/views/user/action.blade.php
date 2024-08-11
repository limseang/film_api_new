<div class="d-inline-flex">
    @if(!($table->deleted_at)) 
        <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
            data-action="{{ route('user.status', $table->id) }}" 
            data-html="true" data-placement="left">
            <i class="{{$table->status == 1 ? 'fa fa-toggle-on
            text-success danger-success' : 'fa fa-toggle-off text-danger'}} text-opacity-10" style="font-size: 25px"></i>
        </a>
        &nbsp;
        <a href="{{route('user.edit', $table->id)}}" class="text-success">
            <i class="fas fa-edit" style="font-size:14px"></i> 
        </a>
        {{-- &nbsp; --}}
        <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
            data-action="{{ route('user.delete', $table->id) }}" 
            data-html="true" data-placement="left">
            <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
        </a>
    @else
    <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
        data-action="{{ route('user.restore', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-sync text-md text-info" style="font-size:14px"></i>
    </a>
    @endif

</div>
