$(document).ready(function() {
    $('#matter').on('change', function() {
        var matter = $(this).val();
        var subType = $('#sub_type');
        var actionSelect = $('#action');
    
        actionSelect.empty();
    
        if (matter == 1) {
            actionSelect.append(`
                <option value="1">Endorsement for UACAD</option>
                <option value="3">Endorsement for BOR</option>
            `);
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');
        } else if (matter == 2) {
            subType.prop('disabled', false);
            $('#subTypeContainer').css('display', 'block');
    
            actionSelect.append(`
                <option value="2">Endorsement for UADCO</option>
                <option value="3">Endorsement for BOR</option>
            `);
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
    // Ensure the primary proponent is added only once
    if (!selectedProponents.some(p => p.id === primaryProponent.id)) {
            selectedProponents.push(primaryProponent);
    }
    console.log(selectedProponents);
    
    let proponentIds = selectedProponents.map(selectedProponents => selectedProponents.id);
    proponentInput.val(proponentIds.join(','));
    
    $("#addProponent").on("keyup", function () {
        let query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                url: "/proponents/search-users",
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
                                                    <img src="${user.image ? user.image : '/default-avatar.png'}" alt="Avatar" class="rounded-circle">
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
                    <div class="d-flex justify-content-between align-items-center ms-2 me-2">
                        <div class="d-flex justify-content-start align-items-center ">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <img src="${userImage}" alt="Avatar" class="rounded-circle">
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

    // SUBMIT PROPOSAL FOR PROPONENT
    let submitProposalBtn = $("#submitProposalBtn");
    submitProposalBtn.on('click', function (e) {
        e.preventDefault();
    
        var proposalFrm = $("#proposalFrm");
        var actionUrl = proposalFrm.attr('action');
    
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: proposalFrm.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                submitProposalBtn.html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Submitting...</span> `).prop('disabled', true);
            },
            success: function (response) {
                proposalFrm[0].reset();
                submitProposalBtn.html(`<i class='bx bx-send'></i>
                    <span>Submit Proposal</span> `).prop('disabled', false);
                showAlert(response.type, response.title, response.message);
            
               // Loop through each file in Dropzone and remove its preview element
                myDropzone.files.forEach(function(file) {
                    if (file.previewElement) {
                        file.previewElement.remove();
                    }
                });

                // Clear the Dropzone file array without triggering removedfile callback
                myDropzone.files = [];

                // Clear the proposalFiles array and update the hidden input field
                proposalFiles = [];
                proposalInput.val("");
            
                // Reset selected proponents, keeping only the primary proponent
                selectedProponents = [primaryProponent];
                let proponentIds = selectedProponents.map(proponent => proponent.id);
                proponentInput.val(proponentIds.join(','));
            
                // Remove additional proponents from UI
                $("#proponentListCon").html(`
                    <li data-id="${primaryProponent.id}">
                        <div class="d-flex justify-content-between align-items-center ms-2 me-2">
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
            },            
            error: function (xhr, status, error) {
                submitProposalBtn.html(`<i class='bx bx-send'></i>
                    <span>Submit Proposal</span> `).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("warning", response.title, response.message);
            }
        });
    });
})
