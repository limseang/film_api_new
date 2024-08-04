<script>
    $(function() { // Shorthand for $( document ).ready()
        $('body').on('click','.show_log',function(){
           var log_id = $(this).attr('log_id');
           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
           $.ajax({
               type: "POST",
               url: '{{ url("admin/system_log/show-detail")}}',
               data: {
                    log_id:log_id
               },
               dataType: "JSON",
               cache: false,
               processData: true, 
               success: function (response) {
                    $('#modal_show_property').modal('show');
                    $('.log_lists').html(response.trs);
                    $('.activity_log').text(response.event);
               }
           });
        })
    });
    </script>