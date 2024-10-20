<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 'last_name','username', 'email', 'phone_number', 'password', 
        'organization', 'role_id', 'city', 'present_address', 
        'permanent_address', 'date_of_birth', 'bio', 'profile_image'
   
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function research()
    {
        return $this->hasMany(Research::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function posts(){
        return $this->hasMany(Post::class);
    }
    public function reports(){
        return $this->hasMany(Report::class);
    }
    public function coeAssignments()
{
    return $this->hasMany(UserCoeAssignment::class);
}

public function coeclasses()
{
    return $this->belongsToMany(CoeClass::class, 'user_coe_assignments', 'user_id', 'coe_class_id');
}

public function proposalsAssigned()
{
    return $this->belongsToMany(Proposal::class, 'user_proposal_assignments', 'reviewer_id', 'proposal_id')
                ->withTimestamps() // Using the custom pivot table
                ->with(['phases', 'phases.activities', 'reviews']);
}
public function activities(){
    return $this->hasMany(ActivityHistory::class);
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
}
