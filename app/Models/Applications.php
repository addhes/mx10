<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Assuming User model is in App\Models

class Applications extends Model
{
    protected $table = "applications";

    protected $fillable = [
        'freelancer_id',
        'job_id',
        'cv',
    ];

    /**
     * Relasi ke User (sebagai freelancer)
     * freelancer_id adalah foreign key yang merujuk ke users.id
     */
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id', 'id');
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }
}
