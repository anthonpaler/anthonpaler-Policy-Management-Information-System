<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupProposal extends Model
{
  use HasFactory;

  use SoftDeletes;
  protected $table = 'group_proposals';

  protected $fillable = [
    'group_title',
    'order_no',
  ];

  // GET GROUP PROPOSAL FILES
  public function files()
  {
    return $this->hasMany(GroupProposalFiles::class, 'group_proposal_id')->orderBy('order_no');
  }
}
