<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use League\Csv\Reader;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the CSV file
        $csvFilePath = storage_path('app/seeder_data/user_data_m27.csv');

        if (!file_exists($csvFilePath) || !is_readable($csvFilePath)) {
            $this->command->error("CSV file not found or is not readable: $csvFilePath");
            return;
        }

        // Read CSV file
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // Set headers

        $insertData = [];

        foreach ($csv->getRecords() as $row) {
            $insertData[] = [
                'employee_id'       => $this->sanitizeValue($row['employee_id']),
                'name'              =>  $this->sanitizeValue($row['name']),
                'email'             => $this->sanitizeValue($row['email']),
                'email_verified_at' => $this->parseDate($row['email_verified_at']),
                'password'          => isset($row['password']) ? Hash::make($row['password']) : null,
                'image'             => $this->sanitizeValue($row['image']),
                'role'              => $this->sanitizeValue($row['role']),
                'google_id'         => $this->sanitizeValue($row['google_id']),
                'remember_token'    => $this->sanitizeValue($row['remember_token']),
                'created_at'        => $this->parseDate($row['created_at'], now()),
                'updated_at'        => $this->parseDate($row['updated_at'], now()),
                'deleted_at'        => $this->parseDate($row['deleted_at']),
            ];
        }

        // Insert data into 'users' table
        if (!empty($insertData)) {
            DB::table('users')->insert($insertData);
            $this->command->info('User data seeded successfully!');
        } else {
            $this->command->warn('No valid data found in CSV.');
        }
    }

    /**
     * Convert "NULL" or empty strings to actual null.
     */
    private function sanitizeValue($value)
    {
        return (!isset($value) || trim($value) === '' || strtolower($value) === 'null') ? null : $value;
    }

    /**
     * Safely parse date values.
     */
    private function parseDate($date, $default = null)
    {
        if ($this->sanitizeValue($date) === null) {
            return $default; // Return default value if empty/null
        }
        
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return $default; // Return default if parsing fails
        }
    }
}
