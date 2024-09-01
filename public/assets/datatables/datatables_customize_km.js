
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
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "ទាំងអស់"]],
            dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            buttons: {
                dom: {
                    button: {
                        className: 'btn bg-success bg-opacity-10 text-success'
                    }
                },
                buttons: [
                    {
                        extend: 'csv',
                        text: 'នាំចេញទម្រង់ CSV <i class="ph-file-csv ms-2"></i>',
                    },
                    {
                        extend: 'excel',
                        text: 'នាំចេញទម្រង់ Excel <i class="ph-file-xls ms-2"></i>',
                    },
                    {
                        extend: 'pdf',
                        text: 'នាំចេញទម្រង់ to PDF <i class="ph-file-pdf ms-2"></i>',
                    },
                    {
                        extend: 'print',
                        text: '<i class="ph-printer me-2"></i> បោះពុម្ព',
                    }
                ]
            },
            language: {
                search: '<span class="me-3">Filter:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
                searchPlaceholder: 'Type to filter...',
                paginate: { 'first': 'First', 'last': 'Last', 'next': document.dir == "rtl" ? '&larr;' : '&rarr;', 'previous': document.dir == "rtl" ? '&rarr;' : '&larr;' },
                lengthMenu: "បង្ហាញទិន្នន័យ <span class='ms-2'>_MENU_</span>",
                info: "កំពុងបង្ហាញ _START_ ដល់ _END_ នៃ _TOTAL_ ទិន្នន័យ",
                infoEmpty: "កំពុងបង្ហាញ 0 ដល់ 0 នៃ 0 entries",
                emptyTable: "មិនមានទិន្នន័យក្នុងប្រព័ន្ធទេ។",
                loadingRecords: "កំពុងដំណើរការ...",
                zeroRecords: "មិនមានទិន្នន័យក្នុងប្រព័ន្ធទេ។",
                processing: "កំពុងដំណើរការ...",
            }
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
