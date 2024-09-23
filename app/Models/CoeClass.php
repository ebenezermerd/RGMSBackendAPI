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
        return $this->hasMany(UserCoeAssignment::class);
    }
    // app/Models/CoeClass.php

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coe_assignments', 'coe_class_id', 'user_id');
    }
    

}
