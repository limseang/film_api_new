<div class="d-inline-flex"> 
    <a href="{{route('film.edit', $table->id)}}" class="text-success">
        <i class="fas fa-edit" style="font-size:14px"></i> 
    </a>
    {{-- &nbsp; --}}
    <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
        data-action="{{ route('film.delete', $table->id) }}" 
        data-html="true" data-placement="left">
        <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
    </a>
    

</div>
