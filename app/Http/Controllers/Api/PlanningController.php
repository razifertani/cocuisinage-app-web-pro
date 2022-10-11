<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PlanningCannotBeAdded;
use App\Http\Controllers\Controller;
use App\Models\Planning;
use DB;
use Validator;

class PlanningController extends Controller
{
    public function store_plannings()
    {
        try {
            $plannings = json_decode(request('plannings'), true);

            $validator = Validator::make($plannings, [
                '*.professional_id' => 'required',
                '*.establishment_id' => 'required',
                '*.day' => 'required|date_format:Y-m-d',
                '*.should_start_at' => 'required|date_format:H:i',
                '*.should_finish_at' => 'required|date_format:H:i|after_or_equal:*.should_start_at',
            ], [
                '*.should_finish_at.after_or_equal' => 'Vérifier les horaires que vous avez entré !',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ], 500);
            }

            DB::beginTransaction();

            foreach ($plannings as $planning) {
                $planning = new Planning($planning);

                if (request('monthly') == "1") {
                    $response = $planning->create_or_update_recursivly();
                } else {
                    $response = $planning->create_or_update_once();
                }

                if ($response instanceof PlanningCannotBeAdded) {
                    throw new PlanningCannotBeAdded();
                }
            }

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => 'Créneau(x) créé(s) avec succès !',
            ], 200);

        } catch (PlanningCannotBeAdded $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => 'Créneau déjà ajouté à ce moment là !',
            ], 400);

        } catch (\Throwable$th) {
            DB::rollback();
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
