<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    protected $fillable = ['grade', 'value'];

    public function programme()
    {
        return $this->hasMany(Programme::class, 'lowest_grade_for_cores');
    }
}
