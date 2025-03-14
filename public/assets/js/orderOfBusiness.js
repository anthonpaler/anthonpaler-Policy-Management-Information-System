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
var filterFrm = $("#filterFrm");
var filterBtn = $("#filterButton");
filterBtn.on('click', function(event){
    filter_oob();
});
$(".oob-tab").on('click', function(event){
    var level = $(this).data('level');
    // alert(level);
    $("#level").val(level);
    filter_oob();
});

function filter_oob(){
event.preventDefault();
//  alert('Clicked');
    var actionUrl = filterFrm.attr('action');
    $.ajax({
        method: "POST",
        url: actionUrl,
        data: filterFrm.serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            filterBtn.html(`<i class='bx bx-loader-alt bx-spin' ></i>
                <span>Filtering...</span> `).prop('disabled', true);
        },
        success: function (response) {
            filterBtn.html(`<i class='bx bx-filter-alt'></i>
                <span>Filter</span> `).prop('disabled', false);
            if(response.type == 'success'){
                $('#oobTableBody').html(response.html);
            //  showAlert("success", "Filtered", "Meeting filtered successfully!");
            }else{
                showAlert("danger", "Can't Filter", "Something went wrong!");
            }
        },            
        error: function (xhr, status, error) {
            filterBtn.html(`<i class='bx bx-filter-alt'></i>
                <span>Filter</span> `).prop('disabled', false);
            console.log(xhr.responseText);
            let response = JSON.parse(xhr.responseText);
            showAlert("danger", response.title, response.message);
        }
    });
}