<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobUrl extends Model
{
    use HasFactory;

    protected $fillable = ['keyword_id', 'url', 'page', 'is_scraped'];

    /**
     * Get the keyword this job URL belongs to.
     */
    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }
    
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}