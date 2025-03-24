<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorMeeting extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'bor_meetings';

    protected $fillable = [
        'creator_id',
        'level',
        'description',
        'meeting_date_time',
        'quarter',
        'year',
        'venue_id',
        'status',
        'council_type',
        'action',
        'modality',
        'mode_if_online',
        'link',
        'submission_start',
        'submission_end',
    ];

    protected $dates = ['submission_start', 'submission_end', 'meeting_date_time'];

    // DETERMINE IF THE SUBMISSION IS CLOSED
    public function getIsSubmissionClosedAttribute()
    {
        $currentDate = Carbon::now();
        return $currentDate->greaterThan($this->submission_end) || 
               $currentDate->lessThan($this->submission_start) ||  
               $currentDate->greaterThan($this->meeting_date_time);
    }

    // DETERMINE IF THE MEETING EXIST IN BOARD OOB
    public function orderOfBusiness()
    {
        return $this->hasOne(BoardOob::class, 'bor_meeting_id');
    }

    public function getHasOrderOfBusinessAttribute()
    {
        return $this->orderOfBusiness()->exists();
    }

    // GET MEETING LEVEL 
    public function getMeetingLevel()
    {
        return 'BOR';
    }

    // GET MEETING COUNCILTYPE 
    public function getMeetingCouncilType()
    {
        return 2;
    }

    // Relationship with Venue
    public function venue()
    {
        return $this->belongsTo(Venues::class, 'venue_id');
    }

    // GET PROPOSAL DETAILS
    public function proposals()
    {
        return $this->hasManyThrough(
            Proposal::class, 
            BoardMeetingAgenda::class, 
            'bor_meeting_id', 
            'id', 
            'id', 
            'board_proposal_id'
        )->with('proponents');
    }

    // Function to get the campus name
    public function getCampusName()
    {
        return 'All Campuses';
    }
}
