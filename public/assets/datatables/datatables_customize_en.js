
// Setup module
// ------------------------------

const DatatableBasic = function() {


    //
    // Setup module components
    //

    // Basic Datatable examples
    const _componentDatatableBasic = function() {
        if (!$().DataTable) {
            console.warn('Warning - datatables.min.js is not loaded.');
            return;
        }

        // Setting datatable defaults
        $.extend( $.fn.dataTable.defaults, {
            autoWidth: false,
            scrollCollapse: true,
            scrollY: "480px",
            // scrollY: "380px",
            scrollX: true,
            searching: false,
            pageLength: 50,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "All"]],
            dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            buttons: {
                dom: {
                    button: {
                        className: 'btn bg-success border-0 ms-1 rounded bg-opacity-10 text-success'
                    }
                },
                buttons: [
                    {
                        extend: 'csv',
                        text: 'Export to CSV <i class="ph-file-csv ms-2"></i>',
                    },
                    {
                        extend: 'excel',
                        text: 'Export to Excel <i class="ph-file-xls ms-2"></i>',
                    },
                    {
                        extend: 'pdf',
                        text: 'Export to PDF <i class="ph-file-pdf ms-2"></i>',
                    },
                    {
                        extend: 'print',
                        text: '<i class="ph-printer me-2"></i> Print',
                    }
                ]
            },
            language: {
                search: '<span class="me-3">Filter:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
                searchPlaceholder: 'Type to filter...',
                paginate: { 'first': 'First', 'last': 'Last', 'next': document.dir == "rtl" ? '&larr;' : '&rarr;', 'previous': document.dir == "rtl" ? '&rarr;' : '&larr;' },
                lengthMenu: "Show <span class='ms-2'>_MENU_</span>",
                info: "Showing _START_ to _END_ of _TOTAL_ Records",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                emptyTable: "No data available in table",
                loadingRecords: "Loading...",
                zeroRecords: "No data available in table",
                processing: "Loading...",
            },
            ajax: {
                beforeSend: function() {
                   var tableBody = $('.datatable-loading-custom').find('tbody');
                   var colspan = tableBody.find("tr:first td").length;
                   tableBody.html("<tr><td colspan='" + colspan + 
                    "class='text-center'><span class='d-flex justify-content-center align-items-center'><i class='ph-spinner-gap text-success' style='font-size:35px;' id='loadingDataTable'></i>" +
                    "Pleas Wait, Loading...</span></td></tr>");
                },
                complete: function() {
                    $('datatable-loading-custom').find('tbody').find("#loadingDataTable").remove();
                }
            },
        });
    };

    //
    // Return objects assigned to module
    //
    return {
        init: function() {
            _componentDatatableBasic();
        }
    }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    DatatableBasic.init();
});
