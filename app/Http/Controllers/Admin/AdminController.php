<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmisEmployee;
use App\Models\Employee;
use App\Models\AcademicCouncilMembership;
use App\Models\AdministrativeCouncilMembership;
use App\Models\Campus;


use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function searchHrmisEmail(Request $request)
    {
        $search = $request->input('query');
            // Step 1: Fetch from HRMIS connection
            $hrmisResults = HrmisEmployee::where('EmailAddress', 'LIKE', "%$search%")
            ->select('EmailAddress', 'Campus')
            ->limit(10)
            ->get();

            // Step 2: Get unique campus IDs from results
        $campusIds = $hrmisResults->pluck('Campus')->unique()->filter()->all();

             // Step 3: Get campus names from local connection
        $campusNames = Campus::whereIn('id', $campusIds)
            ->pluck('name', 'id'); // key = id, value = name

         // Step 4: Map the campus name back to each employee
        $finalResults = $hrmisResults->map(function ($item) use ($campusNames) {
            return [
                'EmailAddress' => $item->EmailAddress,
                'CampusID' => $item->Campus,
                'CampusName' => $campusNames[$item->Campus] ?? 'Unknown'
            ];
        });

        return response()->json($finalResults);
    }

    public function AddAcademicMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'campus' => 'required|integer',
        ]);
    
        try {
            // First, create or get the employee
            $employee = Employee::firstOrCreate(
                ['EmailAddress' => $request->email],
                ['campus' => $request->campus]
            );
    
            // Then, add to academic_council_membership if not already present
            $exists = DB::table('academic_council_membership')
                        ->where('employee_id', $employee->id)
                        ->exists();
    
            if (!$exists) {
                DB::table('academic_council_membership')->insert([
                    'employee_id' => $employee->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            return response()->json(['message' => 'Academic Council Member added successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function AddAdministrativeMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'campus' => 'required|integer',
        ]);
    
        try {
            // First, create or get the employee
            $employee = Employee::firstOrCreate(
                ['EmailAddress' => $request->email],
                ['campus' => $request->campus]
            );
    
            // Then, add to academic_council_membership if not already present
            $exists = DB::table('administrative_council_membership')
                        ->where('employee_id', $employee->id)
                        ->exists();
    
            if (!$exists) {
                DB::table('administrative_council_membership')->insert([
                    'employee_id' => $employee->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            return response()->json(['message' => 'Administrative Council Member added successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function AddJointCouncilMember(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'campus' => 'required|integer',
    ]);

    try {
        // Step 1: Add to employees table
        $employee = Employee::firstOrCreate(
            ['EmailAddress' => $request->email],
            ['campus' => $request->campus]
        );

        // Step 2: Add to academic_council_membership if not exists
        DB::table('academic_council_membership')->updateOrInsert(
            ['employee_id' => $employee->id],
            ['updated_at' => now(), 'created_at' => now()]
        );

        // Step 3: Add to administrative_council_membership if not exists
        DB::table('administrative_council_membership')->updateOrInsert(
            ['employee_id' => $employee->id],
            ['updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['message' => 'Joint Council Member added successfully']);
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}

   

}
