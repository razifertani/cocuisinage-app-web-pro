<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Spatie\Permission\Models\Permission;
use Validator;

class TaskController extends Controller
{
    public function store()
    {

        try {
            $validator = Validator::make(request()->all(),
                [
                    'professional_id' => 'required',
                    'establishment_id' => 'required',
                    'planning_id' => 'required',
                    'name' => 'required',
                    'status' => 'required',
                ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ], 401);
            }

            if (!auth()->user()->can(Permission::find(4)->name)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Permission non accordé !',
                ], 401);
            }

            $task = Task::create([
                'professional_id' => request('professional_id'),
                'establishment_id' => request('establishment_id'),
                'planning_id' => request('planning_id'),
                'name' => request('name'),
                'status' => request('status'),
                'comment' => request('comment'),
                'image_link' => request('image_link'),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Tâche créé avec succès !',
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
            $validator = Validator::make(request()->all(),
                [
                    'professional_id' => 'required',
                    'establishment_id' => 'required',
                ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ], 401);
            }

            Task::where('id', $id)
                ->update([
                    'status' => request('status'),
                    'comment' => request('comment'),
                    'image_link' => request('image_link'),
                ]);

            return response()->json([
                'error' => false,
                'message' => 'Tâche mis à jour avec succès !',
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
