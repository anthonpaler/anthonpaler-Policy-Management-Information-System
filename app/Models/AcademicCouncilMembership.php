<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;


class AcademicCouncilMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
    ];

    public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}
}
