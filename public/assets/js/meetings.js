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