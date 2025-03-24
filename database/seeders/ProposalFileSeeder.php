<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\ProposalFile;
use App\Models\Proposal;


class ProposalFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load CSV file
        $csv = Reader::createFromPath(storage_path('app/latest_pfiles.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            // Ensure the proposal_id exists before inserting
            $proposalExists = Proposal::where('id', $row['proposal_id'])->exists();

            if ($proposalExists) {
                ProposalFile::create([
                    'file' => $row['file'],
                    'version' => $row['version'],
                    'file_status' => $row['file_status'],
                    'is_active' => $row['is_active'],
                    'file_reference_id' => is_numeric($row['file_reference_id']) ? (int)$row['file_reference_id'] : null,
                    'proposal_id' => $row['proposal_id'],
                    'order_no' => !empty($row['order_no']) ? $row['order_no'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                echo "Skipping: Proposal ID " . $row['proposal_id'] . " not found.\n";
            }
        }
    }
}