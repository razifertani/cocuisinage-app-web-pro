<?php

namespace App\Models;

use App\Exceptions\PlanningCannotBeAdded;
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
        "monthly",
    ];

    protected $dates = [
        "day",
    ];

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

    public function create_or_update()
    {
        if ($this->check_if_planning_can_be_added($this->day)) {

            if ($this->id == 0) {

                Planning::create([
                    'professional_id' => $this->professional_id,
                    'establishment_id' => $this->establishment_id,
                    'day' => Carbon::parse($this->day),
                    'should_start_at' => $this->should_start_at,
                    'should_finish_at' => $this->should_finish_at,
                ]);

            } else {

                Planning::where('id', $this->id)
                    ->update([
                        'should_start_at' => $this->should_start_at,
                        'should_finish_at' => $this->should_finish_at,
                    ]);
            }

            return true;
        } else {
            return new PlanningCannotBeAdded();
        }
    }

    public function check_if_planning_can_be_added($day)
    {
        $plannings = Planning::where([
            ['professional_id', $this->professional_id],
            ['establishment_id', $this->establishment_id],
            ['day', $day],
        ])->get();

        foreach ($plannings as $planning) {
            if ($this->id != $planning->id) {
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
