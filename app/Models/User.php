<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // 'campus_id',
        'employee_id',
        'name',
        'image',
        'google_id',
        'email',
        'role',
        'password',
        'verified_email',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * this will hold the forms to be validated
     */
    public function validate() : array {
        return [
            'campus_id' => 'required',
            'employee_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public static function fetchUserById($id) {
        return self::where('id', $id)->first();
    }

    public static function isMyCouncilType($council_type, $role) {
        $response = false;
        if ($council_type == 1) {
            //todo: allow proponents if council type is join council meeting
            return true;
        }
        if ($role == 0 && in_array($council_type , [1,2])) {
            $response = true;
        } else if($role == 1 &&  in_array($council_type , [1,3])) {
            $response = true;
        } else if($role == 2 && in_array($council_type, [1, 2, 3])) {
            $response = true;
        }


        return $response;
    }

    public static function isProponent() {
        $user = Auth::user();
        return in_array($user->role, [0, 1, 2, 6]);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function campus()
{
    return $this->hasOneThrough(Campus::class, Employee::class, 'id', 'id', 'employee_id', 'campus');
}

public function getRoles()
{
    $roles = [];

    if ($this->employee_id) {
        if (\DB::table('local_secretaries')->where('employee_id', $this->employee_id)->exists()) {
            $roles[] = 'Local Secretary';
        }

        if (\DB::table('university_secretaries')->where('employee_id', $this->employee_id)->exists()) {
            $roles[] = 'University Secretary';
        }

        if (\DB::table('board_secretaries')->where('employee_id', $this->employee_id)->exists()) {
            $roles[] = 'Board Secretary';
        }
    }

    return $roles; // Ensure it always returns an array
}


}
