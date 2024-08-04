@extends('layouts.master')
@section('title')
{{__('sma.permission')}}
@endsection
@section('content')
  <!-- Main content -->
  @csrf
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-success card-outline">
            <div class="card-header">
              <h6 class="card-title text-success text-bold">
                <i class="fas fa-key"></i>
                 &nbsp;  &nbsp;<span>{{__('sma.set_permissions')}} <span class="text-dark">( {{$role->name}} )</span></span>
              </h6>
            </div>
            <form method="POST" action="{{route('role.permission.store')}}" class="needs-validation" novalidate>
              @csrf
                <input type="hidden" name="role_id" value="{{$role->id}}">
            <div class="card-body">
              <div class="row">
                @foreach($permissions as $permission)
                <div class="col-12 col-lg-12">
                  <ul class="list-group mb-4">
                    <li class="list-group-item bg-success bg-opacity-20" aria-current="true">
                      
                      <div class="icheck-success d-inline">
                        <input type="checkbox" style="cursor: pointer;"  class="form-check-input form-check-input-success" name="permission_id[]" id="permission_parent{{$permission->id}}" data-permission={{$permission->id}}  {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} value="{{$permission->id}}">
                        <label class="form-check-label" style="cursor: pointer;"  for="permission_parent{{$permission->id}}"> <span style="font-size: 12px; color:#343a40;"> {{__('permission.'.$permission->name)}}</span>
                        </label>
                    </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row">
                            @if($permission->children->count() > 0)
                                @foreach($permission->children as $child)
                                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                        <div class="p-2 border mt-1 mb-2">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" style="cursor: pointer;"  class="form-check-input form-check-input-success" name="permissions[]" data-parent-permssion={{$permission->id}} data-child-permision={{$child->id}} id="permission{{$child->id}}"  {{ in_array($child->id, $rolePermissions) ? 'checked' : '' }} value="{{$child->id}}">
                                                <label class="form-check-label"  style="cursor: pointer;" for="permission{{$child->id}}"> <span style="font-size: 12px; color:#343a40;">{{__('permission.'.$child->name)}}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </li>
                    </ul>
              </div>
                @endforeach
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-outline-success mb-3"><i class="{{ config('setup.edit_icon') }}"></i>{{__('sma.save')}}</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @section('scripts')
  <script>     
  // check data-permision attribute checkbox and set all child id permission checkboxes and uncheck id permission checkbox
  $(document).ready(function(){
        $('input[type="checkbox"]').click(function(){
            if($(this).is(":checked")){
                var permission_id = $(this).attr('data-permission');
                $('input[data-parent-permssion="'+permission_id+'"]').prop('checked', true);
            }else{
                var permission_id = $(this).attr('data-permission');
                $('input[data-parent-permssion="'+permission_id+'"]').prop('checked', false);
            }
        });
        // when uncheck all child permission or uncheck one checkbox then uncheck parent permission checkbox
        $('input[type="checkbox"]').click(function(){
            var permission_id = $(this).attr('data-parent-permssion');
            if($('input[data-parent-permssion="'+permission_id+'"]').not(':checked')){
                $('#permission_parent'+permission_id).prop('checked', false);
            }
        });
        // when check all child child permission then check parent permission checkbox
        $('input[type="checkbox"]').click(function(){
            // get all child permission id and check all child permission checkbox parent permission checkbox
            var permission_id = $(this).attr('data-parent-permssion');
            if($('input[data-parent-permssion="'+permission_id+'"]').length == $('input[data-parent-permssion="'+permission_id+'"]:checked').length){
                $('#permission_parent'+permission_id).prop('checked', true);
            }
        });
  });
</script>
@endsection
@endsection