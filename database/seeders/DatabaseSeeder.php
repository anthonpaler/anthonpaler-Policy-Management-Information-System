<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the individual seeders
        $this->call([
            EmployeeSeeder::class,
            ProposalsTableSeeder::class,
            LocalCouncilMeetingsTableSeeder::class,
            UniversityCouncilMeetingsTableSeeder::class,
            UniversityOobSeeder::class,
            LocalOobSeeder::class,
            LocalMeetingAgendaSeeder::class,
            UniversityMeetingAgendaSeeder::class,
            ProposalFileSeeder::class,
            ProposalLogSeeder::class,
            ProposalProponentsSeeder::class,
            AdministrativeCouncilMembershipSeeder::class,
        ]);
    }
}