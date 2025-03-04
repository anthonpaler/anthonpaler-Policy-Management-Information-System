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
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'local_proposal_id');
    }

    public function meeting()
    {
        return $this->belongsTo(LocalCouncilMeeting::class, 'local_council_meeting_id');
    }
}
