<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $connection = 'mysql'; // Use the default connection (policy DB)
    protected $table = 'employees'; // Table name in policy database

    protected $fillable = [
        'FirstName', 'MiddleName', 'LastName', 'EmailAddress', 'Cellphone', 'profilephoto', 'campus'
    ];

    protected $dates = ['deleted_at'];

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus');
    }

}
