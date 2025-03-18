document.addEventListener("DOMContentLoaded", function () {
    const toggleFullscreenBtn = document.getElementById("toggleIframeFullscreen");
    const fileIframe = document.getElementById("fileIframe");

    toggleFullscreenBtn.addEventListener("click", function () {
        if (!document.fullscreenElement) {
            if (fileIframe.requestFullscreen) {
                fileIframe.requestFullscreen();
            } else if (fileIframe.mozRequestFullScreen) { // Firefox
                fileIframe.mozRequestFullScreen();
            } else if (fileIframe.webkitRequestFullscreen) { // Chrome, Safari, Opera
                fileIframe.webkitRequestFullscreen();
            } else if (fileIframe.msRequestFullscreen) { // IE/Edge
                fileIframe.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari, Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }
        }
    });
});