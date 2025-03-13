<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UniversityOob extends Model
{
    use HasFactory;
    
    use SoftDeletes;
    protected $table = 'university_oob';
    protected $fillable = [
        'university_council_meeting_id',
        'status',
        'preliminaries',
    ];

    public function meeting()
    {
        return $this->belongsTo(UniversityCouncilMeeting::class, 'university_council_meeting_id');
    }
}
