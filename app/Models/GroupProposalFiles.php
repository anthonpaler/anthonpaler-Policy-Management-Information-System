<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupProposalFiles extends Model
{
  use SoftDeletes;
  protected $table = 'group_proposal_files';

  protected $fillable = [
    'group_proposal_id',
    'file_name',
    'file',
    'order_no',
  ];
}
