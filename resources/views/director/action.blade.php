<div class="d-inline-flex">
    <span data-toggle="tooltip" role="button" type="button" aria-haspopup="true" title="{{__('sma.change_permission')}}"> 
        <a href="{{route('role.permission', $table->id)}}" class="text-success"><i class="fas fa-bars"></i>
        </a>
    </span>
    &nbsp;
    <a href="{{route('role.edit', $table->id)}}" class="text-success">
        <i class="fas fa-edit" style="font-size:14px"></i> 
    </a>
    {{-- &nbsp; --}}
    <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
        data-action="{{ route('role.delete', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
    </a>
    

</div>
