@extends('layouts.master')
@section('title')
    {{__('global.add_cast')}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h6 class="card-title text-success text-bold">
                        <i class="fas fa-plus"></i>
                        &nbsp;  &nbsp;<span>{{__('sma.add_cast')}}</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-8 p-10">
                            <form action="{{route('cast.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="character">{{__('sma.character')}}</label>
                                    <input type="text" class="form-control" name="character" value="{{old('character')}}" id="character" placeholder="{{trans('sma.please_input')}}" required>
                                    <span class="invalid-feedback">
                                        The field is required.
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="position">{{__('sma.position')}}</label>
                                    <select id="position" class="form-select" name="position" required>
                                        <option value="">{{ __('global.please_select') }}</option>
                                        <option value="Main Role">{{ __('Main Role', [], 'en') }}</option>
                                        <option value="Supporting Role">{{ __('Supporting Role', [], 'en') }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        The field is required.
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="actor_id">{{ trans('sma.artist') }}</label>
                                    <select id="actor_id" class="{{ config('setup.input_select2') }}" name="actor_id" required>
                                        <option value="">{{ __('global.please_select') }}</option>
                                        @foreach($artist as $value)
                                            <option value="{{ $value->id }}" {{old('actor_id') == $value->id ? 'selected':''}} >{{$value->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback">
                                        The field is required.
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="film_id">{{ trans('sma.film') }}</label>
                                    <select id="film_id" class="{{ config('setup.input_select2') }}" name="film_id" required>
                                        <option value="">{{ __('global.please_select') }}</option>
                                        @foreach($film as $value)
                                            <option value="{{ $value->id }}" {{old('film_id') == $value->id ? 'selected':''}} >{{$value->title }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback">
                                        The field is required.
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                                    <select id="status" class="{{ config('setup.input_select2') }} form-select" name="status" required>
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
@endsection
