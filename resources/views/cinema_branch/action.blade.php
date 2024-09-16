<div class="d-inline-flex"> 
    <a href="javascript:void(0)" class="text-success show_detail_cinema_branch" data-cinema-branch-id="{{$table->id}}">
        <span class="{{config('setup.button-opacity-success')}} span-show">
            <i class="fa fa-eye" style="font-size:14px"></i> 
        </span>
    </a>
        <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
            data-action="{{ route('cinema_branch.status', $table->id) }}" 
            data-html="true" data-placement="left">
            <span class="{{$table->status == 1 ? config('setup.button-opacity-success') : config('setup.button-opacity-danger')}} p-1">
                <i class="{{$table->status == 1 ? 'ph-toggle-right' : 'ph-toggle-left'}}" style="font-size:14px"></i>
                </span>
        </a>
        &nbsp;
        <a href="{{route('cinema_branch.edit', $table->id)}}" class="text-success">
            <span class="{{config('setup.button-opacity-success')}}">
                <i class="ph-note-pencil" style="font-size:14px"></i> 
            </span>
        </a>
        {{-- &nbsp; --}}
        <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
            data-action="{{ route('cinema_branch.delete', $table->id) }}" 
            data-html="true" data-placement="left">
            <span class="{{config('setup.button-opacity-danger')}}">
                <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
            </span>
        </a>
    
</div>
