<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ProposalProponentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load user data
        $userCsv = Reader::createFromPath(storage_path('app/seeder_data/user_data_m27.csv'), 'r');
        $userCsv->setHeaderOffset(0);
        $userRecords = iterator_to_array($userCsv->getRecords());

        // Map user id to employee_id
        $userMap = [];
        foreach ($userRecords as $user) {
            $userMap[$user['id']] = $user['employee_id'];
        }

        // Load proposal data
        $proposalCsv = Reader::createFromPath(storage_path('app/seeder_data/proposal_data_m27.csv'), 'r');
        $proposalCsv->setHeaderOffset(0);
        $proposalRecords = iterator_to_array($proposalCsv->getRecords());

        // Insert data into proposal_proponents
        $insertData = [];
        foreach ($proposalRecords as $proposal) {
            $proponentId = $proposal['proponent_id'];
            if (isset($userMap[$proponentId])) {
                $insertData[] = [
                    'proposal_id' => $proposal['id'],
                    'employee_id' => $userMap[$proponentId],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($insertData)) {
            DB::table('proposal_proponents')->insert($insertData);
        }
    }
}
