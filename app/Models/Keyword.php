<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = ['profession_id', 'keyword', 'status'];

    /**
     * Get the profession this keyword belongs to.
     */
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Get the job URLs associated with the keyword.
     */
    public function jobUrls()
    {
        return $this->hasMany(JobUrl::class);
    }

    /**
     * Get the jobs associated with the keyword.
     */
    public function jobs()
    {
        return $this->hasMany(NhsEnglandJob::class);
    }
}