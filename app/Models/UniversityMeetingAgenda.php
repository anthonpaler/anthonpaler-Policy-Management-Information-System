<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UniversityMeetingAgenda extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'university_meeting_agenda';
    protected $fillable = [
        'university_proposal_id',
        'university_meeting_id',
        'status',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'university_proposal_id');
    }

    public function meeting()
    {
        return $this->belongsTo(UniversityCouncilMeeting::class, 'university_meeting_id');
    }
}
