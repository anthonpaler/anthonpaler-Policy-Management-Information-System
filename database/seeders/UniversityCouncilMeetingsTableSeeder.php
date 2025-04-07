<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\UniversityCouncilMeeting;


class UniversityCouncilMeetingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load CSV file
        $csv = Reader::createFromPath(storage_path('app/seeder_data/meetings_data_m27.csv'), 'r');
        $csv->setHeaderOffset(0);

        // Insert records with level = 0
        foreach ($csv as $row) {
            if ($row['level'] == 1) {
                // Convert 'NULL' or empty values to actual null
                $deletedAt = !empty($row['deleted_at']) && strtoupper($row['deleted_at']) !== 'NULL' ? $row['deleted_at'] : null;
                $modeIfOnline = !empty($row['mode_if_online']) && strtoupper($row['mode_if_online']) !== 'NULL' ? (int) $row['mode_if_online'] : null;

                // Insert only if the meeting ID does not already exist
                if (!UniversityCouncilMeeting::where('id', $row['id'])->exists()) {
                    UniversityCouncilMeeting::create([
                        'id' => $row['id'],
                        'submission_start' => $row['submission_start'],
                        'submission_end' => $row['submission_end'],
                        'meeting_date_time' => $row['meeting_date_time'],
                        'modality' => $row['modality'],
                        'quarter' => $row['quarter'],
                        'year' => $row['year'],
                        'council_type' => $row['council_type'],
                        'mode_if_online' => $modeIfOnline,
                        'link' => $row['link'],
                        'description' => $row['description'],
                        'venue' => '',
                        'status' => $row['status'],
                        'creator_id' => $row['creator_id'],
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at'],
                        'deleted_at' => $deletedAt,
                    ]);
                }
            }
        }
    }
}
