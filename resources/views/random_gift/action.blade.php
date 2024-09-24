<div class="d-inline-flex"> 
        {{-- &nbsp; --}}
        <a href="#" type="button" data-click="bpo-delete{{$table->id}}" class="bpo-delete" style="padding: 0.10rem"
            data-action="{{ route('random_gift.delete', $table->id) }}" 
            data-html="true" data-placement="left">
            <span class="{{config('setup.button-opacity-danger')}}">
                <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
            </span>
        </a>
    
</div>
