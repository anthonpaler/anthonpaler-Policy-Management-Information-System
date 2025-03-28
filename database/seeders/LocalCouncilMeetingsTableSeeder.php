<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\LocalCouncilMeeting;


class LocalCouncilMeetingsTableSeeder extends Seeder
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
            if ($row['level'] == 0) {
                // Convert 'NULL' or empty values to actual null
                $deletedAt = !empty($row['deleted_at']) && strtoupper($row['deleted_at']) !== 'NULL' ? $row['deleted_at'] : null;
                $venueId = !empty($row['venue']) && strtoupper($row['venue']) !== 'NULL' ? (int) $row['venue'] : null;
                $modeIfOnline = !empty($row['mode_if_online']) && strtoupper($row['mode_if_online']) !== 'NULL' ? (int) $row['mode_if_online'] : null;

                // Insert only if the meeting ID does not already exist
                if (!LocalCouncilMeeting::where('id', $row['id'])->exists()) {
                    LocalCouncilMeeting::create([
                        'id' => $row['id'],
                        'submission_start' => $row['submission_start'],
                        'submission_end' => $row['submission_end'],
                        'meeting_date_time' => $row['meeting_date_time'],
                        'modality' => $row['modality'],
                        'quarter' => $row['quarter'],
                        'year' => $row['year'],
                        'council_type' => $row['council_type'],
                        'mode_if_online' => $modeIfOnline,
                        'campus_id' => $row['campus_id'],
                        'link' => $row['link'],
                        'description' => $row['description'],
                        'venue_id' => $venueId,
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
