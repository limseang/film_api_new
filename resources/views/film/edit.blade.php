@extends('layouts.master')
@section('title')
{{__('global.edit_director')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_director')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-8 p-10">
            
              <form action="{{route('director.update', $director->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="name">{{__('sma.name')}}</label>
                  <input type="text" class="form-control" name="name" value="{{$director->name}}" id="name" placeholder="Enter name" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="know_for">{{__('sma.know_for')}}</label>
                  <input type="text" class="form-control" name="know_for" value="{{$director->know_for}}" id="know_for" placeholder="Enter know for" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="nationality">{{ trans('sma.nationality') }}</label>
                  <select id="nationality" class="{{ config('setup.input_select2') }}" name="nationality" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($countries as $value)
                      <option value="{{ $value->id }}" {{$director->nationality == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="birth_date">{{ trans('sma.birth_date') }}</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="ph-calendar"></i></span>
                      <input type="text" class="form-control datepicker-autohide" value="{{date('d/m/Y', strtotime($director->birth_date))}}" name="birth_date" placeholder="Please select date" required/>
                    </div>

                </div>

                <div class="mb-3">
                  <label class="form-label" for="death_date">{{ trans('sma.death_date') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="ph-calendar"></i>
                    </span>
                    <input type="text" class="form-control datepicker-autohide2" value="{{$director->death_date ? date('d/m/Y', strtotime($director->death_date)) : ''}}" name="death_date" placeholder="Please select date">
                  </div>

                </div>
                <div class="mb-3">
                  <label class="form-label" for="status">{{ trans('global.publish') }}</label>
                  <select id="publish" class="{{ config('setup.input_select2') }}" name="status" required>
                      <option value="">{{ __('global.please_select') }}</option>
                      <option value="1" {{$director->status == '1' ? 'selected':''}}>{{ __('global.publish_yes') }}</option>
                      <option value="2" {{$director->status == '2' ? 'selected':''}}>{{ __('global.publish_no') }}</option>
                  </select>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="biography">{{trans('sma.biography')}}</label>
                  <textarea rows="3" cols="3" name="biography" class="form-control" id="ckeditor_classic_prefilled2">
                    {{$director->biography}}
                  </textarea>
                </div>
                <div class="mb-3">
                  {{-- Preview imag --}}
                  <p class="fw-semibold">{{trans('sma.avatar_director')}}</p>
                  <div class="file-input preview-update mb-1" style="padding: 4px; border;border-style: dashed; border-color:#f1f4f9; border-radius:0.375rem; border-width: cal(1px *2); border-color-translucent: rgba(0, 0, 0, 0.125)">
                    <div class=" file-drop-zone clearfix">
                      <div class="file-preview-thumbnails clearfix">
                        <div class="file-preview-frame krajee-default  kv-preview-thumb rotatable" id="thumb-1rad2qred4-148090_Screenshot_202024-06-06_20152921.png" data-fileindex="0" data-fileid="148090_Screenshot_202024-06-06_20152921.png" data-filename="Screenshot 2024-06-06 152921.png" data-template="image" data-zoom="">
                          <div class="kv-file-content">
                          <img src="{{$director->avatar_url}}" class="file-preview-image kv-preview-data" title="Screenshot 2024-06-06 152921.png" alt="Screenshot 2024-06-06 152921.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
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
                  <button type="submit" class="btn btn-outline-success mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
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
