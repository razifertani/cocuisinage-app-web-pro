<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use Validator;

class PlanningController extends Controller
{
    public function add_or_update()
    {
        try {
            $validator = Validator::make(request()->all(),
                [
                    'professional_id' => 'required',
                    'establishment_id' => 'required',
                    'day' => 'required|date_format:Y-m-d',
                    'start_at' => 'required|date_format:H:i',
                    'stop_at' => 'sometimes|date_format:H:i|after_or_equal:start_at',
                    'is_boss' => 'required',
                ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ], 401);
            }

            if (request('is_boss') === true) {

                $planning = Planning::create([
                    'professional_id' => request()->user()->id,
                    'establishment_id' => request('establishment_id'),
                    'day' => request('day'),
                    'start_at' => request('start_at'),
                    'stop_at' => request('stop_at'),
                    'is_boss' => request('is_boss'),
                    'day_of_week' => request('day_of_week'),
                ]);

            } else {
                $planning = Planning::where([
                    ['professional_id', request('professional_id')],
                    ['establishment_id', request('establishment_id')],
                    ['day', request('day')],
                    ['is_boss', request('is_boss')],
                ])
                    ->whereNull('stop_at')
                    ->first();

                if ($planning) {
                    $planning->update([
                        'stop_at' => request('stop_at'),
                    ]);
                } else {

                    $planning = Planning::create([
                        'professional_id' => request('professional_id'),
                        'establishment_id' => request('establishment_id'),
                        'day' => request('day'),
                        'start_at' => request('start_at'),
                        'day_of_week' => request('day_of_week'),
                        'is_boss' => request('is_boss'),
                    ]);
                }
            }

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
