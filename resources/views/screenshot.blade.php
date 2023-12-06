<!-- resources/views/screenshot/template.blade.php -->

<html>
<head>
    <title>{{ $title }}</title>
{{--    <meta http-equiv="refresh" content="0; URL={{ $facebook }}" />--}}
</head>
<body>
<div id="showShareButton">
    <img src="{{ $image }}" alt="Screenshot">
    <h1>{{$title }}</h1>
    <p>{{ $content }}</p>
    <button id="shareButton">Share on Facebook</button>
</div>

</body>
<script src="{{ asset('/js/jquery.js') }}"></script>
<script>
    window.location.href = "{{ $facebook }}";
</script>
{{--<script>--}}
{{--    window.onload = function() {--}}
{{--        --}}
{{--        //redirect to home page --}}
{{--        window.location.href = "http://localhost:8000";--}}
{{--        var url = window.location.href;--}}
{{--        var id = url.substring(url.lastIndexOf('/') + 1);--}}
{{--        --}}
{{--        --}}
{{--        --}}
{{--        // get current url and id at the end of url--}}
{{--        // var url = window.location.href;--}}
{{--        // var id = url.substring(url.lastIndexOf('/') + 1);--}}
{{--        // $.ajax({--}}
{{--        //     url: + id,--}}
{{--        //     type: 'post',--}}
{{--        //     success: function(data){--}}
{{--        //         console.log(data);--}}
{{--        //     }--}}
{{--        // });--}}
{{--    };--}}
{{--</script>--}}


    // $(document).ready(function(){
    //     var url = window.location.href;
    //     var id = url.substring(url.lastIndexOf('/') + 1);
    //     $.ajax({
    //         url: 'api/share-article/' + id,
    //         type: 'post',
    //         success: function(data){
    //             console.log(data);
    //         }
    //     });
    // });
    // $("#showShareButton").on('click', function(event){
    //     event.preventDefault();
    //     // get current url and id at the end of url
    //     var url = window.location.href;
    //     var id = url.substring(url.lastIndexOf('/') + 1);
    //     $.ajax({
    //         url: 'api/share-article/' + id,
    //         type: 'post',
    //         success: function(data){
    //             console.log(data);
    //         }
    //     });
    // });
</script>
</html>

