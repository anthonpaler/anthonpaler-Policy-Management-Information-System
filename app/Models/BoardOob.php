<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardOob extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'board_oob';
    protected $fillable = [
        'bor_meeting_id',
        'status',
        'preliminaries',
        
    ];

    public function meeting()
    {
        return $this->belongsTo(BorMeeting::class, 'bor_meeting_id');
    }
}
