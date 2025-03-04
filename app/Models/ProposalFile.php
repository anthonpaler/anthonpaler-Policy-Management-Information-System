<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProposalFile extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'proposal_files';


    protected $fillable = [
        'proposal_id',
        'file',
        'version',
        'file_status',
        'file_reference_id',
        'is_active',
    ];
    
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }
}
