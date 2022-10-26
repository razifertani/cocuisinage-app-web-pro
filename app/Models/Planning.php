<?php

namespace App\Models;

use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    protected $fillable = [
        "id",
        "professional_id",
        "establishment_id",
        "day",
        "should_start_at",
        "should_finish_at",
        "started_at",
        "finished_at",
    ];

    protected $dates = [
        "day",
    ];

    protected $appends = [
        'status',
    ];

    public function getStatusAttribute()
    {
        if ($this->started_at == null && $this->finished_at == null) {
            return 0;
        } else if ($this->started_at != null && $this->finished_at == null) {
            return 1;
        } else if ($this->started_at != null && $this->finished_at != null) {
            return 2;
        }
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function check_if_planning_can_be_added($id, $day)
    {
        $plannings = Planning::where([
            ['professional_id', $this->professional_id],
            ['establishment_id', $this->establishment_id],
            ['day', $day],
        ])->get();

        foreach ($plannings as $planning) {
            if ($id != $planning->id && $this->should_finish_at != null) {
                if (
                    Carbon::createFromTimeString($this->should_start_at)->between(Carbon::createFromTimeString($planning->should_start_at), Carbon::createFromTimeString($planning->should_finish_at))
                    ||
                    Carbon::createFromTimeString($this->should_finish_at)->between(Carbon::createFromTimeString($planning->should_start_at), Carbon::createFromTimeString($planning->should_finish_at))
                    ||
                    (
                        !(Carbon::createFromTimeString($this->should_start_at)->isAfter(Carbon::createFromTimeString($planning->should_finish_at)) && Carbon::createFromTimeString($this->should_finish_at)->isAfter(Carbon::createFromTimeString($planning->should_finish_at)))
                        &&
                        !(Carbon::createFromTimeString($this->should_start_at)->isBefore(Carbon::createFromTimeString($planning->should_start_at)) && Carbon::createFromTimeString($this->should_finish_at)->isBefore(Carbon::createFromTimeString($planning->should_start_at)))
                    )
                ) {
                    return false;
                }
            }
        }
        return true;
    }
}
