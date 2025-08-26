<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sign_in_time'];
    
    protected $casts = [
        'sign_in_time' => 'datetime'
    ];
}