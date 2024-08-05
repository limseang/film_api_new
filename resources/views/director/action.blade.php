<div class="d-inline-flex"> 
    <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
        data-action="{{ route('director.status', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-bars {{$table->status == 1 ? 'text-success' : 'text-danger'}} text-opacity-10"></i>
    </a>
    &nbsp;
    <a href="{{route('director.edit', $table->id)}}" class="text-success">
        <i class="fas fa-edit" style="font-size:14px"></i> 
    </a>
    {{-- &nbsp; --}}
    <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
        data-action="{{ route('director.delete', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
    </a>
    

</div>
