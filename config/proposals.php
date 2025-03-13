<?php
return [
    'matters' => [
        1 => 'Academic Matters',
        2 => 'Administrative Matters',
        3 => 'Matters for Confirmation',
        4 => 'Matters for Information',
    ],
    'proposal_subtypes' => [
        'Financial Matters',
        'Legal Matters',
        'Personnel Matters',
        'Administrative Matters'
    ],
    'status' => [
        0 => 'For Endorsement',
        1 => 'Posted to Agenda',
        2 => 'Returned',
        3 => 'Approved',
        4 => 'Endorsed',
        5 => 'Approved with Coletilla',
        6 => 'Endorsed with Coletilla',
        7 => 'Deferred',
        8 => 'For Review',
        9 => 'Resubmitted',
        10 => 'Confirmed',
    ],
    'requested_action' => [
        // PROPONENT 
        4 => 'Endorsement for Local ACAD',
        5 => 'Endorsement for Local ADCO',

        // LOCAL SECRETARY AND PROPONENT
        1 => 'Endorsement for UACAD',
        2 => 'Endorsement for UADCO',

        // UNIVERSITY SECRETARY
        3 => 'Endorsement for BOR',
        6 => 'Approval for UACAD',
        7 => 'Approval for UADCO',

        // BOARD SECRETARY
        8 => 'BOR Approval',
        9 => 'BOR Confirmation',
        10 => 'BOR Information',
    ],
    'proposal_file_status' => [
        1 => 'Pending',
        2 => 'Accepted',
        3 => 'For Revision',
        4 => 'Revised',
    ],
    'proposal_action' => [
        // FOR SECRETARY PROPOSAL ACTIONS
        // BEFORE MEETING
        0 => 'Post to Agenda',
        1 => 'Return',

        // AFTER MEETING
        2 => 'Approve',
        3 => 'Endorse',
        4 => 'Approve with Coletilla',
        5 => 'Endorse with Coletilla',
        6 => 'Deffer',
        9 => 'Confirm',

        // FOR PROPONENT PROPOSAL ACTIONS
        7 => 'Submit',
        8 => 'Resubmit',
    ]
];