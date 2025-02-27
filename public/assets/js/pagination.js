$(document).ready(function() {
    // FOR PROPOSAL TABLES
    $('#meetingTable').DataTable({
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


    // FOR PROPOSAL TABLES
    $('#proposalTable').DataTable({
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