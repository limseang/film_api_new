
<div class="modal-header bg-success bg-opacity-10 text-success">
    <h5 class="modal-title">{{trans('sma.cinema_branch')}} <span class="{{config('setup.badge_primary')}}">{{$cineBranch->name ?? ''}}</span></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<br>
<div class="row g-3 align-items-center p-2">
    <div class="col-12">
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('sma.link')}} </span> : &nbsp;<a href="{{$cineBranch->link .$cineBranch->map_link ?? ''}}" class="text-dark" target="_blank">{{$cineBranch->link ?? ''}}</a></p>
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('sma.email')}} </span> :  &nbsp;{{$cineBranch->email ?? ''}}</p>
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('Latitude')}} </span> :  &nbsp;{{$cineBranch->lat ?? ''}}</p>
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('Longitude')}} </span> :  &nbsp;{{$cineBranch->lng ?? ''}}</p>
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('Facebook')}} </span> :  &nbsp;<a href="{{$cineBranch->facebook ?? ''}}" class="text-dark" target="_blank">{{$cineBranch->facebook ?? ''}}</a></p>
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('Instagram')}} </span> :  &nbsp;<a href="{{$cineBranch->instagram ?? ''}}" class="text-dark" target="_blank">{{$cineBranch->instagram ?? ''}}</a></p>
        <p><span class="{{config('setup.badge_primary')}}"> {{trans('Youtube')}} </span> :  &nbsp;<a href="{{$cineBranch->youtube ?? ''}}" class="text-dark" target="_blank">{{$cineBranch->youtube ?? ''}}</a></p>
    </div>
</div>
<br>
<div class="modal-footer">
    <button type="button" class="btn-link {{config('setup.button_opacity_danger')}} mb-3" data-bs-dismiss="modal">Close</button>
</div>

