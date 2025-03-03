<?php
return [
    'council_types' => [
       'local_level' => [
            1 => 'Joint Local Academic and Administrative Council Meeting',
            2 => 'Local Academic Council Meeting',
            3 => 'Local Administrative Council Meeting',
        ],
        'university_level' => [
            1 => 'Joint University Academic and Administrative Council Meeting',
            2 => 'University Academic Council Meeting',
            3 => 'University Administrative Council Meeting',
        ],
        'board_level' => [
            1 => 'BOR Meeting',
        ],

    ],
   'quaterly_meetings' => [
        1 => '1st Quarter',
        2 => '2nd Quarter',
        3 => '3rd Quarter',
        4 => '4th Quarter',
        // 5 => 'Special Meeting',
    ],
    'status' => [
        0 => 'Open',
        1 => 'Close',
    ],
    'level' => [
        'Local',
        'University',
        'Board'
    ],
    'modalities' => [
        1 => 'Face to face',
        2 => 'Online',
        3 => 'Hybrid',
    ],
    'mode_if_online_types' => [
        1 => 'Zoom',
        2 => 'Google Meet',
        3 => 'Skype'
    ],
];
