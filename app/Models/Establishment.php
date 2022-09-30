<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{

    protected $fillable = ["company_id", "name", "zip_code", "type", "city", "created_at", "updated_at"];

    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_roles_in_establishment')->withPivot('role_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class)->orderBy('should_start_at');
    }
}
