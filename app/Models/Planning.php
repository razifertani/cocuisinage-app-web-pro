<?php

namespace App\Models;

use App\Exceptions\PlanningCannotBeAdded;
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
            if ($id != $planning->id) {
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

    public function create_or_update_once()
    {
        try {

            if ($this->check_if_planning_can_be_added($this->id, $this->day)) {

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
        } catch (\Throwable$th) {
            report($th);
            return new PlanningCannotBeAdded();
        }
    }

    public function create_or_update_recursivly()
    {
        try {

            $current_date = Carbon::parse($this->day);
            $one_year_later = Carbon::now()->addYear(1);

            while ($current_date->isBefore($one_year_later)) {

                $planning = Planning::where([
                    ['professional_id', $this->professional_id],
                    ['establishment_id', $this->establishment_id],
                    ['day', $current_date],
                ])->where(function ($query) {
                    $query->where('should_start_at', $this->should_start_at)
                        ->orWhere('should_finish_at', $this->should_finish_at);
                })->first();

                if ($planning == null) {

                    if ($this->check_if_planning_can_be_added($this->id, $current_date)) {
                        Planning::create([
                            'professional_id' => $this->professional_id,
                            'establishment_id' => $this->establishment_id,
                            'day' => $current_date,
                            'should_start_at' => $this->should_start_at,
                            'should_finish_at' => $this->should_finish_at,
                        ]);
                    } else {
                        return new PlanningCannotBeAdded();
                    }

                } else {

                    if ($this->check_if_planning_can_be_added($planning->id, $current_date)) {
                        Planning::where('id', $planning->id)->update([
                            'should_start_at' => $this->should_start_at,
                            'should_finish_at' => $this->should_finish_at,
                        ]);
                    } else {
                        return new PlanningCannotBeAdded();
                    }
                }

                $current_date->addDays(7);
            }

        } catch (\Throwable$th) {
            report($th);
            return new PlanningCannotBeAdded();
        }
    }
}
