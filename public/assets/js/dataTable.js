$(document).ready(function() {
    // FOR MEETING TABLES
    let table = $('#meetingTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "<i class='bx bx-chevrons-left'></i> Previous",
                "next": "Next <i class='bx bx-chevrons-right'></i>"
            }
        },
        "dom": '<"top"f>rt<"bottom"ip><"clear">',
    });

    $('.dataTables_filter').hide();

    $('#meetingSearch').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Year filter
    $('select[name="year"]').on('change', function () {
        let selectedYear = $(this).val();

        if (selectedYear) {
            table.column(3).search(selectedYear).draw();
        } else {
            table.column(3).search('').draw();
        }
    });

    // END FOR MEETING TABLES

    // FOR PROPOSAL TABLES
    let proposalTable = $('#proposalTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "<i class='bx bx-chevrons-left'></i> Previous",
                "next": "Next <i class='bx bx-chevrons-right'></i>"
            }
        },
        "dom": '<"top"f>rt<"bottom"ip><"clear">',
    });

    $('.dataTables_filter').hide();

    $('#proposalSearch').on('keyup', function () {
        proposalTable.search(this.value).draw();
    });

    $('select[name="proposalStatus"]').on('change', function () {
        let status = $(this).val();

        if (status) {
            proposalTable.column(6).search(status).draw();
        } else {
            proposalTable.column(6).search('').draw();
        }
    });

    $('select[name="proposalMatter"]').on('change', function () {
        let matter = $(this).val();

        if (matter) {
            proposalTable.column(4).search(matter).draw();
        } else {
            proposalTable.column(4).search('').draw();
        }
    });

    $('select[name="proposalAction"]').on('change', function () {
        let action = $(this).val();

        if (action) {
            proposalTable.column(5).search(action).draw();
        } else {
            proposalTable.column(5).search('').draw();
        }
    });



    // FOR OOB LIST
    let obbTable = $('#oobTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "<i class='bx bx-chevrons-left'></i> Previous",
                "next": "Next <i class='bx bx-chevrons-right'></i>"
            }
        },
        "dom": '<"top"f>rt<"bottom"ip><"clear">',
    });

    $('.dataTables_filter').hide();

    $('#oobSearch').on('keyup', function () {
        obbTable.search(this.value).draw();
    });

    // Year filter
    $('select[name="year"]').on('change', function () {
        let selectedYear = $(this).val();

        if (selectedYear) {
            obbTable.column(3).search(selectedYear).draw();
        } else {
            obbTable.column(3).search('').draw();
        }
    });

    // FOR USER DOWNLOAD LOGS
    $('#userDownloadLogTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "search": "Search: ",
            "lengthMenu": "_MENU_ entries per page",
            "paginate": {
                "previous": "<i class='bx bx-chevrons-left'></i> Previous",
                "next": "Next <i class='bx bx-chevrons-right'></i>"
            }
        },
        "dom": '<"top"lf>rt<"bottom"ip><"clear">',
    });
});
