$(document).ready(function() {
    // CHANGE FILE TAB
    $(".file-tab").click(function() {
        $(".file-tab").removeClass("active");
        $(this).addClass("active");

        let level = $(this).data("status");

        if (level == 0) {
            $(".latest-version-files").removeClass("d-none");
            $(".old-version-files").addClass("d-none");
        } else {
            $(".latest-version-files").addClass("d-none");
            $(".old-version-files").removeClass("d-none");
        }
    });

    let selectedProponents = window.selectedProponents || [];
    let proponentInput = $("#proponents");

    // Automatically add the primary proponent (logged-in user)
    let primaryProponent = {
        id: $("#primaryProponent").data("id"),
        name: $("#primaryProponent").data("name"),
        email: $("#primaryProponent").data("email"),
        image: $("#primaryProponent").data("image"),
    };

    // Check if the primary proponent is valid (id and name must not be empty/null)
    if (primaryProponent.id && primaryProponent.name) {
        // Ensure the primary proponent is added only once
        if (!selectedProponents.some(p => p.id === primaryProponent.id)) {
            selectedProponents.push(primaryProponent);
        }
    }

    console.log(selectedProponents);

    let proponentIds = selectedProponents.map(selectedProponents => selectedProponents.id);
    proponentInput.val(proponentIds.join(','));

    $("#addProponent").on("keyup", function () {
        let query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                url: "/search-users",
                type: "GET",
                data: { query: query },
                success: function (data) {
                    let searchResults = $(".search-drop-card ul");
                    searchResults.empty();

                    if (data.length > 0) {
                        data.forEach(user => {
                            if (!selectedProponents.some(u => u.id === user.employee_id)) {
                                let userItem = `
                                    <li data-id="${user.employee_id}" data-name="${user.name}" data-email="${user.email}" data-image="${user.image}" class="select-user">
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="avatar-wrapper">
                                                <div class="avatar avatar-sm me-4">
                                                    <img src="${user.image ? user.image : '/assets/img/avatars/default-avatar.jpg'}" alt="Avatar" class="rounded-circle">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);" class="text-heading text-truncate">
                                                    <span class="fw-medium">${user.name}</span>
                                                </a>
                                                <small>${user.email}</small>
                                            </div>
                                        </div>
                                    </li>
                                `;
                                searchResults.append(userItem);
                            }
                        });
                    } else {
                        searchResults.append("<li>No users found</li>");
                    }

                    $(".search-drop-card").show();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    let response = JSON.parse(xhr.responseText);
                    showAlert("warning", response.title, response.message);
                }
            });
        } else {
            $(".search-drop-card").hide();
        }
    });

    // Handle user selection
    $(document).on("click", ".select-user", function () {
        let userId = $(this).closest("li").data("id");
        let userName = $(this).closest("li").data("name");
        let userImage = $(this).closest("li").data("image");
        let userEmail = $(this).closest("li").data("email");

        if (!selectedProponents.some(u => u.id === userId)) {
            selectedProponents.push({ id: userId, name: userName, email: userEmail, image: userImage });

            $("#proponentListCon").append(`
            <li data-id="${userId}">
                     <div class="d-flex justify-content-between align-items-center ms-2 me-2 flex-wrap gap-2">
                        <div class="d-flex justify-content-start align-items-center ">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <img src="${userImage ? userImage : '/assets/img/avatars/default-avatar.jpg'}" alt="Avatar" class="rounded-circle">
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="javascript:void(0);" class="text-heading text-truncate m-0">
                                    <span class="fw-medium">${userName}</span>
                                </a>
                                <small>${userEmail}</small>
                            </div>
                        </div>
                        <div class="">
                            <small class="badge bg-label-danger d-flex align-items-center gap-2 remove" data-id="${userId}"><i class='bx bx-trash'></i>Remove</small>
                        </div>
                    </div>
                </li>
            `);
        }

        $("#addProponent").val("");
        $(".search-drop-card").hide();
        console.log(selectedProponents);
        proponentIds = selectedProponents.map(selectedProponents => selectedProponents.id);
        proponentInput.val(proponentIds.join(','));
    });

    // Remove selected user
    $(document).on("click", ".remove", function () {
        let userId = $(this).data("id");
        selectedProponents = selectedProponents.filter(user => user.id !== userId);

        $(`#proponentListCon li[data-id="${userId}"]`).remove();

        if (selectedProponents.length === 0) {
            $("#proponentListCon").empty();
        }
        console.log(selectedProponents);
        proponentIds = selectedProponents.map(selectedProponents => selectedProponents.id);
        proponentInput.val(proponentIds.join(','));
    });

    // Hide dropdown when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".c-field-p").length) {
            $(".search-drop-card").hide();
        }
    });


    // ADD/SUBMIT PROPOSAL RELATED CODE

    // RESUMIT BUTTON TRIGGER
    $(".resubmit-proposal").on('click', function (e) {
        e.preventDefault();

        var file_id = $(this).data('id');
        $("#file-upload").data("id", file_id).click(); // Store file ID and trigger file input
        $(this).closest(".file-status").html("Revised");
    });

    // CUSTOM FILE UPLOAD FOR RESUBMITINNG PROPOSAL FILE
    $("#file-upload").on("change", function (e) {
        var file = e.target.files[0]; // Get selected file
        var file_id = $(this).data("id");

        if (file) {
            reuploadedFiles.push({ file_id, file });
        }

        console.log(reuploadedFiles);

        if (this.files.length > 0) {
            var proposalRow = $("button.resubmit-proposal[data-id='" + file_id + "']").closest("tr");

            // Update the status to "Revised"
            proposalRow.find(".badge.bg-label-primary").text("Revised");

            // Increment version number
            var versionElement = proposalRow.find(".version");
            var currentVersion = parseInt(versionElement.text().replace("Version ", "").trim());
            var newVersion = currentVersion + 1;

            // Update version badge
            versionElement.text("Version " + newVersion);

            // Update file name
            proposalRow.find(".text-wrap").text(file.name);
        }
    });


    // TEMPORARILY DELETE PROPOSAL FILE
    $(".delete-proposal-file").on("click", function (e) {
        e.preventDefault();
        var file_id = $(this).data("id");
        var button = $(this);

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                deletedFiles.push(file_id);
                button.closest("tr").remove();
                console.log(deletedFiles);
            }
        });
    });

    // UPDATE PROPOSAL
    $("#updateProposal").on('click', function (e) {
        e.preventDefault();

        var proposalFrm = $("#editProposalFrm")[0]; // Get the raw form element
        var formData = new FormData(proposalFrm); // Create FormData from the form

        var actionUrl = $("#editProposalFrm").attr('action');


        uploadedProposalFiles.forEach((file, index) => {
            formData.append(`proposal_files[${index}]`, file);
        });

        // Append deleted files
        deletedFiles.forEach((fileId) => {
            formData.append("deleted_files[]", fileId);
            console.log("Deleted File ID: " + fileId);
        });

        // Append reuploaded files
        reuploadedFiles.forEach(({ file_id, file }) => {
            formData.append(`reuploaded_files[${file_id}]`, file);
            console.log("Reuploaded File ID: " + file_id);
            console.log("Reuploaded File: " + file);

        });

        // console.log('Form Data:', formData);

        // Send AJAX Request
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#updateProposal").html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Saving Changes...</span>`).prop('disabled', true);
            },
            success: function (response) {
                $("#updateProposal").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span>`).prop('disabled', false);
                // console.log(response);
                showAlert(response.type, response.title, response.message);

                if(response.type ==  'success'){
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                $("#updateProposal").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span>`).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("warning", response.title, response.message);
            }
        });
    });

    // ADD PROPOSAL
    $("#addProposalBtn").on('click', function (e) {
      e.preventDefault();

      var rawEmail = $("#proponent_email").val();
      var parsedEmail = JSON.parse(rawEmail)[0].value;

      console.log(parsedEmail);
      $("#proponent_email").val(parsedEmail);


      var proposalFrm = $("#addProposalFrm")[0];
      var formData = new FormData(proposalFrm);

      var actionUrl = $("#addProposalFrm").attr('action');

      uploadedProposalFiles.forEach((file, index) => {
          formData.append(`proposal_files[${index}]`, file);
      });
      console.log('Form Data:', formData);

      $.ajax({
          method: "POST",
          url: actionUrl,
          data: formData,
          processData: false,
          contentType: false,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function () {
              $("#addProposalBtn").html(`<i class='bx bx-loader-alt bx-spin' ></i>
                  <span>Adding Proposal...</span>`).prop('disabled', true);
          },
          success: function (response) {
              $("#addProposalBtn").html(`Add Proposal`).prop('disabled', false);
              // console.log(response);
              showAlert(response.type, response.title, response.message);

              if(response.type ==  'success'){
                  location.reload();
              }
          },
          error: function (xhr, status, error) {
            $("#addProposalBtn").html(`Add Proposal`).prop('disabled', false);
              console.log(xhr.responseText);
              let response = JSON.parse(xhr.responseText);
              showAlert("warning", response.title, response.message);
          }
      });
    });

    // SUBMIT PROPOSAL FOR PROPONENT
    let submitProposalBtn = $("#submitProposalBtn");

    submitProposalBtn.on('click', function (e) {
        e.preventDefault();

        let proposalFrm = $("#proposalFrm");
        var actionUrl = proposalFrm.attr('action');

        // Create FormData object
        var formData = new FormData(proposalFrm[0]);

        // Append uploadedProposalFiles to FormData
        uploadedProposalFiles.forEach((file, index) => {
            formData.append(`proposal_files[${index}]`, file);
        });

        $.ajax({
            method: "POST",
            url: actionUrl,
            data: formData,
            processData: false, // Prevent jQuery from processing data
            contentType: false, // Set proper content type for file uploads
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                submitProposalBtn.html(`<i class='bx bx-loader-alt bx-spin'></i>
                    <span>Submitting...</span>`).prop('disabled', true);
            },
            success: function (response) {
                submitProposalBtn.html(`<i class='bx bx-send'></i>
                    <span>Submit Proposal</span>`).prop('disabled', false);
                showAlert(response.type, response.title, response.message);

                if(response.type == 'success'){
                    uploadedFilesLabel.style.display = "none";
                    proposalFrm[0].reset();

                    // Reset uploadedProposalFiles
                    uploadedProposalFiles = [];
                    fileList.innerHTML = ""; // Clear file list

                    // Reset selected proponents, keeping only the primary proponent
                    selectedProponents = [primaryProponent];
                    let proponentIds = selectedProponents.map(proponent => proponent.id);
                    proponentInput.val(proponentIds.join(','));

                    // Remove additional proponents from UI
                    $("#proponentListCon").html(`
                        <li data-id="${primaryProponent.id}">
                            <div class="d-flex justify-content-between align-items-center ms-2 me-2 flex-wrap gap-2">
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="${primaryProponent.image}" alt="Avatar" class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-heading text-truncate m-0">
                                            <span class="fw-medium">${primaryProponent.name}</span>
                                        </a>
                                        <small>${primaryProponent.email}</small>
                                    </div>
                                </div>
                                <div class="">
                                    <small class="badge bg-label-secondary d-flex align-items-center gap-2">
                                        <i class='bx bx-user-check'></i>Submitter
                                    </small>
                                </div>
                            </div>
                        </li>
                    `);
                }
            },
            error: function (xhr, status, error) {
                submitProposalBtn.html(`<i class='bx bx-send'></i>
                    <span>Submit Proposal</span>`).prop('disabled', false);

                console.log(xhr.responseText); // Log the raw response

                try {
                    let response = JSON.parse(xhr.responseText);
                    showAlert("warning", response.title, response.message);
                } catch (e) {
                    showAlert("danger", "Error", "An unexpected error occurred. Please try again.");
                }
            }

        });
    });

    // SHOW PROPOSAL FILE
   $(document).on('click', '.view-files', function (e) {
        e.preventDefault();
        var files = $(this).data("files");
        var title = $(this).data("title");

        console.log(files);

        if (!files || files.length === 0) {
            $("#modalFiles").html('<p class="text-danger">No files available.</p>');
        } else {
            let fileListHtml = `
                <div class="">
                    <div class="d-flex flex-column">
                        <span class="form-label">Title:</span>
                        <h6 id="modal-title">${title || 'No Title Available'}</h6>
                    </div>
                    <div class="">
                        <span class="form-label">Files:</span>
                        <div class="d-flex flex-column gap-2 mt-2">
            `;

            $.each(files, function (index, fileObj) {
                if(fileObj.is_active == true){
                    fileListHtml += `
                    <a href="#" class="form-control d-flex align-items-center gap-2 view-file-preview" style="text-transform: none;"
                    data-file-url="/storage/proposals/${fileObj.file}" >
                        <span>${fileObj.order_no}. </span><i class='bx bx-file-blank'></i><span>${fileObj.file}</span>
                    </a>`;
                }
            });

            fileListHtml += `</div></div></div>`;
            $("#modalFiles").html(fileListHtml);
        }

        var myModal = new bootstrap.Modal(document.getElementById('proposalFIleModal'));
        myModal.show();
    });

    $(document).on('click', '.view-file-preview', function (e) {
        e.preventDefault();
        const fileUrl = $(this).data('file-url');
        const fileExtension = fileUrl.split('.').pop().toLowerCase();

        if (fileExtension !== 'pdf') {
            window.open(fileUrl, '_blank'); // Open Excel, CSV, etc., in a new tab
            $('#fileModal').modal('hide');
            return;
        }

        if (/Mobi|Android/i.test(navigator.userAgent)) { // Open file in new tab if mobile device
            window.open(fileUrl, '_blank');
            $('#fileModal').modal('hide');
        } else {
            $('#fileIframe').attr('src', fileUrl);
            var fileModal = new bootstrap.Modal(document.getElementById('fileModal'));
            fileModal.show();
        }

    });
    $(document).on('click', '.view-single-file-preview', function (e) {
        e.preventDefault();
        const fileUrl = $(this).data('file-url');
        const fileExtension = fileUrl.split('.').pop().toLowerCase();

        // If the file is not a PDF, always open it in a new tab
        if (fileExtension !== 'pdf') {
            window.open(fileUrl, '_blank'); // Open Excel, CSV, etc., in a new tab
            $('#fileModal').modal('hide');
            return;
        }

        if (/Mobi|Android/i.test(navigator.userAgent)) { // Open file in new tab if mobile device
            window.open(fileUrl, '_blank');
            $('#fileModal').modal('hide');
        } else {
            $('#fileIframe').attr('src', fileUrl);
            $('#fileModal').modal('show');
        }
    });

    // $('#fileModal').on('show.bs.modal', function () {
    //     $('#proposalFIleModal').addClass('d-block');
    // });

    // $('#fileModal').on('hidden.bs.modal', function () {
    //     $('#proposalFIleModal').removeClass('d-block');
    //     $('#proposalFIleModal').modal('show');
    // });

    $('#proposalFIleModal').on('hidden.bs.modal', function () {
        setTimeout(function() {
            if ($('.modal-backdrop').length > 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }
        }, 200);
    });



    // SELECT MULTIPLE PROPOSALS IN SECRETARY VIEW MEETING PROPOSAL
    let selectedProposals = new Set();

    $('.select-proposal').on('change', function() {
        let proposalId = $(this).data('id');
        if ($(this).is(':checked')) {
            selectedProposals.add(proposalId);
        } else {
            selectedProposals.delete(proposalId);
        }
        console.log("Updated selected proposals:", Array.from(selectedProposals));
    });

    // console.log(proposalStatus);
    // UPDATE SELECTED PROPOSAL STATUS USING PROPOSAL ACTION
    $('#updateMultiProposalBtn').on('click', function() {
        var proposalStatusInput = $("#proposalStatusInput");
        var action = proposalStatusInput.data('id');
        var status_label = proposalStatus[action+1];
        console.log(action)

        if (action === undefined || action === "") {
            showAlert('warning', 'No Selected Action!', 'Select First.');
            return;
        }
        // console.log(action);
        if (selectedProposals.size === 0) {
            showAlert('warning', 'Warning!', 'No selected Proposal');
            return;
        }

        $.ajax({
            url: "/proposals/update-selected-proposal-status",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                proposals: [...selectedProposals],
                action: action,
            },
            beforeSend: function() {
                $('#updateMultiProposalBtn').html(`<i class='bx bx-loader-alt bx-spin bx-rotate-90' ></i> Updating Proposal Status...`).prop('disabled', true);
            },
            success: function(response) {
                console.log("Success Response:", response);

                $('#updateMultiProposalBtn').html(`<i class='bx bx-send'></i> Update Proposal Status`).prop('disabled', false);

                if (response.type === 'success') {
                    selectedProposals.forEach(id => {
                        let row = $(`input[data-id="${id}"]`).closest('tr');

                        row.find('td.status-cell span')
                        .html(`<i class='bx bx-radio-circle-marked'></i>${status_label}`)
                        .removeClass()
                        .addClass('mb-0 align-items-center d-flex w-px-100 gap-1');

                        // Disable checkbox
                        row.find('input.select-proposal').prop('disabled', true).prop('checked', false);
                    });

                    // Clear selection
                    selectedProposals.clear();
                    $('.select-proposal').prop('checked', false);

                    showAlert(response.type, response.title, response.message);
                }else{
                    showAlert(response.type, response.title, response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error({
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });

                $('#updateMultiProposalBtn').html(`<i class='bx bx-send'></i> Update Proposal Status`).prop('disabled', false);

                showAlert('danger', 'Error!', 'Something Went Wrong!');
            }
        });
    });

    // ASSIGN PROPOSAL ACTION VALUE TO INPUT
    $(".proposal-action").on('click', function(e) {
        e.preventDefault();

        var action_id = $(this).data('id');
        var action_label = $(this).data('label');

        var proposalStatusInput = $("#proposalStatusInput");

        proposalStatusInput.text(action_label);
        proposalStatusInput.data('id', action_id);
        // alert(action_id + ' ' + action_label);
        console.log(proposalStatusInput.data('id'));

        if ([1, 4, 5, 6].includes(action_id)) {
            $("#comment").prop('disabled', false);
            $(".select-proposal-file").removeClass('d-none').addClass('d-block');
        } else {
            $("#comment").prop('disabled', true);
            $(".select-proposal-file").removeClass('d-block').addClass('d-none');
        }
    });

    // SELECT SPECIFIC FILES
    let selectedProposalFiles = new Set();

    $('.select-proposal-file').on('change', function() {
        let proposalFileId = $(this).data('id');

        if ($(this).is(':checked')) {
            selectedProposalFiles.add(proposalFileId);
        } else {
            selectedProposalFiles.delete(proposalFileId);
        }

        console.log("Updated selected proposal files: ", Array.from(selectedProposalFiles));
    });

    // UPDATE SPECIFIC PROPOSAL - SECRETARY POV
    $("#updateProposalStatus").on('click', function(){
        var proposalStatusInput = $("#proposalStatusInput");
        var action = proposalStatusInput.data('id');
        var status_label = proposalStatus[action+1];
        var proposal_id = $("#updateProposalStatus").data('id');
        var comment = $('#comment').val();

        console.log("Selected Action: "+action);

        if (action === undefined || action === "") {
            showAlert('warning', 'No Selected Action!', 'Select First.');
            return;
        }

        if ([1, 4, 5].includes(action)) {
            if (selectedProposalFiles.size === 0) {
                showAlert('warning', 'Warning!', 'Please select a file that needs revision');
                return;
            }

            if(comment == ""){
                showAlert('warning', 'Warning!', 'Please add a comments or suggestions');
                return;
            }
        }

        var formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('proposal_id', proposal_id);
        formData.append('action', action);
        formData.append('comment', comment);

        // Append selected proposal files
        [...selectedProposalFiles].forEach((fileId, index) => {
            formData.append(`proposal_files[${index}]`, fileId);
        });

        $.ajax({
            url: "/proposals/update-proposal-status",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $("#updateProposalStatus").html(`<i class='bx bx-loader-alt bx-spin bx-rotate-90' ></i> Updating Proposal Status...`).prop('disabled', true);
            },
            success: function(response) {
                console.log("Success Response:", response);
                $("#updateProposalStatus").html(`<i class='bx bx-send'></i> Update Proposal Status`).prop('disabled', false);
                showAlert(response.type, response.title, response.message);
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error({
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                $("#updateProposalStatus").html(`<i class='bx bx-send'></i> Update Proposal Status`).prop('disabled', false);
                showAlert('danger', 'Error!', 'Something Went Wrong!');
            }
        });

    });

    // UPDATE PROPOSAL DETAILS - SECRETARY POV
    $("#updateProposalSec").on('click', function (e) {
        e.preventDefault();
        // alert("Clicked");
        var proposalFrm = $("#editProposalFrm");
        var actionUrl = proposalFrm.attr('action');

        $.ajax({
            method: "POST",
            url: actionUrl,
            data: proposalFrm.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#updateProposalSec").html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Saving Changes...</span> `).prop('disabled', true);
            },
            success: function (response) {
                $("#updateProposalSec").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span> `).prop('disabled', false);
                $(".select-proposal-file").removeClass('d-block').addClass('d-none');
                showAlert(response.type, response.title, response.message);
            },
            error: function (xhr, status, error) {
                $("#updateProposalSec").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span> `).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("warning", response.title, response.message);
            }
        });
    });


    // RENAME FILE
    $(document).on("click", ".rename-file-btn", function (e) {
        e.preventDefault();
        let fileId = $(this).data("id");
        $("#renameFileModal").attr("data-file-id", fileId);
        let currentFileName = $(this).data("filename");

        $("#renameFileModal").data("file-id", fileId);
        $("#currentFileName").val(currentFileName);
    });

    $("#renameFileBtn").on("click", function (e) {
        e.preventDefault();

        let fileId = $("#renameFileModal").data("file-id");
        let newFileName = $("#newFileName").val().trim();

        if (newFileName === "") {
            alert("Please enter a new file name.");
            return;
        }

        $.ajax({
            url: "/rename-proposal-file",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                file_id: fileId,
                new_file_name: newFileName
            },
            beforeSend:function(){
                $("#renameFileBtn").text("Renaming...").prop('disabled', true);
            },
            success: function (response) {
                $("#renameFileBtn").text("Rename").prop('disabled', false);
                if(response.type == 'success'){
                    showAlert(response.type, response.title, response.message);
                    location.reload();
                }else{
                    showAlert("danger", "Error!", response.message);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert('danger', 'Error!', response.message);
                $("#renameFileBtn").text("Rename").prop('disabled', false);

            }
        });
    });

    // SUBMIT PROPOSAL SECRETARY
    var endorsedProposals = endorsedProposalIds;
    console.log("Endorsed Proposals: "+ endorsedProposals);

    $('#submitSecBtn').on('click', function(event) {
        event.preventDefault();
        // alert('Cliked');
        var secProposalFrm = $("#submitProposalFrm");
        var actionUrl = secProposalFrm.attr('action');
        if (endorsedProposals) {
            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    endorsedProposals: endorsedProposals
                },
                beforeSend: function() {
                    $('#submitSecBtn').html(`<i class='bx bx-loader-alt bx-spin bx-rotate-90'></i> Submitting`).prop('disabled', true);
                },
                success: function(response) {
                    console.log("Response:", response);

                    if (response.type === 'success') {
                        $('#submitSecBtn').prop('disabled', true);
                        showAlert(response.type, response.title, response.message);

                        window.location.href= response.redirect;
                    }else{
                        showAlert(response.type, response.title, response.message);
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error({
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });

                    $('#submitSecBtn').html(`<i class='bx bx-send'></i> Submit to Universiry`).prop('disabled', false);

                    showAlert('danger', 'Error!', 'Somthing went wrong');
                }
            });
        }else{
            showAlert('danger', 'Error!', 'No Proposals');
        }
    });
})
document.addEventListener("DOMContentLoaded", function () {

    const proposalTable = document.querySelector("#proposalFilesTable tbody");

    new Sortable(proposalTable, {
        animation: 150,
        handle: "td",
        ghostClass: "sortable-ghost",
        onEnd: function (evt) {
            console.log("Row moved from index", evt.oldIndex, "to", evt.newIndex);
            updateOrderNumbers();
        }
    });

    function updateOrderNumbers() {
        let updatedFiles = [];
        document.querySelectorAll("#proposalFilesTable tbody tr").forEach((row, index) => {
            let fileId = row.querySelector(".select-proposal-file").dataset.id;
            let orderNoElement = row.querySelector(".file_order_no");

            orderNoElement.textContent = index + 1;
            updatedFiles.push({
                id: fileId,
                order_no: index + 1
            });
            console.log(fileId);
        });

        fetch("/update-proposal-file-order", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ files: updatedFiles })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Order updated successfully", data);
        })
        .catch(error => {
            console.error("Error updating order", error);
        });
    }
});
