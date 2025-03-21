generateOOBBtn = $("#generateOOBBtn");

generateOOBBtn.on('click', function (e) {
    e.preventDefault();

    var oobFrm = $("#oobFrm");
    var actionUrl = oobFrm.attr('action');

    $.ajax({
        method: "POST",
        url: actionUrl,
        data: oobFrm.serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            generateOOBBtn.html(`<i class='bx bx-loader-alt bx-spin' ></i>
                <span>Generating...</span> `).prop('disabled', true);
        },
        success: function (response) {
            
            generateOOBBtn.html(`<i class='bx bx-send'></i>
                <span>Generate OOB</span>`).prop('disabled', false);

            if(response.type == 'success'){
                generateOOBBtn.html(`<i class='bx bx-send'></i>
                    <span>Generate OOB</span>`).prop('disabled', true);
            }

            showAlert(response.type, response.title, response.message);
        },            
        error: function (xhr, status, error) {
            generateOOBBtn.html(`<i class='bx bx-send'></i>
                <span>Generate OOB</span>`).prop('disabled', false);
            console.log(xhr.responseText);
            let response = JSON.parse(xhr.responseText);
            showAlert("warning", response.title, response.message);
        }
    });
});


// SAVE CHANGES
saveOOBBtn = $("#saveOOBBtn");

saveOOBBtn.on('click', function (e) {
    e.preventDefault();

    var oobFrm = $("#oobFrm");
    var actionUrl = oobFrm.attr('action');

    $.ajax({
        method: "POST",
        url: actionUrl,
        data: oobFrm.serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            saveOOBBtn.html(`<i class='bx bx-loader-alt bx-spin' ></i>
                <span>Saving    ...</span> `).prop('disabled', true);
        },
        success: function (response) {
            
            saveOOBBtn.html(`<i class='bx bx-save' ></i>
                <span>Save Changes</span>`).prop('disabled', false);
            showAlert(response.type, response.title, response.message);
        },            
        error: function (xhr, status, error) {
            saveOOBBtn.html(`<i class='bx bx-save' ></i>
                <span>Save Changes</span>`).prop('disabled', false);
            console.log(xhr.responseText);
            let response = JSON.parse(xhr.responseText);
            showAlert("warning", response.title, response.message);
        }
    });
});

// DISSEMINATE

document.addEventListener("DOMContentLoaded", function () {
    if (typeof postedToAgendaProposalIDS !== "undefined" && postedToAgendaProposalIDS.length === 0) {
        showAlert("warning", "No Posted to Agenda Proposals", "There are No Posted to Agenda Proposals Available.");
    } else {
        console.log("Proposal IDs:", postedToAgendaProposalIDS);
    }

    let endorsedProposals = postedToAgendaProposalIDS || [];
    let disseminateOOBBtn = $("#disseminateOOBBtn");

    disseminateOOBBtn.on("click", function (e) {
        e.preventDefault();

        if (endorsedProposals.length === 0) {
            showAlert("warning", "No Posted to Agenda Proposals", "There are No Posted to Agenda Proposals Available.");
            return; 
        }

        var oob_id = $(this).data("id");
        actionUrl =  $(this).data("action");
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: { postedToAgendaProposalIDS: postedToAgendaProposalIDS },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                disseminateOOBBtn
                    .html(`<i class='bx bx-loader-alt bx-spin'></i><span>Disseminating...</span>`)
                    .prop("disabled", true);
            },
            success: function (response) {
                disseminateOOBBtn
                    .html(`<i class='bx bx-send'></i><span>Disseminate OOB</span>`)
                    .prop("disabled", false);

                if (response.type == "success") {
                    disseminateOOBBtn
                        .html(`<i class='bx bx-send'></i><span>OOB Disseminated</span>`)
                        .prop("disabled", true);
                        $("#exportOOB").removeClass('d-none');
                    $("#saveOOBBtn")
                        .html(`<i class='bx bx-send'></i><span>Save Changes</span>`)
                        .prop("disabled", true);

                }

                showAlert(response.type, response.title, response.message);
            },
            error: function (xhr, status, error) {
                disseminateOOBBtn
                    .html(`<i class='bx bx-send'></i><span>Disseminate OOB</span>`)
                    .prop("disabled", false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("warning", response.title, response.message);
            },
        });
    });
});


// OOB FILTERING
$(".oob-tab").on('click', function(event){
    $(".oob-tab").removeClass("active"); 
    $(this).addClass("active"); 

    var level = $(this).data('level');
    filter_oob(event, level);
});

function filter_oob(event, level){
    if (event) event.preventDefault();    
    var actionUrl = $('#filterRow').data('action');
    alert(level);
    $.ajax({
        method: "POST",
        url: actionUrl,
        data: { level: level },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $("#customFilterLoader").removeClass('d-none');
        },
        success: function (response) {
            $("#customFilterLoader").addClass('d-none');
        
            if(response.type == 'success'){
                let table = $('#oobTable').DataTable();
                table.clear().destroy();
            
                $('#oobTableBody').html(response.html); // Ensure backend returns proper <tr> data
                
                // Reinitialize DataTable
                let newTable = $('#oobTable').DataTable({
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
        
                // Rebind search input event
                $('#oobSearch').off('keyup').on('keyup', function () {
                    newTable.search(this.value).draw();
                });
        
                // Rebind year filter event
                $('select[name="year"]').off('change').on('change', function () {
                    let selectedYear = $(this).val(); 
        
                    if (selectedYear) {
                        newTable.column(4).search(selectedYear).draw();
                    } else {
                        newTable.column(4).search('').draw();
                    }
                });
        
            } else {
                showAlert("danger", "Can't Filter", "Something went wrong!");
            }
        },       
        error: function (xhr, status, error) {
            $("#customFilterLoader").addClass('d-none');
            console.log(xhr.responseText);
            let response = JSON.parse(xhr.responseText);
            showAlert("danger", response.title, response.message);
        }
    });
}