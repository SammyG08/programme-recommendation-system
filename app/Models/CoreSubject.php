<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreSubject extends Model
{
    use HasFactory;
    protected $fillable = ['mathematics', 'english', 'science', 'social'];

    public function programme()
    {
        return $this->hasOne(Programme::class);
    }

    private function getCores()
    {
        $cores = [
            'Mathematics' => $this->mathematics,
            'English' => $this->english,
            'Science' => $this->science,
            'Social Studies' => $this->social,
        ];

        $requiredCores = [];
        foreach ($cores as $subject => $value) {
            if ($value === 'required') {
                $requiredCores[] = $subject;
            }
        }


        return $requiredCores;
    }

    public function coreSubjects($programmeName)
    {
        $requiredCores = $this->getCores();
        $cores = CoreSubject::whereHas('programme', function ($query) use ($programmeName) {
            $query->where('programme_name', $programmeName);
        })->get();

        if ($cores->count() > 1) {
            foreach ($cores as $core) {
                $coreSubjects = $core->getCores();
                foreach ($coreSubjects as $subject) {
                    if (!in_array($subject, $requiredCores)) {
                        $requiredCores[] = $subject;
                    }
                }
            }
        }
        return $requiredCores;
    }
}
