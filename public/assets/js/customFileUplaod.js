// COSTUM MULTIPLE FILE UPLOAD
const dropArea = document.getElementById("dropArea");
const fileUpload = document.getElementById("fileUpload");
const fileList = document.getElementById("fileList");
let uploadedProposalFiles = [];

dropArea.addEventListener("click", () => fileUpload.click());
dropArea.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropArea.style.background = "#f1f1f1";
});
dropArea.addEventListener("dragleave", () => {
    dropArea.style.background = "#fff";
});
dropArea.addEventListener("drop", (e) => {
    e.preventDefault();
    dropArea.style.background = "#fff";
    handleFiles(e.dataTransfer.files);
});
fileUpload.addEventListener("change", (e) => {
    handleFiles(e.target.files);
});

function handleFiles(files) {
    Array.from(files).forEach((file) => {
        const fileType = file.name.split('.').pop().toLowerCase();

        // Allowed file types: PDF, CSV, Excel (.xls, .xlsx)
        const allowedExtensions = ['pdf', 'csv','xls', 'xlsx'];

        if (!allowedExtensions.includes(fileType)) {
            showAlert('danger', 'Unsupported File', 'Only PDF and Excel files (.pdf, .csv, .xls, .xlsx) are allowed.")');
            return;
        }

        if (!uploadedProposalFiles.some(f => f.name === file.name)) {
            uploadedProposalFiles.push(file);
            displayFile(file);
            simulateUpload(file);
        }
    });
}


function displayFile(file) {
    const listItem = document.createElement("li");
    listItem.classList.add("file-item");

    const uploadedFilesLabel = document.getElementById("uploadedFilesLabel");

    if (fileList.children.length === 0) {
        uploadedFilesLabel.style.display = "block";
    }

    const fileType = file.name.split('.').pop().toLowerCase();
    const iconSrc = getImageByFileType(fileType);

    listItem.innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <div class="">
                <img src="${iconSrc}" class="file-icon" alt="File Icon">
            </div>
            <div class="file-name">
                <strong>${file.name}</strong>
                <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="delete-file-btn"><i class='bx bx-trash'></i></button>
            <div class="progress-circle" data-progress="0">
                <span class="progress-text">0%</span>
            </div>
        </div>
    `;

    console.log(uploadedProposalFiles);

    listItem.querySelector(".delete-file-btn").addEventListener("click", () => {
        uploadedProposalFiles = uploadedProposalFiles.filter(f => f.name !== file.name);
        listItem.remove();

        if (fileList.children.length === 0) {
            uploadedFilesLabel.style.display = "none";
        }
        console.log(uploadedProposalFiles);
    });

    fileList.appendChild(listItem);
}

// CUSTOM UPLOAD CIRCLE PROGRESS BAR
function simulateUpload(file) {
    const listItem = Array.from(fileList.children).find(li => li.querySelector("strong").textContent === file.name);
    if (!listItem) return;

    const progressCircle = listItem.querySelector(".progress-circle");
    const progressText = listItem.querySelector(".progress-text");
    let progress = 0;
    const interval = setInterval(() => {
        if (progress >= 100) {
            clearInterval(interval);
            progressText.innerHTML  =`<i class='bx bx-check progress-check'></i>`;
            progressCircle.style.background = "#39DA8A";
            return;
        }
        progress += 10;
        progressCircle.setAttribute("data-progress", progress);
        progressCircle.style.background = `conic-gradient(#fd7e14 ${progress}%, #ffffff ${progress}% 100%)`;
        progressText.textContent = `${progress}%`;
    }, 200);
}

// COSTUM MULTIPLE FILE UPLOAD
// const groupDropArea = document.getElementById("groupDropArea");
// const groupFileUpload = document.getElementById("groupFileUpload");
// const groupFileList = document.getElementById("groupFileList");
// let uploadedGroupFiles = [];

// groupDropArea.addEventListener("click", () => groupFileUpload.click());
// groupDropArea.addEventListener("dragover", (e) => {
//     e.preventDefault();
//     groupDropArea.style.background = "#f1f1f1";
// });
// groupDropArea.addEventListener("dragleave", () => {
//     groupDropArea.style.background = "#fff";
// });
// groupDropArea.addEventListener("drop", (e) => {
//     e.preventDefault();
//     groupDropArea.style.background = "#fff";
//     handleGroupFiles(e.dataTransfer.files);
// });
// groupFileUpload.addEventListener("change", (e) => {
//     handleGroupFiles(e.target.files);
// });

// function handleGroupFiles(files) {
//     Array.from(files).forEach((file) => {
//         const fileType = file.name.split('.').pop().toLowerCase();

//         // Allowed file types: PDF, CSV, Excel (.xls, .xlsx)
//         const allowedExtensions = ['pdf', 'csv','xls', 'xlsx'];

//         if (!allowedExtensions.includes(fileType)) {
//             showAlert('danger', 'Unsupported File', 'Only PDF and Excel files (.pdf, .csv, .xls, .xlsx) are allowed.")');
//             return;
//         }

//         if (!uploadedGroupFiles.some(f => f.name === file.name)) {
//             uploadedGroupFiles.push(file);
//             displayGroupFile(file);
//             simulateGroupUpload(file);
//         }
//     });
// }


// function displayGroupFile(file) {
//     const listItem = document.createElement("li");
//     listItem.classList.add("file-item");

//     const groupUploadedFilesLabel = document.getElementById("groupUploadedFilesLabel");

//     if (groupFileList.children.length === 0) {
//         groupUploadedFilesLabel.style.display = "block";
//     }

//     const fileType = file.name.split('.').pop().toLowerCase();
//     const iconSrc = getImageByFileType(fileType);

//     listItem.innerHTML = `
//         <div class="d-flex align-items-center gap-2">
//             <div class="">
//                 <img src="${iconSrc}" class="file-icon" alt="File Icon">
//             </div>
//             <div class="file-name">
//                 <strong>${file.name}</strong>
//                 <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
//             </div>
//         </div>
//         <div class="d-flex gap-2">
//             <button class="delete-file-btn"><i class='bx bx-trash'></i></button>
//             <div class="progress-circle" data-progress="0">
//                 <span class="progress-text">0%</span>
//             </div>
//         </div>
//     `;

//     console.log(uploadedGroupFiles);

//     listItem.querySelector(".delete-file-btn").addEventListener("click", () => {
//         uploadedGroupFiles = uploadedGroupFiles.filter(f => f.name !== file.name);
//         listItem.remove();

//         if (groupFileList.children.length === 0) {
//             groupUploadedFilesLabel.style.display = "none";
//         }
//         console.log(uploadedGroupFiles);
//     });

//     groupFileList.appendChild(listItem);
// }

// // CUSTOM UPLOAD CIRCLE PROGRESS BAR
// function simulateGroupUpload(file) {
//     const listItem = Array.from(groupFileList.children).find(li => li.querySelector("strong").textContent === file.name);
//     if (!listItem) return;

//     const progressCircle = listItem.querySelector(".progress-circle");
//     const progressText = listItem.querySelector(".progress-text");
//     let progress = 0;
//     const interval = setInterval(() => {
//         if (progress >= 100) {
//             clearInterval(interval);
//             progressText.innerHTML  =`<i class='bx bx-check progress-check'></i>`;
//             progressCircle.style.background = "#39DA8A";
//             return;
//         }
//         progress += 10;
//         progressCircle.setAttribute("data-progress", progress);
//         progressCircle.style.background = `conic-gradient(#fd7e14 ${progress}%, #ffffff ${progress}% 100%)`;
//         progressText.textContent = `${progress}%`;
//     }, 200);
// }





