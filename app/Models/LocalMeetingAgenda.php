<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LocalMeetingAgenda extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'local_meeting_agenda';

    protected $fillable = [
        'local_council_meeting_id',
        'local_proposal_id',
        'status',
        'local_oob_id',
        'order_no',
        'group_proposal_id',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'local_proposal_id');
    }
    
    public function proposal_group()
    {
        return $this->belongsTo(GroupProposal::class, 'group_proposal_id');
    }

    public function meeting()
    {
        return $this->belongsTo(LocalCouncilMeeting::class, 'local_council_meeting_id');
    }
    public function orderOfBusiness()
    {
        return $this->belongsTo(LocalOob::class, 'local_oob_id');
    }

    public static function countProposalsByMeeting($meetingId)
    {
    return self::where('local_council_meeting_id', $meetingId)
        ->whereNotNull('local_proposal_id') // optional: count only if proposal is linked
        ->count();
    }

}
