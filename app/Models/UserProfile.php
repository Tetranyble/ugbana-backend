<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'education',
        'job_experience',
        'skills',
        'user_id',
    ];

    protected $casts = [
        'education' => 'array',
        'job_experience' => 'array',
        'skills' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
