<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

// use App\Models\Company;
// use App\Models\Establishment;
use App\Models\Professional;
use Hash;

// use Auth;

use Illuminate\Validation\Rule;

// use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Models\Role;
// use Validator;

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

                'target_plannings', 'worked_plannings',
                'company.establishments.professionals.target_plannings.tasks',
                'company.establishments.professionals.worked_plannings',

                'tasks',
                // 'company.establishments.target_plannings',
                // 'company.establishments.worked_plannings',
                /* 'roles.permissions' */
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
            $user = Professional::with('roles')->findOrFail(auth()->user()->id);

            request()->validate([
                'establishment_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => ['required', Rule::unique('professionals')->ignore($user)],
                'phone_number' => 'sometimes',
            ]);

            $user->first_name = request('first_name');
            $user->last_name = request('last_name');
            $user->email = request('email');
            $user->phone_number = request('phone_number');

            // if (request()->has('role_id')) {
            //     foreach ($user->roles as $role) {
            //         if ($role->pivot->establishment_id == request('establishment_id') && $role->pivot->role_id != request('role_id')) {
            //             $user->establishments_roles()->detach(
            //                 request('establishment_id'),
            //                 [
            //                     'role_id' => $role->pivot->role_id,
            //                 ],
            //             );
            //             $user->establishments_roles()->attach(
            //                 request('establishment_id'),
            //                 [
            //                     'role_id' => request('role_id'),
            //                 ],
            //             );
            //         }
            //     }
            // }

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
}