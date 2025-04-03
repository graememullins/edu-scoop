<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profession extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the keywords associated with the profession.
     */
    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    public function jobs()
    {
        return $this->hasMany(NhsEnglandJob::class, 'profession_id');
    }

    public function group()
    {
        return $this->belongsTo(ProfessionGroup::class, 'profession_group_id');
    }
}