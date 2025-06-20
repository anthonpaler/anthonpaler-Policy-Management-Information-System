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
        'university_oob_id',
        'order_no',
        'group_proposal_id',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'university_proposal_id');
    }

        
    public function proposal_group()
    {
        return $this->belongsTo(GroupProposal::class, 'group_proposal_id');
    }
    
    public function meeting()
    {
        return $this->belongsTo(UniversityCouncilMeeting::class, 'university_meeting_id');
    }

    
    public function orderOfBusiness()
    {
        return $this->belongsTo(UniversityOob::class, 'university_oob_id');
    }
}
