<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UniversityCouncilMeeting extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'university_council_meetings';
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

    // DETERMINE IF THE MEETING EXIST IN UNIVERSITY OOB
    public function orderOfBusiness()
    {
        return $this->hasOne(UniversityOob::class, 'university_council_meeting_id');
    }

    public function getHasOrderOfBusinessAttribute()
    {
        return $this->orderOfBusiness()->exists();
    }

    // GET MEETING LEVEL 
    public function getMeetingLevel()
    {
        return 'University';
    }
    // GET MEETING COUNCILTYPE 
    public function getMeetingCouncilType()
    {
        return 1;
    }

    // Relationship with Venue
    public function venue()
    {
        return $this->belongsTo(Venues::class, 'venue_id');
    }
}
