<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAcceptance extends Model
{
    use HasFactory;

    protected $table = 'terms_acceptance';

    public $timestamps = false;

    protected $fillable = [
        'user_type',
        'user_id',
        'terms_version',
        'ip_address',
        'user_agent',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];
}