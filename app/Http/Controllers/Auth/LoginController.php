<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
class LoginController extends Controller
{
    //   */
    public function index()
    {
        // dd(AESCipher::encrypt("thor"));

        return view('content.auth.auth-login');
    }


    public function handleGoogleLogin(Request $request)
    {
        $user = $this->findOrCreateUser($request);

        // Check if findOrCreateUser returned an error response
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the error response
        }

        session(['user_role' => $user->role]);


        Auth::login($user);
        return response()->json([
            'message' => 'Login successful.',
            'success' => true,
            'user_position' => $user->role,
            'redirect' => route(getUserRole().'.dashboard'),
        ]);
    }


    protected function findOrCreateUser($request)
    {
        try {
            // Check if the email exists in the employee table
            $employee = DB::table('employees')->where('EmailAddress', $request->email)->first();

            if (!$employee) {
                return response()->json([
                    'message' => 'Unauthorized: Employee record not found.',
                    'success' => false,
                ], 403);
            }
            $isBoardSecretary = DB::table('board_secretaries')
            ->where('employee_id', $employee->id)
            ->exists();

            $isUniversitySecretary = DB::table('university_secretaries')
            ->where('employee_id', $employee->id)
            ->exists();

            // Check if the user has a "Local Secretary" PositionTitleCode
            $isLocalSecretary = DB::table('local_secretaries')
            ->where('employee_id', $employee->id)
            ->exists();

            $isAcademicCouncil = DB::table('academic_council_membership')
            ->where('employee_id', $employee->id)
            ->exists();

            $isAdministrativeCouncil = DB::table('administrative_council_membership')
            ->where('employee_id', $employee->id)
            ->exists();


            // Determine the role based on the conditions
              if ($isLocalSecretary) {
                  $role = 3;

              } elseif($isUniversitySecretary){
                  $role = 4;
                   // University Campus Secretary

              } elseif($isBoardSecretary){
                  $role = 5;
                  // Board Campus Secretary

              }elseif ($isAcademicCouncil && $isAdministrativeCouncil) {
                  $role = 2;
                  // Academic Council / Administrative Council

              } elseif ($isAdministrativeCouncil) {
                  $role = 1;
                  // Administrative Council

                } elseif ($isAcademicCouncil) {
                  $role = 0;
                   // Academic Council

                }elseif ($request->email == "reyanthonpaler1@gmail.com") {
                  $role = 7;
                   // Super Admin

                }else{
                $role = null;
            }


            // Check if the user already exists in the users table
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Register a new user
                $user = User::create([
                    'email' => $request->email,
                    'name' => $request->name,
                    'image' => $request->image,
                    'password' => Hash::make(Str::random(10)), // Generate a random password
                    'role' => $role,
                    'employee_id' => $employee->id, 
                ]);

            } else {
                // Update the user's role and employee ID if necessary
                $user->update([
                    'role' => $role,
                    'employee_id' => $employee->id,
                    'google_id' => $request->google_id,
                    'image' => $request->image,

                ]);
            }

            return $user;

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in findOrCreateUser: ' . $e->getMessage());

            // Return a generic error message
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
                'success' => false,
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
