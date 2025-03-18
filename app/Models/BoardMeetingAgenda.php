<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BoardMeetingAgenda extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'board_meeting_agenda';
    protected $fillable = [
        'board_proposal_id',
        'bor_meeting_id',
        'status',
        'board_oob_id',
        'order_no',
        'group_proposal_id',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'board_proposal_id');
    }
        
    public function proposal_group()
    {
        return $this->belongsTo(GroupProposal::class, 'group_proposal_id');
    }

    public function meeting()
    {
        return $this->belongsTo(BorMeeting::class, 'bor_meeting_id');
    }

    public function orderOfBusiness()
    {
        return $this->belongsTo(BoardOob::class, 'board_oob_id');
    }
}
