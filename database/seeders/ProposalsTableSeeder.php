<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\Proposal;


class ProposalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

          // Load CSV files
          $proposalsCsv = Reader::createFromPath(storage_path('app/seeder_data/proposal_data_m27.csv'), 'r');
          $proposalsCsv->setHeaderOffset(0);

          $usersCsv = Reader::createFromPath(storage_path('app/seeder_data/user_data_m27.csv'), 'r');
          $usersCsv->setHeaderOffset(0);

          // Create a mapping of user id to employee_id
          $userMap = [];
          foreach ($usersCsv as $user) {
              $userMap[$user['id']] = $user['employee_id'];
          }

          // Insert proposals with mapped employee_id
          foreach ($proposalsCsv as $proposal) {
              // Get the correct employee_id using proponent_id
            //   $employeeId = $userMap[$proposal['proponent_id']] ?? null;

              // Convert 'NULL' or empty strings to actual null values for timestamps
              $deletedAt = (!empty($proposal['deleted_at']) && strtoupper($proposal['deleted_at']) !== 'NULL') ? $proposal['deleted_at'] : null;
              $subType = (!empty($proposal['sub_type']) && strtoupper($proposal['sub_type']) !== 'NULL') ? (int) $proposal['sub_type'] : null;

              // Ensure `employee_id` is available before proceeding
            //   if (!$employeeId) {
            //       continue; // Skip if employee_id is missing
            //   }

              // Use firstOrCreate to avoid duplicates
              Proposal::updateOrInsert(
                  ['id' => $proposal['id']], // Unique condition
                  [
                      'title' => $proposal['title'],
                      'type' => $proposal['type'],
                      'sub_type' => $subType,
                      'action' => $proposal['action'],
                      'status' => $proposal['status'],
                    //   'employee_id' => $employeeId, // Insert employee_id instead of proponent_id
                      'campus_id' => $proposal['campus_id'],
                      'created_at' => $proposal['created_at'],
                      'updated_at' => $proposal['updated_at'],
                      'deleted_at' => $deletedAt,
                  ]
              );
          }
      }
  }
