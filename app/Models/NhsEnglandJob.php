<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class NhsEnglandJob extends Model
{
    use HasFactory; use SoftDeletes;

    protected $table = 'nhs_england_jobs';

    protected $primaryKey = 'job_id';
    public $incrementing = false; // If job_id is not auto-incrementing
    protected $keyType = 'string'; // If job_id is a string

    protected $fillable = [
        'job_id',
        'job_link',
        'reference_number',
        'trust',
        'band',
        'contract_type',
        'contact_job_title',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_line_1',
        'address_line_2',
        'town',
        'postcode',
        'post_code',
        'longitude',
        'latitude',
        'post_code_validated',
        'keyword_id',
        'profession_id',
        'keyword_checked',
        'source_id',
        'profession_id',
        'job_title',
        'website_url',
        'posted_date',
        'closing_date',
        'is_scraped',
        'region',
        'ccg',
        'post_code_validated',
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