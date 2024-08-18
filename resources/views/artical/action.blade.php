<div class="d-inline-flex"> 
    @if(!($table->deleted_at)) 
    <a href="{{route('artical.edit', $table->id)}}" class="text-success">
        <i class="fas fa-edit" style="font-size:14px"></i> 
    </a>
    {{-- &nbsp; --}}
    <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
        data-action="{{ route('artical.delete', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
    </a>
    
    @else
    <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
        data-action="{{ route('artical.restore', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-sync text-md text-info" style="font-size:14px"></i>
    </a>
    @endif

</div>
