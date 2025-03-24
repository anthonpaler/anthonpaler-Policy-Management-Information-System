<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalOob extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'local_oob';
    protected $fillable = [
        'local_council_meeting_id',
        'status',
        'preliminaries',
        'previous_minutes'
    ];

    public function meeting()
    {
        return $this->belongsTo(LocalCouncilMeeting::class, 'local_council_meeting_id');
    }
}
