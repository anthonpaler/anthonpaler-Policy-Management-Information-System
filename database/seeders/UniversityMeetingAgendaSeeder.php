<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\UniversityMeetingAgenda;

class UniversityMeetingAgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the latest proposals CSV
        $csv = Reader::createFromPath(storage_path('app/seeder_data/proposal_data_m27.csv'), 'r');
        $csv->setHeaderOffset(0);

        // Insert records into the local_meeting_agenda table
        foreach ($csv as $row) {
            // Only process rows where local_meeting_id is present
            if (!empty($row['university_meeting_id']) && strtoupper($row['university_meeting_id']) !== 'NULL') {
                UniversityMeetingAgenda::create([
                    'university_meeting_id' => $row['university_meeting_id'],
                    'university_proposal_id' => $row['id'], // Proposal ID
                    'university_oob_id' => !empty($row['university_oob_id']) && strtoupper($row['university_oob_id']) !== 'NULL' ? $row['university_oob_id'] : null,
                    'status' => !empty($row['status']) && strtoupper($row['status']) !== 'NULL' ? $row['status'] : null,
                    'created_at' => !empty($row['created_at']) && strtoupper($row['created_at']) !== 'NULL' ? $row['created_at'] : null,
                    'updated_at' => !empty($row['updated_at']) && strtoupper($row['updated_at']) !== 'NULL' ? $row['updated_at'] : null,
                    'deleted_at' => !empty($row['deleted_at']) && strtoupper($row['deleted_at']) !== 'NULL' ? $row['deleted_at'] : null,
                ]);
            }
        }
    }
}