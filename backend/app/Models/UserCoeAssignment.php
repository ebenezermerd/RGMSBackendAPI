<?php

// app/Models/UserCoeAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoeAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'coe_class_id'];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the COE class associated with this assignment.
     */
    public function coeClass()
    {
        return $this->belongsTo(CoeClass::class);
    }
}
