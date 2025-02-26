function showAlert(type, title, message) {
    if (type == "success") {
        toastr.success(message, title, {
            closeButton: true,
            progressBar: true,
        });
    } else if (type == "warning") {
        toastr.warning(message, title, {
            closeButton: true,
            progressBar: true,
        });
    } else if (type == "danger") {
        toastr.error(message, title, {  
            closeButton: true,
            progressBar: true,
        });
    } else {
        toastr.info(message, title, {
            closeButton: true,
            progressBar: true,
        });
    }
}   
