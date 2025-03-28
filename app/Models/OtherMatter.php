<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherMatter extends Model
{
    protected $fillable = [
        'proposal_id',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }
}
