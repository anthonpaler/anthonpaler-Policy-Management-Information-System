<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use League\Csv\Reader;

class AdministrativeCouncilMembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Path to your uploaded CSV file
        $csvFilePath = storage_path('app/seeder_data/designation_data.csv');

        // Read the CSV file
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // Set the first row as the header

        // Loop through the rows and insert each empId into the database
        foreach ($csv as $row) {
            DB::table('administrative_council_membership')->insert([
                'employee_id' => $row['empId'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Administrative Council Membership data has been seeded.');
    }
}
