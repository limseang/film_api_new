@extends('layouts.master')
@section('title')
{{__('sma.edit_artical')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
          <h6 class="card-title text-success text-bold">
            <i class="fas fa-edit"></i>
              &nbsp;  &nbsp;<span>{{__('sma.edit_artical')}}</span>
          </h6>
        
        </div>
        <div class="card-body">
          <form action="{{route('artical.update', $artical->id)}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
          <div class="row">
            <div class="col-12 col-lg-6 p-10">
                <div class="mb-3">
                  <label class="form-label" for="title">{{__('sma.title')}}</label>
                  <input type="text" class="form-control" name="title" value="{{$artical->title}}" id="name" placeholder="{{__('sma.please_input')}}" required>
                  <span class="invalid-feedback">
                    The field is required.
                  </span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="origin_id">{{ trans('sma.origin') }}</label>
                  <select id="origin_id" class="{{ config('setup.input_select2') }}" name="origin_id">
                      <option value="">{{ __('global.please_select') }}</option>
                      @foreach($origins as $value)
                      <option value="{{ $value->id }}" {{$artical->origin_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                <div class="mb-3">
                  <label class="form-label" for="tag_id">{{ trans('sma.tag') }}</label>
                  <select id="tag_id" class="{{ config('setup.input_select2') }}" multiple name="tag_id[]" required data-placeholder="{{ __('global.please_select') }}">
                      @foreach($tag as $value)
                      <option value="{{ $value->id }}" {{in_array($value->id, $multiTag )? 'selected':''}} >{{$value->name }}</option>
                      @endforeach
                  </select>
                    <span class="invalid-feedback">
                      The field is required.
                    </span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="type_id">{{ trans('sma.type') }}</label>
                    <select id="type_id" class="{{ config('setup.input_select2') }}" name="type_id" required>
                        <option value="">{{ __('global.please_select') }}</option>
                        @foreach($type as $value)
                        <option value="{{ $value->id }}" {{$artical->type_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                        @endforeach
                    </select>
                      <span class="invalid-feedback">
                        The field is required.
                      </span>
                    </div>
                    <div class="mb-3">
                      <label class="form-label" for="film_id">{{ trans('sma.film') }}</label>
                      <select id="film_id" class="{{ config('setup.input_select2') }}" name="film_id">
                          <option value="">{{ __('global.please_select') }}</option>
                          @foreach($film as $value)
                          <option value="{{ $value->id }}" {{$artical->film_id == $value->id ? 'selected':''}} >{{$value->title }}</option>
                          @endforeach
                      </select>
                        <span class="invalid-feedback">
                          The field is required.
                        </span>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="category_id">{{ trans('sma.category') }}</label>
                        <select id="category_id" class="{{ config('setup.input_select2') }}" name="category_id" required>
                            <option value="">{{ __('global.please_select') }}</option>
                            @foreach($categories as $value)
                            <option value="{{ $value->id }}" {{$artical->category_id == $value->id ? 'selected':''}} >{{$value->name }}</option>
                            @endforeach
                        </select>
                          <span class="invalid-feedback">
                            The field is required.
                          </span>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                      <div class="mb-3">
                          <p class="fw-semibold">{{trans('sma.image')}}</p>
                          <div class="file-input preview-update mb-1" style="padding: 4px; border;border-style: dashed; border-color:#f1f4f9; border-radius:0.375rem; border-width: cal(1px *2); border-color-translucent: rgba(0, 0, 0, 0.125)">
                            <div class=" file-drop-zone clearfix">
                              <div class="file-preview-thumbnails clearfix">
                                <div class="file-preview-frame krajee-default  kv-preview-thumb rotatable" id="thumb-1rad2qred4-148090_Screenshot_202024-06-06_20152921.png" data-fileindex="0" data-fileid="148090_Screenshot_202024-06-06_20152921.png" data-filename="Screenshot 2024-06-06 152921.png" data-template="image" data-zoom="">
                                  <div class="kv-file-content">
                                  <img src="{{$artical->image_url}}" class="file-preview-image kv-preview-data" title="Screenshot 2024-06-06 152921.png" alt="Screenshot 2024-06-06 152921.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
                                </div>
                              </div>
                              </div>
                            </div>
                            <br>
                          </div>
                        <input type="file" class="file-input-caption2" name="image">
                        </div>
                  </div>
                    <div class="col-12 col-lg-12">
                    <div class="mb-3">
                      <label class="form-label" for="description">{{trans('sma.description')}}</label>
                      <textarea rows="3" cols="3" name="description" class="form-control" id="ckeditor_classic_prefilled3" required>
                        {{$artical->description}}
                      </textarea>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                  <button type="submit" class="btn btn-primary mb-3" name="submit"><i class="{{ config('setup.edit_icon') }} me-2"></i> {{__('sma.update')}}</button>
                </div>
          </div>
        </form>
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

    function MyUploadAdapter(loader) {
    this.loader = loader;
    }

    MyUploadAdapter.prototype.upload = function() {
      return this.loader.file
          .then(file => new Promise((resolve, reject) => {
              const formData = new FormData();
              formData.append('upload', file);
              formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

              $.ajax({
                  url: '{{ route('artical.upload_image') }}',
                  type: 'POST',
                  data: formData,
                  processData: false,
                  contentType: false,
                  headers: {
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                  },
                  success: function(response) {
                      if (response.url) {
                          resolve({ default: response.url });
                      } else {
                          reject('Upload failed');
                      }
                  },
                  error: function() {
                      reject('Upload failed');
                  }
              });
          }));
    };

    MyUploadAdapter.prototype.abort = function() {
      // Handle abort if needed
    };
    $(document).ready(function() {
      ClassicEditor.create(document.querySelector('#ckeditor_classic_prefilled3'), 
        {
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'fontFamily',
                    'fontSize',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    'imageUpload',
                    'undo',
                    'redo'
                ]
            },
          ckfinder: {
            uploadUrl: '{{ route('artical.upload_image') }}'
                }
            }).then(editor => {
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new MyUploadAdapter(loader);
                };
        }).catch(error => {
            console.error(error);
        });
    });
  </script>
  @endsection
  @endsection
