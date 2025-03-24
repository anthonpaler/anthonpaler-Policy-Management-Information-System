<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalProponent extends Model
{
    use HasFactory;

    // use SoftDeletes;

    protected $table = 'proposal_proponents';
    protected $fillable = [
        'proposal_id',
        'employee_id',
    ];

    public function proposal() {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }
    
    public function proponents()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
        
}
