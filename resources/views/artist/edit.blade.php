@extends('layouts.master')
@section('title')
{{__('global.edit_artist')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_artist')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('artist.update', $artist->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$artist->name}}" id="name" placeholder="Enter name" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="known_for">{{__('sma.know_for')}}</label>
                  <input type="text" class="form-control" name="known_for" value="{{$artist->known_for}}" id="known_for" placeholder="Enter know for" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="nationality">{{ trans('sma.nationality') }}</label>
                  <select id="nationality" class="{{ config('setup.input_select2') }}" name="nationality" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($countries as $value)
                      <option value="{{ $value->id }}" {{$artist->nationality == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="birth_date">{{ trans('sma.birth_date') }}</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="ph-calendar"></i></span>
                      <input type="text" class="form-control datepicker-autohide" value="{{date('d/m/Y', strtotime($artist->birth_date))}}" name="birth_date" placeholder="{{trans('sma.please_select_date')}}" required/>
                    </div>

                </div>

                <div class="mb-3">
                  <label class="form-label" for="death_date">{{ trans('sma.death_date') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="ph-calendar"></i>
                    </span>
                    <input type="text" class="form-control datepicker-autohide2" value="{{$artist->death_date ? date('d/m/Y', strtotime($artist->death_date)) : ''}}" name="death_date" placeholder="{{trans('sma.please_select_date')}}">
                  </div>

                </div>
                <div class="mb-3">
                  <label class="form-label" for="gender">{{ trans('sma.gender') }}</label>
                  <select id="gender" class="{{ config('setup.input_select2') }} form-select" name="gender" required="">
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="Male" {{$artist->gender == 'Male' ? 'selected':''}}>{{ __('sma.male') }}</option>
                      <option value="Female" {{$artist->gender == 'Female' ? 'selected':''}}>{{ __('sma.female') }}</option>
                  </select>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="publish" class="{{ config('setup.input_select2') }}" name="status" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{$artist->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{$artist->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                  </select>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="biography">{{trans('sma.biography')}}</label>
                  <textarea rows="3" cols="3" name="biography" class="form-control" id="ckeditor_classic_prefilled2">
                    {{$artist->biography}}
                  </textarea>
                </div>
                <div class="mb-3">
                  {{-- Preview imag --}}
                  <p class="fw-semibold">{{trans('sma.avatar_artist')}}</p>
                  <div class="file-input preview-update mb-1" style="padding: 4px; border;border-style: dashed; border-color:#f1f4f9; border-radius:0.375rem; border-width: cal(1px *2); border-color-translucent: rgba(0, 0, 0, 0.125)">
                    <div class=" file-drop-zone clearfix">
                      <div class="file-preview-thumbnails clearfix">
                        <div class="file-preview-frame krajee-default  kv-preview-thumb rotatable" id="thumb-1rad2qred4-148090_Screenshot_202024-06-06_20152921.png" data-fileindex="0" data-fileid="148090_Screenshot_202024-06-06_20152921.png" data-filename="Screenshot 2024-06-06 152921.png" data-template="image" data-zoom="">
                          <div class="kv-file-content">
                          <img src="{{$artist->avatar_url}}" class="file-preview-image kv-preview-data" title="Screenshot 2024-06-06 152921.png" alt="Screenshot 2024-06-06 152921.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
                        </div>
                      </div>
                      </div>
                    </div>
                    <br>
                  </div>
							    <input type="file" class="file-input-caption2" name="image">
                </div>
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-primary mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  @section('scripts')
  <script>
    $(document).ready(function() {
      // when upload file displan none in class preview-update
      $('.file-input-caption2').change(function() {
        $('.preview-update').css('display', 'none');
      });
    });
  </script>
  @endsection
  @endsection
