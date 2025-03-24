<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\UniversityOob;

class UniversityOobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the latest OOB CSV
        $csv = Reader::createFromPath(storage_path('app/latest_oob.csv'), 'r');
        $csv->setHeaderOffset(0);

        // Insert records into the local_oob table
        foreach ($csv as $row) {
            // Only process rows where level = 0
            if (isset($row['level']) && (int)$row['level'] === 1) {
                UniversityOob::create([
                    'id' => $row['id'],
                    'university_council_meeting_id' => $row['meeting_id'],
                    'preliminaries' => $row['preliminaries'],
                    'previous_minutes' => $row['previous_minutes'],
                    'status' => 1, 
                    'created_at' => !empty($row['created_at']) && strtoupper($row['created_at']) !== 'NULL' ? $row['created_at'] : null,
                    'updated_at' => !empty($row['updated_at']) && strtoupper($row['updated_at']) !== 'NULL' ? $row['updated_at'] : null,
                    'deleted_at' => !empty($row['deleted_at']) && strtoupper($row['deleted_at']) !== 'NULL' ? $row['deleted_at'] : null,
                ]);
            }
        }
    }
}