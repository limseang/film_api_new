<div class="d-inline-flex">
    @if(!($table->deleted_at)) 
        @if(authorize('can change status artist'))
            <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status"
                data-action="{{ route('artist.status', $table->id) }}" 
                data-html="true" data-placement="left">
                <span class="{{$table->status == 1 ? config('setup.button-opacity-success') : config('setup.button-opacity-danger')}} p-1">
                <i class="{{$table->status == 1 ? 'ph-toggle-right' : 'ph-toggle-left'}}" style="font-size:14px"></i>
                </span>
            </a>
            &nbsp;
        @endif
        @if(authorize('can edit artist'))
            <a href="{{route('artist.edit', $table->id)}}" class="text-success">
                <span class="{{config('setup.button-opacity-success')}}">
                    <i class="ph-note-pencil" style="font-size:14px"></i> 
                </span>
            </a>
            &nbsp;
        @endif
        @if(authorize('can delete artist'))
            <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
                data-action="{{ route('artist.delete', $table->id) }}" 
                data-html="true" data-placement="left">
                <span class="{{config('setup.button-opacity-danger')}}">
                    <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
                </span>
            </a>
        @endif
    @else
        @if(authorize('can restore artist'))
            <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
                data-action="{{ route('artist.restore', $table->id) }}" 
                data-html="true" data-placement="left">
                <span class="{{config('setup.button-opacity-info')}}">
                    <i class="fas fa-sync text-md text-info" style="font-size:14px"></i>
                </span>
            </a>
        @endif
    @endif

</div>
