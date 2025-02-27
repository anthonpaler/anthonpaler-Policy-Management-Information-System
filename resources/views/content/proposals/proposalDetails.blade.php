@extends('layouts/contentNavbarLayout')

@section('title', 'Proposals')

@section('content')
<div class="d-flex justify-content-between">
    <h6 class="py-3 mb-4">
        <span class="text-muted fw-light">Proposals / Meetings / Meeting's Proposals</span> Proposal's Details
    </h6>
    <div class="">
        <button type="" class="btn btn-warning d-flex gap-2">
            <i class='bx bx-arrow-back' ></i>
            <span> Go Back</span>
        </button>
    </div>
</div>

<div class="d-flex flex-column gap-3">
    <div class="d-flex gap-3 w-100">
        <div class="card w-100">
            <div class="card-body">
                <h5 class="card-title">Meeting Submission Details</h5>
                <div class="card-details mt-4">
                    <div class="d-flex gap-3">
                        <span class="form-label">Quarter / Year  : </span>
                        <h6>1st Quarter / 2025</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Description  : </span>
                        <h6>N/A</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Modality  : </span>
                        <h6>Face to face</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Venue  : </span>
                        <h6>UISA</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Council Type  : </span>
                        <h6>Joint Local Academic and Administrative Council Meeting</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Status  : </span>
                        <h6 class="text-success">Active</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Submission  : </span>
                        <h6>Tuesday, January 21, 2025 8:00 AM  -   Tuesday, January 21, 2025 8:00 AM</h6>
                    </div>
                </div>
                <!-- <p class="card-text">
                Some quick example text to build on the card title and make up the bulk of the card's content.
                </p>
                <a href="javascript:void(0)" class="btn btn-outline-primary">Go somewhere</a> -->
            </div>
        </div>
        <div class="card w-100">
            <div class="card-body">
                <h5 class="card-title">Proposal's Details</h5>
                <div class="card-details mt-4">
                    <div class="d-flex gap-3">
                        <span class="form-label">Proponent  : </span>
                        <h6>Jhon Doe</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Title  : </span>
                        <h6>College of Engineering OBE_PEO_PO_for Local Academic Council</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Action  : </span>
                        <h6>Endorsement for UADCO</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Proposal File  : </span>
                        <h6 class="text-primary">Download File</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Status  : </span>
                        <h6>For Endorsement</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Council Type  : </span>
                        <h6 class="text-success">Administrative Matters</h6>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="form-label">Sub-type  : </span>
                        <h6>Financial Matters</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">File Preview</h5>
            <div class="file-wrapper mt-4">
                <div class="d-flex gap-3">
                    <span class="form-label">File Name  : </span>
                    <h6 class="text-primary">College_of_Engineering_OBE_PEO_PO_for_Local_Academic_Council.pdf</h6>
                </div>
                <div class="file-container">
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection