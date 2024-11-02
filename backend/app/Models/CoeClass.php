<?php
// app/Models/CoeClass.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoeClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Define the relationship with UserCoeAssignment
    public function userCoeAssignments()
    {
        return $this->hasOne(UserCoeAssignment::class);
    }
    // app/Models/CoeClass.php

    public function user()
    {
        return $this->hasOneThrough(User::class, UserCoeAssignment::class, 'coe_class_id', 'id', 'id', 'user_id');
    }
    

}
