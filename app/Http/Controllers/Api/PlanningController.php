<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use Carbon\Carbon;

class PlanningController extends Controller
{
    public function store()
    {
        try {
            request()->validate([
                'professional_id' => 'required',
                'establishment_id' => 'required',
                'day' => 'required|date_format:Y-m-d',
                'should_start_at' => 'sometimes|date_format:H:i',
                'should_finish_at' => 'sometimes|date_format:H:i|after_or_equal:should_start_at',
                'started_at' => 'sometimes|date_format:H:i',
                'finished_at' => 'sometimes|date_format:H:i|after_or_equal:started_at',
            ], [
                'should_finish_at.after_or_equal' => 'Vérifier les horaires que vous avez entré !',
                'finished_at.after_or_equal' => 'Vérifier les horaires que vous avez entré !',
            ]);

            $planning = new Planning([
                'professional_id' => request('professional_id'),
                'establishment_id' => request('establishment_id'),
                'day' => Carbon::parse(request('day')),
                'should_start_at' => request('should_start_at'),
                'should_finish_at' => request('should_finish_at'),
                'started_at' => request('started_at'),
                'finished_at' => request('finished_at'),
            ]);

            if (request('monthly') != "1") {
                if ($planning->check_if_planning_can_be_added($planning->id, $planning->day)) {
                    Planning::create([
                        'professional_id' => request('professional_id'),
                        'establishment_id' => request('establishment_id'),
                        'day' => Carbon::parse(request('day')),
                        'should_start_at' => request('should_start_at'),
                        'should_finish_at' => request('should_finish_at'),
                    ]);
                }
            } else {
                $current_date = Carbon::parse($planning->day);
                $one_year_later = Carbon::now()->addYear(1);

                while ($current_date->isBefore($one_year_later)) {
                    if ($planning->check_if_planning_can_be_added($planning->id, $current_date)) {
                        Planning::create([
                            'professional_id' => request('professional_id'),
                            'establishment_id' => request('establishment_id'),
                            'day' => $current_date,
                            'should_start_at' => request('should_start_at'),
                            'should_finish_at' => request('should_finish_at'),
                        ]);
                    }

                    $current_date->addDays(7);
                }
            }

            return response()->json([
                'error' => false,
                'message' => 'Créneau créé avec succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function update($id)
    {
        try {
            request()->validate([
                'should_start_at' => 'sometimes|date_format:H:i',
                'should_finish_at' => 'sometimes|date_format:H:i|after_or_equal:should_start_at',
            ]);

            $planning = Planning::findOrFail($id);

            $planning->should_start_at = request('should_start_at');
            $planning->should_finish_at = request('should_finish_at');

            if ($planning->check_if_planning_can_be_added($planning->id, $planning->day)) {
                $planning->save();

            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Créneau ne peut pas être modifié !',
                ], 500);
            }

            return response()->json([
                'error' => false,
                'message' => 'Mise à jour effectuée avec succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function update_time($id)
    {
        try {
            request()->validate([
                'started_at' => 'required|date_format:H:i',
                'finished_at' => 'sometimes|date_format:H:i|after_or_equal:started_at',
            ]);

            $planning = Planning::findOrFail($id);

            $planning->started_at = request('started_at');
            $planning->finished_at = request('finished_at');
            if ($planning->should_finish_at == null) {
                $planning->should_finish_at = request('finished_at');
            }

            $planning->save();

            return response()->json([
                'error' => false,
                'message' => 'Créneau mis à jour avec succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
