<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeachingJob extends Model
{
    /** @use HasFactory<\Database\Factories\TeachingJobFactory> */
    use HasFactory; use SoftDeletes;

    protected $table = 'teaching_jobs';

    protected $primaryKey = 'job_id';
    public $incrementing = false; // If job_id is not auto-incrementing
    protected $keyType = 'string'; // If job_id is a string
   
    protected $fillable = [
        'job_id',
        'job_link',
        'job_title',
        'reference_number',
        'posted_by',
        'town',
        'post_code',
        'is_scraped',
        'posted_date',
        'closing_date',
        'subject',
        'education_phase',
        'age_range',
        'school_size',
        'school_type',
        'contract_type',
        'contact_phone',
        'contact_email',
        'keyword_checked',
        'post_code_validated',
        'region',
        'country',
        'nuts',
        'pfa',
        'latitude',
        'longitude',
    ];

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }
    
    public function keyword()
    {
        return $this->belongsTo(Keyword::class, 'keyword_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}