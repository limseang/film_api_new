@extends('layouts.master')
@section('title')
{{__('sma.add_subtitle')}}
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card card-success card-outline">
        <div class="card-header">
            <div class="d-flex justify-content-between align-content-center">
                <h6 class="card-title text-success text-bold">
                    <i class="ph-file-plus"></i>
                    &nbsp;  &nbsp;<span>{{__('sma.edit_subtitle')}}
                </h6>
                <a class="btn btn-flat-success btn-sm rounded-pill p-2 add-subtitle" href="{{route('episode.add-subtitle', $episode->id)}}">
                    <i class="ph-plus"></i>
                </a>
            </div>
        </div>
        
        <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-6 p-10">
                        <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.film_name')}}:</span>&nbsp; {{$film->title}}</p>
                        <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode_name')}}:</span>&nbsp;{{$episode->title}}</p>
                        <p><span class="{{config('setup.badge_primary')}}">{{trans('sma.episode')}}:</span>&nbsp;{{$episode->episode}}</p>
                    </div>
                </div>
            <div class="row">
            <div class="col-12 col-lg-12 p-10">
                <table class="table table-borderless">
                    <thead class="table-primary bg-opacity-10 table-borderless">
                        <tr style="border: none">
                            <th class="text-center text-primary"><i class="ph-list-numbers"></i> {{trans('sma.n_o')}} </th>
                            <th class="text-center text-primary"><i class="ph-identification-card"></i> {{trans('sma.subtitle')}} </th>
                            <th class="text-center text-primary"> <i class="ph-file"></i>{{trans('sma.file_subtitle')}} </th>
                            <th class="text-center text-primary">{{trans('sma.action')}} </th>
                        </tr>
                    </thead>
                    <tbody style="maigin:4px" class="t-body">
                        @foreach($episodeSubtitles as $key => $value)
                        <tr class=" text-center">
                            <td>
                                <span class="badge bg-teal bg-opacity-20 text-teal rounded-pill p-1" data-value="table-one">
                                    {{$key + 1}}
                                </span>
                            </td>
                            <td style="cursor: pointer;">
                                
                                <span class="{{config('setup.badge_primary')}}">{{$value->language->name}}</span>
                            </td>
                            <td>
                                {{-- <input type="file" data-file="file-input" data-show-preview="false" name="file[]" id="file" required> --}}
                                <span class="btn btn-flat-success btn-sm rounded-pill p-2 coming-soon">
                                    <i class="ph-folder-open"></i>
                                </span>
                            </td>
                            <td>
                                <a href="#" class="text-success show_properties" data-language-id="{{ $value->id }}">
                                    <span class="{{config('setup.button-opacity-success')}}">
                                        <i class="ph-note-pencil" style="font-size:14px"></i> 
                                    </span>
                                </a>
                                &nbsp;
                                <a href="#" type="button" data-click="bpo-delete" class="bpo-delete" style="padding: 0.10rem"
                                    data-action="{{ route('episode.delete_subtitle', $value->id) }}" 
                                    data-html="true" data-placement="left">
                                    <span class="{{config('setup.button-opacity-danger')}}">
                                        <i class="fas fa-trash text-danger text-opacity-10" style="font-size:14px"></i>
                                    </span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
          </div>
          <br>
        </div>
        </div>
    </div>
  </div>
  <div id="modal_form_inline" class="modal fade"data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
  </div>

  @section('scripts')
  <script>
   $(document).ready(function() {
     $('.show_properties').on('click', function(e) {
        e.preventDefault();
        var language_id = $(this).data('language-id');
        var $button = $(this);
        $.ajax({
            url: "{{ route('episode.edit_file_subtitle') }}",
            type: 'GET',
            data: {language_id: language_id},
            beforeSend: function() {
                // show_properties  find data-language-id and add spinner
                $button.prop('disabled', true);
                $button.html("<div class='spinner-border me-2' role='status'><span class='visually-hidden'>Loading...</span></div>");
            },
            success: function(response) {
                $button.prop('disabled', false);
                $button.html(`<span class="{{config('setup.button-opacity-success')}}"><i class="ph-note-pencil" style="font-size:14px"></i> </span>`);
                $('#modal_form_inline').modal('show');
                $('#modal_form_inline .modal-content').html(response).show();
            }
        });
    });
     // show modal
   
    

});


    </script>
    
  @endsection
@endsection