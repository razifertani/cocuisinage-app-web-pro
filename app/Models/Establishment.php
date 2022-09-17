<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{

    protected $fillable = ["company_id", "name", "zip_code", "type", "city", "created_at", "updated_at"];

    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_roles_in_establishment');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function target_plannings()
    {
        return $this->hasMany(Planning::class)->where('is_boss', true);
    }

    public function worked_plannings()
    {
        return $this->hasMany(Planning::class)->where('is_boss', '=', false);
    }
}
