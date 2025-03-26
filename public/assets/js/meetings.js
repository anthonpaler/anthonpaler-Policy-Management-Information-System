$(document).ready(function() {
    $(document).on('change', '#addMeetInfo', function(){
        if(this.checked){
            // alert("Checked");
            $("#meetingInfo").removeClass('d-none');
            $("#modality").attr('required', 'required');
            $("#meeting_date_time").attr('required', 'required');
        }else{
            $("#meetingInfo").addClass('d-none');
            $("#modality").removeAttr('required');
            $("#meeting_date_time").removeAttr('required');
        }
    });
    $(document).on('change', '#modality', function() {
        switch ($(this).val()) {
            case '1':
                $('#venueField').removeClass('d-none');
                $("#venue").attr('required', 'required');

                $('#onlineModeInfo').addClass('d-none');
                $("#mode_if_online").removeAttr('required', 'required');

                break;
            case '2':
                $('#onlineModeInfo').removeClass('d-none');
                $("#mode_if_online").attr('required', 'required');

                $('#venueField').addClass('d-none');
                $("#venue").removeAttr('required', 'required');
                break;
            case '3':
                $('#venueField').removeClass('d-none');
                $("#venue").attr('required', 'required');

                $('#onlineModeInfo').removeClass('d-none');
                $("#mode_if_online").attr('required', 'required');
                break;
            default:
                $('#venueField').addClass('d-none');
                $("#venue").removeAttr('required', 'required');

                $('#onlineModeInfo').addClass('d-none');
                $("#mode_if_online").removeAttr('required', 'required');
                break;
        }
    });

    // CREATE MEETING
    $("#createMeetingBtn").on('click', function(event){
        event.preventDefault();
        // alert("Clicked");
        var meetingFrm = $("#meetingForm");
        var actionUrl = meetingFrm.attr('action');

        $.ajax({
            method: "POST",
            url: actionUrl,
            data: meetingFrm.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#createMeetingBtn").html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Creating Meeting...</span> `).prop('disabled', true);
            },
            success: function (response) {
                $("#createMeetingBtn").html(`<i class='bx bx-plus'></i>
                    <span>Create Meeting</span> `).prop('disabled', false);
                if(response.type === 'success'){
                    meetingFrm[0].reset();
                    showAlert(response.type, response.title, response.message);  
                    window.location.href = response.redirect;
                }else{
                    showAlert(response.type, response.title, response.message);  
                }      
            },            
            error: function (xhr, status, error) {
                $("#createMeetingBtn").html(`<i class='bx bx-plus'></i>
                    <span>Create Meeting</span> `).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("danger", response.title, response.message);
            }
        });
    });

    // EDIT MEETING 
    $("#editMeetingBtn").on('click', function(event){
        event.preventDefault();
        // alert("Clicked");
        var meetingFrm = $("#meetingForm");
        var actionUrl = meetingFrm.attr('action');
    
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: meetingFrm.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#editMeetingBtn").html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Saving Changes...</span> `).prop('disabled', true);
            },
            success: function (response) {
                $("#editMeetingBtn").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span> `).prop('disabled', false);
                if(response.type === 'success'){
                    showAlert(response.type, response.title, response.message);  
                }else{
                    showAlert(response.type, response.title, response.message);  
                }      
            },            
            error: function (xhr, status, error) {
                $("#editMeetingBtn").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span> `).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("danger", response.title, response.message);
            }
        });
    });


    // FILTER MEETINGS
    $(".meeting-tab").on('click', function(event){
        $(".meeting-tab").removeClass("active"); 
        $(this).addClass("active"); 
    
        var level = $(this).data('level');
        filter_meeting(event, level);
    });
    
    function filter_meeting(event, level){
        if (event) event.preventDefault();    
        var actionUrl = $('#filterRow').data('action');
        
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: {level:level},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#customFilterLoader").removeClass('d-none');
            },
            success: function (response) {
                $("#customFilterLoader").addClass('d-none');
            
                if(response.type == 'success'){
                    let table = $('#meetingTable').DataTable();
                    table.clear().destroy();
                
                    $('#meetingsTableBody').html(response.html); // Ensure backend returns proper <tr> data
                    
                    // Reinitialize DataTable
                    let newTable = $('#meetingTable').DataTable({
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
                    $('#meetingSearch').off('keyup').on('keyup', function () {
                        newTable.search(this.value).draw();
                    });
            
                    // Rebind year filter event
                    $('select[name="year"]').off('change').on('change', function () {
                        let selectedYear = $(this).val(); 
            
                        if (selectedYear) {
                            newTable.column(3).search(selectedYear).draw();
                        } else {
                            newTable.column(3).search('').draw();
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
});

$(document).ready(function () {
    let today = new Date();
    today.setDate(today.getDate());
    let minStartDate = today.toISOString().split("T")[0];

    let $submissionStart = $("#submission_start");
    let $submissionEnd = $("#submission_end");
    let $meetingDate = $("#meeting_date_time");

    // Set minimum date for Submission Start
    $submissionStart.attr("min", minStartDate);

    $submissionStart.on("change", function () {
        let startDate = new Date($(this).val());
        startDate.setDate(startDate.getDate() + 1); // Add one day

        let minEndDate = startDate.toISOString().split("T")[0];
        $submissionEnd.attr("min", minEndDate);

        // Reset only if invalid
        if (new Date($submissionEnd.val()) < startDate) {
            $submissionEnd.val("");
        }
    });

    $submissionEnd.on("change", function () {
        let endDate = new Date($(this).val());

        if (endDate) {
            endDate.setDate(endDate.getDate() + 2); // Add one day
            endDate.setHours(0, 0, 0, 0);

            let formattedMeetingMinDate = endDate.toISOString().slice(0, 16);
            $meetingDate.attr("min", formattedMeetingMinDate);

            // Reset only if invalid
            if (new Date($meetingDate.val()) < endDate) {
                $meetingDate.val("");
            }
        }
    });

    $meetingDate.on("change", function () {
        let selectedDateTime = new Date($(this).val());
        let minMeetingDate = new Date($meetingDate.attr("min"));

        if (selectedDateTime < minMeetingDate) {
            showAlert('warning', 'Invalid Date', "Meeting date must be at least one day after the submission end date");
            $(this).val(""); // Reset only if invalid
        }
    });
});
