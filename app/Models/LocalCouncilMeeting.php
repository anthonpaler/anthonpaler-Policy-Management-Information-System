<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LocalCouncilMeeting extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'local_council_meetings';

    protected $fillable = [
        'creator_id',
        'campus_id',
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

    public function getIsSubmissionClosedAttribute()
    {
        $currentDate = Carbon::now();
    
        if (!$this->submission_end || !$this->submission_start || !$this->meeting_date_time) {
            return false; 
        }
    
        return $currentDate->greaterThan($this->submission_end) || 
               $currentDate->lessThan($this->submission_start) ||  
               $currentDate->greaterThan($this->meeting_date_time);
    }

    // DETERMINE IF THE MEETING EXIST IN LOCAL OOB
    public function orderOfBusiness()
    {
        return $this->hasOne(LocalOob::class, 'local_council_meeting_id');
    }

    public function getHasOrderOfBusinessAttribute()
    {
        return $this->orderOfBusiness()->exists();
    }

    // GET MEETING LEVEL 
    public function getMeetingLevel()
    {
        return 'Local';
    }

    // GET MEETING COUNCILTYPE 
    public function getMeetingCouncilType()
    {
        return 0;
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
            LocalMeetingAgenda::class, 
            'local_council_meeting_id', 
            'id', 
            'id', 
            'local_proposal_id'
        );
    }

    // Define relationship to Campus
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    // Function to get the campus name
    public function getCampusName()
    {
        return $this->campus ? $this->campus->name : 'N/A';
    }
}
