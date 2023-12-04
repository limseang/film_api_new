<!-- resources/views/screenshot/template.blade.php -->

<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <div id="showShareButton">
        <img src="{{ $image }}" alt="Screenshot">
        <h1>{{$title }}</h1>
        <p>{{ $content }}</p>
    </div>

</body>
<script>
    // document.getElementById('shareLink').onclick = function(e) {
    //     e.preventDefault();
    //
    //     // Check if the app is installed
    //     // This is just a placeholder. Replace it with the actual code to check the app
    //     var isAppInstalled = false;
    //
    //     if (isAppInstalled) {
    //         // If the app is installed, redirect to the Facebook share page
    //         window.location.href = 'https://www.google.com/';
    //     } else {
    //         // If the app is not installed, redirect to the app download page
    //         window.location.href = 'https://www.facebook.com/aseanglozz/';
    //     }
    // };
    // when click on this blade, send request to method post

    $(document).ready(function(){
        $("body").on('click', function(event){
            event.preventDefault();
            // get current url and id at the end of url
            var url = window.location.href;
            var id = url.substring(url.lastIndexOf('/') + 1);
            $.post('api/share-article/' + id, function(data){
                console.log(data);
            });
        });
    });


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

