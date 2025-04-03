<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;


class Proposal extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'campus_id',
        'title',
        'action',
        'status',
        'type',
        'sub_type',
    ];

    // GET PROPOSAL FILES
    public function files()
    {
        return $this->hasMany(ProposalFile::class, 'proposal_id')->orderBy('order_no');
    }

    // GET PROPOSAL PROPONENTS
    public function proponents() {
        return $this->belongsToMany(User::class, 'proposal_proponents', 'proposal_id', 'employee_id',
        'id',
        'employee_id');
    }

    // DETERMINE IF PROPOSAL IS OTHER MATTER
    public function isOtherMatter()
    {
        return $this->hasOne(OtherMatter::class, 'proposal_id')->exists();
    }
    // GET CURRENT LEVEL OF THE PROPOSAL
    public function localAgendas()
    {
        return $this->hasMany(LocalMeetingAgenda::class, 'local_proposal_id');
    }

    public function universityAgendas()
    {
        return $this->hasMany(UniversityMeetingAgenda::class, 'university_proposal_id');
    }

    public function boardAgendas()
    {
        return $this->hasMany(BoardMeetingAgenda::class, 'board_proposal_id');
    }

    public function getCurrentLevelAttribute()
    {
        $inLocal = $this->localAgendas()->exists();
        $inUniversity = $this->universityAgendas()->exists();
        $inBoard = $this->boardAgendas()->exists();

        if ($inBoard) {
            return 2;
        } elseif ($inUniversity) {
            return 1;
        } elseif ($inLocal) {
            return 0;
        }

        return 'Not Assigned';
    }

    // GETTING MEETING INFO
    public function localMeeting()
    {
        return $this->hasOneThrough(
            LocalCouncilMeeting::class,
            LocalMeetingAgenda::class,
            'local_proposal_id', // Foreign key on LocalMeetingAgenda
            'id', // Primary key on LocalCouncilMeeting
            'id', // Primary key on Proposal
            'local_council_meeting_id' // Foreign key on LocalCouncilMeeting
        );
    }

    public function universityMeeting()
    {
        return $this->hasOneThrough(
            UniversityCouncilMeeting::class,
            UniversityMeetingAgenda::class,
            'university_proposal_id',
            'id',
            'id',
            'university_meeting_id'
        );
    }

    public function boardMeeting()
    {
        return $this->hasOneThrough(
            BorMeeting::class,
            BoardMeetingAgenda::class,
            'board_proposal_id',
            'id',
            'id',
            'bor_meeting_id'
        );
    }

    public function getMeetingAttribute()
    {
        if ($this->getCurrentLevelAttribute() == 0) {
            return $this->localMeeting;
        } elseif ($this->getCurrentLevelAttribute() == 1) {
            return $this->universityMeeting;
        } elseif ($this->getCurrentLevelAttribute() == 2) {
            return $this->boardMeeting;
        }
        return null;
    }

    // GET PROPOSAL COUNT IN EACH LEVEL
    public function scopeProposalsCountByEmployeeInLevel($query, $employeeId): array
    {
        return [
            'local' => $query->whereHas('proponents', function ($q) use ($employeeId) {
                $q->where('users.employee_id', $employeeId);
            })->whereHas('localAgendas')->count(),

            'university' => $query->whereHas('proponents', function ($q) use ($employeeId) {
                $q->where('users.employee_id', $employeeId);
            })->whereHas('universityAgendas')->count(),

            'board' => $query->whereHas('proponents', function ($q) use ($employeeId) {
                $q->where('users.employee_id', $employeeId);
            })->whereHas('boardAgendas')->count(),
        ];
    }

    // DETERMINE IF THE PROPOSAL IS EDITABLE

    public function getIsEditableAttribute()
    {
        $editableStatuses = [0, 2, 5, 6];

        // Check if the proposal's status is editable
        $isStatusEditable = in_array($this->status, $editableStatuses);

        // Check if the local submission period is ongoing
        $isSubmissionOngoing = $this->localMeeting &&
            Carbon::now()->between($this->localMeeting->submission_start, $this->localMeeting->submission_end);

        return $isStatusEditable || $isSubmissionOngoing;
    }

}
