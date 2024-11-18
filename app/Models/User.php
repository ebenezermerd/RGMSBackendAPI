<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 'last_name','username', 'email', 'phone_number', 'password', 
        'organization', 'role_id', 'city', 'present_address', 
        'permanent_address', 'date_of_birth', 'bio', 'profile_image','email_verified',  'verification_code', 'verification_code_expires_at'
   
    ];

    // Define the relationship for messages sent by the user
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Define the relationship for messages received by the user
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }


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
    return $this->hasOne(UserCoeAssignment::class);
}

public function coeClass()
{
    return $this->hasOneThrough(CoeClass::class, UserCoeAssignment::class, 'user_id', 'id', 'id', 'coe_class_id');
}

public function proposalsAssigned()
{
    return $this->belongsToMany(Proposal::class, 'user_proposal_assignments', 'reviewer_id', 'proposal_id')
                ->wherePivot('request_status', 'accepted') // Ensure the request status is 'accepted'
                ->withTimestamps() // Using the custom pivot table
                ->with(['phases', 'phases.activities', 'reviews']);
}
public function activities(){
    return $this->hasMany(ActivityHistory::class);
}

public function reviewerStatus()
{
    return $this->hasOne(ReviewerStatus::class, 'reviewer_id');
}

public function complaints(){
    return $this->hasMany(Complaint::class);
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

     /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
    
}
