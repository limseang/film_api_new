<div class="d-inline-flex"> 
    <div class="dropdown">
        <a href="#" class="text-body" data-bs-toggle="dropdown">
             <i class="ph-list text-primary"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="{{route('film.show-episode', $table->id)}}" class="dropdown-item">
                <span style="white-space: nowrap">
                    <i class="fas fa-file-video text-info" style="font-size:17px"></i> {{trans('sma.show_episodes')}}
                </span>
            </a>
            <a href="{{route('film.edit', $table->id)}}" class="dropdown-item">
                <span style="white-space: nowrap">
                    <i class="fas fa-edit text-success" style="font-size:14px"></i> {{trans('sma.edit_this_record')}}
                </span>
            </a>
            <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete dropdown-item" style=""
                data-action="{{ route('film.delete', $table->id) }}" 
                data-html="true" data-placement="left">
                <span style="white-space: nowrap">
                    <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>  {{trans('sma.delete_this_record')}}
                </span>
            </a>
        </div>
    </div>
    

</div>
