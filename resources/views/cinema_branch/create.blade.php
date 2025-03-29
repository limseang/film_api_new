@extends('layouts.master')
@section('title')
{{__('sma.add_cinema_branch')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-plus"></i>
              &nbsp;  &nbsp;<span>{{__('sma.add_cinema_branch')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
            
              <form action="{{route('cinema_branch.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{old('name')}}" id="name" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="address">{{__('sma.address')}}</label>
                  <input type="text" class="form-control" name="address" value="{{old('address')}}" id="address" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="phone">{{__('sma.phone')}}</label>
                  <input type="text" class="form-control" name="phone" value="{{old('phone')}}" id="phone" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="show_type">{{__('sma.show_type')}}</label>
                  <input type="text" class="form-control" name="show_type" value="{{old('show_type')}}" id="show_type" placeholder="{{trans('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="show_type">{{__('sma.email')}}</label>
                  <input type="email" class="form-control" name="email" value="{{old('email')}}" id="email" placeholder="{{trans('sma.please_input')}}">
                </div>
                  <div class="mb-3">
                    <label class="form-label" for="cinema_id">{{ trans('sma.cinema') }}</label>
                    <select id="cinema_id" class="{{ config('setup.input_select2') }}" name="cinema_id" required>
                        <option value="">{{ __('global.please_select') }}</option>
                        @foreach($cinema as $value)
                        <option value="{{ $value->id }}" {{old('cinema_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                        @endforeach
                    </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                    </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{old('status') == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{old('status') == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                <div class="mb-3">
                  <p class="fw-semibold">{{trans('sma.image')}}</p>
							  <input type="file" class="file-input-caption2" name="image">
                </div>
                </div>
                <div class="col-12 col-lg-6 p-10">
                  <div class="mb-3">
                    <label class="form-label" for="link">{{__('sma.link')}}</label>
                    <input type="text" class="form-control" name="link" value="{{old('link')}}" id="link" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="map_link">{{__('Map Link')}}</label>
                    <input type="text" class="form-control" name="map_link" value="{{old('map_link')}}" id="map_link" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="lat">{{__('Lat')}}</label>
                    <input type="text" class="form-control" name="lat" value="{{old('lat')}}" id="lat" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="lng">{{__('Lng')}}</label>
                    <input type="text" class="form-control" name="lng" value="{{old('lng')}}" id="lng" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="ticket_price">{{__('sma.ticket_price')}}</label>
                    <input type="text" class="form-control" name="ticket_price" value="{{old('ticket_price')}}" id="ticket_price" placeholder="{{trans('sma.please_input')}}" required>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="facebook">{{__('Facebook')}}</label>
                    <input type="url" class="form-control" name="facebook" value="{{old('facebook')}}" id="facebook" placeholder="{{trans('sma.please_input')}}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="instagram">{{__('Instagram')}}</label>
                    <input type="url" class="form-control" name="instagram" value="{{old('instagram')}}" id="instagram" placeholder="{{trans('sma.please_input')}}">
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="youtube">{{__('Youtube')}}</label>
                    <input type="url" class="form-control" name="youtube" value="{{old('youtube')}}" id="youtube" placeholder="{{trans('sma.please_input')}}">
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="{{config('setup.button_opacity_primary')}} mb-3" name="submit" value="Save">{{trans('sma.save')}} <i class="{{config('setup.save_icon')}} ms-2"></i></button>
                  <button type="submit" class="{{config('setup.button_opacity_success')}} mb-3 ms-3" name="submit" value="Save_New">{{trans('sma.save_new')}} <i class="{{config('setup.save_new_icon')}} ms-2"></i></button>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
<script>
   $('#ticket_price').on('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
            });
</script>
  @endsection
