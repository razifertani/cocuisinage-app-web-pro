<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;

// use App\Mail\SendInvitationLinkMail;
// use App\Models\Invitation;
// use Carbon\Carbon;
// use Hash;
// use Mail;
// use Spatie\Permission\Models\Role;

class CollegueController extends Controller
{

    public function toggle_permission()
    {
        try {
            request()->validate([
                'collegue_id' => 'required',
                'establishment_id' => 'required',
                'permission_id' => 'required',
            ]);

            $collegue = Professional::with('establishments_roles')->findOrFail(request('collegue_id'));

            $auth_user_is_not_owner = auth()->user()->establishments_roles->firstWhere('id', request('establishment_id'))->pivot->role_id != 1;
            $auth_user_have_not_manage_roles_permission = auth()->user()->establishments_permissions->where('id', request('establishment_id'))->where('permission_id', 5)->count() > 0;
            if ($auth_user_is_not_owner && $auth_user_have_not_manage_roles_permission) {
                return response()->json([
                    'error' => true,
                    'message' => 'Vous n\'avez pas la permission !',
                ], 401);
            }

            if ($collegue->establishments_roles->firstWhere('id', request('establishment_id'))->pivot->role_id == 1) {
                return response()->json([
                    'error' => true,
                    'message' => 'Le patron doit avoir toutes les permissions !',
                ], 401);
            }

            if ($collegue->permissions->contains(request('permission_id'))) {
                $collegue->permissions()->detach(
                    request('permission_id'),
                    [
                        'establishment_id' => request('establishment_id'),
                    ],
                );
            } else {
                $collegue->permissions()->attach(
                    request('permission_id'),
                    [
                        'establishment_id' => request('establishment_id'),
                    ],
                );
            }

            return response()->json([
                'error' => false,
                'message' => 'Permission modifiée avec succès !',
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
