<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AcademicCouncilMembership;
use App\Models\AdministrativeCouncilMembership;
use App\Models\Employee;


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
      'venue',
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

  // GET PROPOSAL COUNT
  public function getProposalCount()
  {
      return $this->agendas()->count();
  }
  public function agendas()
  {
      return $this->hasMany(LocalMeetingAgenda::class, 'local_council_meeting_id');
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
          'local_council_meeting_id', // Foreign key on LocalMeetingAgenda table
          'id', // Foreign key on Proposal table
          'id', // Local key on LocalCouncilMeeting table
          'local_proposal_id' // Local key on LocalMeetingAgenda table
      )->with('proponents');
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

  public function creator()
  {
      return $this->belongsTo(Employee::class, 'creator_id');
  }

  public function councilMembers()
  {
    // Ensure meeting has a valid council_type
    if (is_null($this->council_type)) {
      return collect(); // Return empty collection if no council_type
    }

    $academicMembers = AcademicCouncilMembership::whereHas('employee')
        ->where('council_type', $this->council_type)
        ->with('employee')
        ->get();

    $adminMembers = AdministrativeCouncilMembership::whereHas('employee')
        ->where('council_type', $this->council_type)
        ->with('employee')
        ->get();

    return $academicMembers->merge($adminMembers)->map(function ($member) {
        return $member->employee;
    });
  }
}
