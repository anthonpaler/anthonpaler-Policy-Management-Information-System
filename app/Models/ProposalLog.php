<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProposalLog extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'proposal_logs';

    protected $fillable = [
        'proposal_id',
        'employee_id',
        'comments',
        'status',
        'level',
        'action',
        'file_id',
    ];

    /**
     * Get the proposal associated with the log.
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
