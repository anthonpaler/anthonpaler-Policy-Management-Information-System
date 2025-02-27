<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;
    protected $table = 'campus';

    protected $fillable = ['name'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'campus');
    }
}
