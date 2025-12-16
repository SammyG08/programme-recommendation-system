<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreSubject extends Model
{
    use HasFactory;
    protected $fillable = ['mathematics', 'english', 'science', 'social'];
}
