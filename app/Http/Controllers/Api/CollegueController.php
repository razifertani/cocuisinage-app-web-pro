<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Professional;
use Carbon\Carbon;
use Hash;
use Spatie\Permission\Models\Role;

// use App\Mail\SendInvitationLinkMail;
// use Mail;

class CollegueController extends Controller
{
    public function invite_collegue()
    {
        try {
            request()->validate([
                'company_id' => 'required',
                'establishment_id' => 'required',
                'email' => 'required|unique:professionals|unique:invitations',
                'first_name' => 'required',
                'last_name' => 'required',
                'phone_number' => 'required',
                'role_id' => 'required',
            ]);

            $invitation_token = base64_encode(json_encode([
                'company_id' => request('company_id'),
                'establishment_id' => request('establishment_id'),
                'first_name' => request('first_name'),
                'last_name' => request('last_name'),
                'phone_number' => request('phone_number'),
                'email' => request('email'),
                'role_id' => request('role_id'),
            ]));

            $url_token = md5($invitation_token);

            $invitation = Invitation::firstOrCreate(
                [
                    'email' => request('email'),
                ],
                [
                    'email' => request('email'),
                    'invitation_token' => $invitation_token,
                    'url_token' => $url_token,
                    'registered_at' => null,
                ]
            );

            $url = url('/api/collegue/accept_invitation') . '/' . $url_token;
            // Mail::to(request('email'))->send(new SendInvitationLinkMail($url));

            return response()->json([
                'error' => false,
                'message' => 'Invitation envoyée avec succès !',
            ], 200);
        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function accept_collegue_invitation($url_token)
    {
        try {
            request()->validate([
                'password' => 'required',
            ]);

            $invitation = Invitation::where('url_token', $url_token)->first();

            if ($invitation == null) {
                return response()->json([
                    'error' => true,
                    'message' => 'Lien invalide !',
                ], 401);
            }
            if ($invitation->registered_at != null) {
                return response()->json([
                    'error' => true,
                    'message' => 'Compte déjà créé, veuillez vous connectez !',
                ], 401);
            }

            $invitation_token = base64_decode($invitation->invitation_token);
            $params = json_decode($invitation_token, true);

            $collegue = Professional::create([
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name'],
                'email' => $params['email'],
                'password' => Hash::make(request('password')),
                'company_id' => $params['company_id'],
            ]);

            $collegue->establishments_roles()->attach(
                $params['establishment_id'],
                [
                    'role_id' => $params['role_id'],
                ],
            );
            $collegue->permissions()->attach(
                Role::findOrFail($params['role_id'])->permissions,
                [
                    'establishment_id' => $params['establishment_id'],
                ],
            );

            $invitation->registered_at = Carbon::now();
            $invitation->save();

            return response()->json([
                'error' => false,
                'message' => 'Collègue créé avec succès !',
            ], 200);
        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

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
