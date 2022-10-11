<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Task;
use App\Services\FCMService;

// use Mail;
// use App\Mail\SendToOwnerDeniedTaskMail;

class TaskController extends Controller
{
    public function store()
    {
        try {
            request()->validate([
                'professional_id' => 'required',
                'establishment_id' => 'required',
                'planning_id' => 'required',
                'name' => 'required',
                'status' => 'required',
            ]);

            $auth_user_can_add_tasks = auth()->user()->canManagePermission(request('establishment_id'), config('cocuisinage.permissions_ids.manage_tasks'));

            if (!$auth_user_can_add_tasks) {
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
            ]);

            (new FCMService())->sendFCM(request('establishment_id'), auth()->user()->id, request('professional_id'), config('cocuisinage.notifications_types.task'), 'Tâche accordée', 'Une nouvelle tâche vous a été accordée');

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
            request()->validate([
                'professional_id' => 'required',
                'establishment_id' => 'required',
            ]);

            if (request('status') == -1) {
                $professional = Professional::findOrFail(request('professional_id'));
                // Mail::to($professional->email)->send(new SendToOwnerDeniedTaskMail("Message"));
            }

            $task = Task::findOrFail($id);

            $task->status = request('status') ?? $task->status;
            $task->comment = request('comment') ?? $task->comment;

            if (request()->hasFile('image')) {
                $task->image = $this->upload_image(auth()->user()->id);
            }

            $task->save();

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
