<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Proposal extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'employee_id',
        'campus_id',
        'title',
        'action',
        'status',
        'type',
        'sub_type',
    ];

    public function files()
    {
        return $this->hasMany(ProposalFile::class, 'proposal_id');
    }

    public function proponents()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

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

        if ($inLocal && !$inUniversity) {
            return 0;
        } elseif ($inLocal && $inUniversity) {
            return 1;
        }
        elseif ($inLocal && $inUniversity && $inBoard) {
            return 2;
        }

        return 'Not Assigned';
    }


}
