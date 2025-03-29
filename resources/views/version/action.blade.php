<div class="d-inline-flex">
    @if(authorize('can change status version'))
        <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
            data-action="{{ route('version.status', $table->id) }}" 
            data-html="true" data-placement="left">
            <span class="{{$table->status == 1 ? config('setup.button-opacity-success') : config('setup.button-opacity-danger')}} p-1">
                <i class="{{$table->status == 1 ? 'ph-toggle-right' : 'ph-toggle-left'}}" style="font-size:14px"></i>
            </span>
        </a>
        &nbsp;
    @endif
    @if(authorize('can edit version'))
        <a href="{{route('version.edit', $table->id)}}" class="text-success">
            <span class="{{config('setup.button-opacity-success')}}">
                <i class="ph-note-pencil" style="font-size:14px"></i> 
            </span>
        </a>
        &nbsp;
    @endif
    @if(authorize('can delete version'))
        <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
            data-action="{{ route('version.delete', $table->id) }}" 
            data-html="true" data-placement="left">
            <span class="{{config('setup.button-opacity-danger')}}">
                <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
            </span>
        </a>
    @endif
    

</div>
