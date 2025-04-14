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

    public function storeAcademicMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'campus' => 'required|string'
        ]);

        Employee::firstOrCreate(
            ['EmailAddress' => $request->email],
            ['campus' => $request->campus]
        );

        return response()->json(['message' => 'Member added successfully']);
    }

   

}
