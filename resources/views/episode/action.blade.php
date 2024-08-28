<div class="d-inline-flex"> 
    @if(!($table->deleted_at))
    <div class="dropdown">
        <a href="#" class="text-body" data-bs-toggle="dropdown">
            <i class="ph-list text-primary"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="{{route('episode.add-subtitle', $table->id)}}" class="dropdown-item">
                <span style="white-space: nowrap">
                    <span style="white-space: nowrap;" class="{{config('setup.button-opacity-success')}}">
                        <i class="ph-file-plus" style="font-size:14px; text-bold"></i>
                    </span>
                    {{trans('sma.add_subtitle')}}
                </span>
            </a>
            <a href="{{route('episode.add-subtitle', $table->id)}}" class="dropdown-item">
                <span style="white-space: nowrap">
                    <span style="white-space: nowrap;" class="{{config('setup.button-opacity-primary')}}">
                        <i class="ph-file-text" style="font-size:14px; text-bold"></i>
                    </span>
                    {{trans('sma.edit_subtitle')}}
                </span>
            </a>
            <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status dropdown-item" style=""
                data-action="{{ route('episode.status', $table->id) }}" 
                data-html="true" data-placement="left">
                <span style="white-space: nowrap">
                    <span class="{{$table->status == 1 ? config('setup.button-opacity-success') : config('setup.button-opacity-danger')}} p-1">
                        <i class="{{$table->status == 1 ? 'ph-toggle-right' : 'ph-toggle-left'}}" style="font-size:14px"></i>
                    </span>
                    {{trans('sma.status')}}
                </span>
            </a>
            <a href="{{route('episode.edit', $table->id)}}" class="dropdown-item">
                <span style="white-space: nowrap">
                    <span class="{{config('setup.button-opacity-success')}}">
                        <i class="ph-note-pencil" style="font-size:14px"></i> 
                    </span>
                    {{trans('sma.edit_this_record')}}
                </span>
            </a>
            <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete dropdown-item" style=""
                data-action="{{ route('episode.delete', $table->id) }}" 
                data-html="true" data-placement="left">
                <span style="white-space: nowrap">
                    <span class="{{config('setup.button-opacity-danger')}}">
                        <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
                    </span>
                    {{trans('sma.delete_this_record')}}
                </span>
            </a>
        </div>
    </div>
    @else

      <a href="#" type="button" data-click="bpo-status{{$table->id}}" class="bpo-status" style="padding: 0.10rem"
            data-action="{{ route('episode.restore', $table->id) }}" 
            data-html="true" data-placement="left">
            <span class="config('setup.button-opacity-info')">
                <i class="fas fa-sync text-md text-info" style="font-size:14px"></i>
            </span>
        </a>
        &nbsp;
        <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete dropdown-item" style=""
            data-action="{{ route('episode.delete-trash', $table->id) }}" 
            data-html="true" data-placement="left">
            <span style="{{config('setup.button-opacity-info')}}">
                <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
            </span>
        </a>
    @endif
    </div>