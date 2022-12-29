<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Professional;
use Hash;
use Illuminate\Validation\Rule;

class ProfessionalController extends Controller
{
    public function user()
    {
        try {
            $user = request()->user();

            $user->load([
                'establishments_roles',
                'establishments_permissions',
                'company.establishments.professionals.establishments_roles',
                'company.establishments.professionals.establishments_permissions',
                'company.establishments.roles',
                'company.establishments.reservations',
                'company.establishments.schedules',

                'plannings.tasks',
                'company.establishments.professionals.plannings.tasks',

                'tasks',
                'notifications_params',
                'notifications_as_sender',
                'notifications_as_receiver',

                'company.establishments.reservations.table',
                'company.establishments.tables.reservations',
            ]);

            return response()->json([
                'error' => false,
                'data' => $user,
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
            $user = Professional::with('roles')->findOrFail($id);

            request()->validate([
                'establishment_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => ['required', Rule::unique('professional')->ignore($user)],
            ]);

            if (request()->hasFile('image')) {
                $user->profile_photo_path = $this->upload_image(auth()->user()->id);
            }

            $user->first_name = request('first_name');
            $user->last_name = request('last_name');
            $user->email = request('email');
            $user->phone_number = request('phone_number');
            $user->fcm_token = request('fcm_token');

            /*
            if (request()->has('role_id')) {
            foreach ($user->roles as $role) {
            if ($role->pivot->establishment_id == request('establishment_id') && $role->pivot->role_id != request('role_id')) {
            $user->establishments_roles()->detach(
            request('establishment_id'),
            [
            'role_id' => $role->pivot->role_id,
            ],
            );
            $user->establishments_roles()->attach(
            request('establishment_id'),
            [
            'role_id' => request('role_id'),
            ],
            );
            }
            }
            }
             */

            if (request()->has('new_password')) {
                if (!Hash::check(request('password'), $user->password)) {
                    return response()->json([
                        'error' => true,
                        'message' => "Veuillez vérifier le mot de passe actuel !",
                    ], 401);
                } else {
                    $user->password = Hash::make(request('new_password'));
                }
            }

            $user->save();

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

    public function delete($id)
    {
        try {

            $professional = Professional::findOrFail($id);
            Invitation::where('email', $professional->email)->destroy($id);
            $professional->delete();

            return response()->json([
                'error' => false,
                'message' => 'Professionnel supprimé avec succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function toggle_notification_type_active_param($id)
    {
        try {
            request()->validate([
                'establishment_id' => 'required',
                'notification_type_id' => 'required',
            ]);

            $professional = Professional::with('roles')->findOrFail($id);

            $professional->toggle_notification_type_active_param(request('establishment_id'), request('notification_type_id'));

            return response()->json([
                'error' => false,
                'message' => 'Paramètre mis à jour effectuée avec succès !',
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
