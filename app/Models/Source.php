<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'base_url'];

    /**
     * Get job URLs associated with this source.
     */
    public function jobUrls()
    {
        return $this->hasMany(JobUrl::class);
    }

    /**
     * Get jobs associated with this source.
     */
    public function jobs()
    {
        return $this->hasMany(NhsEnglandJob::class);
    }
}