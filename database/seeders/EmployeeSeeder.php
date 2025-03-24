<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $path = storage_path('app/updated_employee_data.csv');

        if (!file_exists($path)) {
            $this->command->error('CSV file not found.');
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        while ($row = fgetcsv($file)) {
            $data = array_combine($header, $row);

            $created_at = $this->formatDate($data['created_at']);
            $updated_at = $this->formatDate($data['updated_at']);

            Employee::updateOrCreate(
                ['id' => $data['id']],
                [
                    'EmailAddress' => $data['EmailAddress'],
                    'campus' => $data['campus'],
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ]
            );
        }

        fclose($file);
    }

    private function formatDate($date)
    {
        try {
            return Carbon::createFromFormat('d/m/Y H:i', $date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now(); // Default to current timestamp if parsing fails
        }
    }
}
