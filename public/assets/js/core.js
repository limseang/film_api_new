$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();


});

function showImage(event) {
    $('#view-img').modal('show');
    var img_link = $(event).attr("src");
    img = jQuery('<img class="dynamic" style="width:100%;">');
    img.attr('src', img_link);
    jQuery('.show-img').html(img);
}
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

$('#password, #confirm_password').on('keyup', function() {

    $('.confirm-message').removeClass('success-message').removeClass('error-message');

    let password = $('#password').val();
    let confirm_password = $('#confirm_password').val();

    if (password === "") {
        $('.confirm-message').text("").addClass('error-message');
        $('.update-btn').prop("disabled", false);

    } else if (password.length < 8) {
        $('.confirm-message').text("At least password must be at 8 characters long").addClass('error-message');
        $('.update-btn').prop("disabled", true);
    } else if (confirm_password === "") {
        $('.confirm-message').text("Confirm Password Field cannot be empty").addClass('error-message');
        $('.update-btn').prop("disabled", true);
    } else if (confirm_password === password) {
        $('.confirm-message').text('Password Match!').addClass('success-message');
        $('.update-btn').prop("disabled", false);
    } else {
        $('.confirm-message').text("Password Doesn't Match!").addClass('error-message');
        $('.update-btn').prop("disabled", true);
    }

});



// $(function() {
//     $("#example1").DataTable({
//         "responsive": true,
//         "lengthChange": false,
//         "autoWidth": false,
//         "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
//     }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
//     let pageLenghs=   $('#setting').val();
//         // console.log(pageLenghs);
//     $('#myTable').DataTable({
//         "paging": true,
//         "lengthChange": true,
//         "searching": true,
//         "ordering": true,
//         "info": true,
//         "autoWidth": false,
//         "responsive": false,
//         "lengthMenu": [
//             [10, 25, 50, 100, -1],
//             [10, 25, 50, 100, "All"]
//         ],
//         "pageLength": 10,
//         // "pagingType": "full_numbers"


//     });
// });

$('body').on('click', '.bpo', function(e) {
    e.preventDefault();
    $(this).popover({ html: true, trigger: 'manual' }).popover('toggle');
    return false;
});
$('body').on('click', '.bpo-close', function(e) {
    $('.bpo').popover('hide');
    return false;
});

$('#myModal')
    .on('show.bs.modal', function() {
        $('.loading').fadeIn('fast');
        $('.modal-show').show('1000');
        setTimeout(function() {
            $('.loading').fadeOut('fast');
        }, 100);
    })
    .on('hide.bs.modal', function() {
        $('.loading').fadeIn('fast');
        $('.modal-show').hide('1000');
        setTimeout(function() {
            $('.loading').fadeOut('fast');
        }, 100);
    });

$(function() {
    //Initialize Select2 Elements
    $('.form-control-select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
})

// Delete Multiple Data

$('#checkAll').on('click', function() {
    if ($("input:checkbox").prop("checked")) {
        $("input:checkbox[name='val[]']").prop('checked', true);
    } else {
        $("input:checkbox[name='val[]']").prop('checked', false);
    }
    $("input:checkbox[name='val[]']").on('change', function() {
        var total_check_row = $("input:checkbox[name='val[]']").length;
        var total_check_rows = $("input:checkbox[name='val[]']:checked").length;
        if (total_check_row == total_check_rows) {
            $('#checkAll').prop("checked", true);
        } else {
            $('#checkAll').prop("checked", false);
        }
    })
})

$(document).on('click', '#delete', function(e) {
    e.preventDefault();
    $('.bpo').popover('hide');
    $('#form_action').val($(this).attr('data-action'));
    $('#action-form').submit();

});


$("#custom-file-img").on("change", function(e) {
    var img_link = document.getElementById('prev');
    img_link.src = URL.createObjectURL(e.target.files[0]);
    var fileName = $(this).val().split("\\").pop();
    var size=this.files[0].size;
    var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
      i=0;while(size>900){size/=1024;i++;}
      var exactSize = (Math.round(size*100)/100)+' '+fSExt[i];
     $('.Size').show();
    $('.Size').html('Size: '+exactSize);
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

$("#custom-file-preview").on("change", function(e) {
    var img_link = document.getElementById('preview');
    img_link.src = URL.createObjectURL(e.target.files[0]);
    var fileName = $(this).val().split("\\").pop();
    var size=this.files[0].size;
    var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
      i=0;while(size>900){size/=1024;i++;}
      var exactSize = (Math.round(size*100)/100)+' '+fSExt[i];
     $('.Size').show();
    $('.Size').html('Size: '+exactSize);
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
$(".custom-file-csv").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
      var size=this.files[0].size;
    var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
      i=0;while(size>900){size/=1024;i++;}
      var exactSize = (Math.round(size*100)/100)+' '+fSExt[i];
     $('.Size-file').show();
    $('.Size-file').html('Size: '+exactSize);
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
$(".custom-file-video").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    var extension=$(this).val().replace(/^.*\./, '');
    var size=this.files[0].size;
    var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
      i=0;while(size>900){size/=1024;i++;}
      var exactSize = (Math.round(size*100)/100)+' '+fSExt[i];
     $('.Size').show();
    $('.Size').html('Size: '+exactSize);
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    
});
// Random code
$(document).ready(function() {
    $('#randomCode').click(function() {
        let x = Math.round(Math.random() * 100000000);
        $('#code').val(x);
    })
});

// checkbox
$(document).ready(function() {
    $('.checkbox').on('change', function() {
        let check = $(this).val();
        if (check == '0') {
            $(this).val(1);
        } else {
            $(this).val(0);
        }

    })
});




// Delete Data without Refresh Pages

//   $(document).on('click', '.po-delete', function(e) {
//     e.preventDefault();
//     $('.bpo').popover('hide');
//     var link = $(this).attr('href');
//     var id= $('#tbody').find('tr').attr('id');
//     $.ajax({
//         type: 'get',
//         url: link,
//         dataType: 'json',

//         success: function(data) {
//               if(data.success) {
//                 $("#"+id).closest('tr').remove();
//                $('#myTable').fadeOut('fast');
//                $('#myTable').fadeIn('fast');
//                $('#alert').append('<div class="alert alert-success alert-dismissible fade show" role="alert"><div>'
//                 +data.success+'</div><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

//               }else{
//                 $('#myTable').fadeOut('fast');
//                  $('#myTable').fadeIn('fast');
//                 $('#alert').append('<div class="alert alert-danger alert-dismissible fade show" role="alert"><div>'
//                  +data.error+'</div><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
//               }

//          },
//          error: function(data) {
//           $('#alert').append('<div class="alert alert-danger alert-dismissible fade show" role="alert"><div>'
//              +data.error+'</div><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
//         },
//     });
//     return false;
// });

document.addEventListener('DOMContentLoaded', function() {
    var requiredInputs = document.querySelectorAll('input[required]');
    requiredInputs.forEach(function(input) {
        var label = document.querySelector('label[for="' + input.name + '"]');
        if (label) {
            label.classList.add('required');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var requiredInputs = document.querySelectorAll('select[required]');
    requiredInputs.forEach(function(input) {
        var label = document.querySelector('label[for="' + input.name + '"]');
        if (label) {
            label.classList.add('required');
        }
    });
});


$(document).ready(function() {
    $('body').on('click', '.bpo-delete', function(e) {
    e.preventDefault(); // Prevent the default action of the link

    // Retrieve the action URL from the data-action attribute of the clicked element
    var actionUrl = $(this).attr('data-action');

    // Display the SweetAlert2 confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        // If the user confirms the action
        if (result.isConfirmed) {
            // Create a form dynamically
            var form = $('<form>', {
                'method': 'GET',
                'action': actionUrl
            });

            // Append CSRF token input
            var token = $('meta[name="csrf-token"]').attr('content');
            var hiddenInput = $('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': token
            });

            form.append(hiddenInput);

            // Append method input for DELETE
            var hiddenMethod = $('<input>', {
                'type': 'hidden',
                'name': '_method',
                'value': 'DELETE'
            });

            form.append(hiddenMethod);

            // Append the form to the body and submit it
            form.appendTo('body').submit();
        }
    });
});

});


$(document).ready(function() {
    $('body').on('click', '.bpo-status', function(e) {
    e.preventDefault(); // Prevent the default action of the link

    // Retrieve the action URL from the data-action attribute of the clicked element
    var actionUrl = $(this).attr('data-action');

    // Display the SweetAlert2 confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Change it!',
        cancelButtonText: 'No, cancel!',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        // If the user confirms the action
        if (result.isConfirmed) {
            // Create a form dynamically
            var form = $('<form>', {
                'method': 'GET',
                'action': actionUrl
            });

            // Append CSRF token input
            var token = $('meta[name="csrf-token"]').attr('content');
            var hiddenInput = $('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': token
            });

            form.append(hiddenInput);

            // Append method input for DELETE
            var hiddenMethod = $('<input>', {
                'type': 'hidden',
                'name': '_method',
                'value': 'DELETE'
            });

            form.append(hiddenMethod);

            // Append the form to the body and submit it
            form.appendTo('body').submit();
        }
    });
});

});





    

