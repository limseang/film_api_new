<div class="d-inline-flex"> 
    @if(!($table->deleted_at)) 
        @if(authorize('can edit artical'))
            <a href="{{route('artical.edit', $table->id)}}" class="text-success">
                <span class="{{config('setup.button-opacity-success')}}">
                <i class="ph-note-pencil" style="font-size:14px"></i> 
                </span>
            </a>
            &nbsp;
        @endif
        @if(authorize('can delete artical'))
            <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete"
                data-action="{{ route('artical.delete', $table->id) }}" 
                data-html="true" data-placement="left">
                <span class="{{config('setup.button-opacity-danger')}}">
                <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
                </span>
            </a>
        @endif        
    @else
        @if(authorize('can restore artical'))
            <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
                data-action="{{ route('artical.restore', $table->id) }}" 
                data-html="true" data-placement="left">
                <span class="{{config('setup.button-opacity-info')}}">
                    <i class="fas fa-sync text-md text-info" style="font-size:14px"></i>
                </span>
            </a>
        @endif
    @endif

</div>
