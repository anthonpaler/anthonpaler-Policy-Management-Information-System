<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProposalLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ProposalLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = storage_path('app/seeder_data/logs_data_m27.csv');
        $userCsvPath = storage_path('app/seeder_data/user_data_m27.csv');

        // Load latest user data to map user_id to employee_id
        $userCsv = Reader::createFromPath($userCsvPath, 'r');
        $userCsv->setHeaderOffset(0);
        $userMap = [];
        foreach ($userCsv as $userRow) {
            $userMap[$userRow['id']] = $userRow['employee_id'];
        }

        // Load the latest proposal logs CSV
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            if (isset($userMap[$row['user_id']])) {
                ProposalLog::create([
                    'status' => $row['status'],
                    'comments' => ($row['comments'] === 'NULL' || empty($row['comments'])) ? null : $row['comments'],
                    'level' => $row['level'],
                    'action' => !empty($row['action']) ? $row['action'] : null,
                    'file_id' => !empty($row['file_id']) ? $row['file_id'] : null,
                    'proposal_id' => $row['proposal_id'],
                    'employee_id' => $userMap[$row['user_id']],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at']
                ]);
            }
        }
    }
}
