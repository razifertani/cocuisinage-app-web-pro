<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendResetPasswordTokenMail;
use App\Models\Company;
use App\Models\Planning;
use App\Models\Professional;
use App\Services\FCMService;
use Auth;
use Hash;
use Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function test()
    {
        Mail::to('razifertani1@gmail.com')->send(new SendResetPasswordTokenMail(1234));
        return 1;

        return (new FCMService())->sendFCM(1, 2, 1, config('cocuisinage.notifications_types.planning'), 'Tâche accordée', 'Une nouvelle tâche vous a été accordée');

        $planning4 = Planning::create([
            'professional_id' => 2,
            'establishment_id' => 1,
            'day' => '2022-11-04',
            'should_start_at' => '10:00',
            'should_finish_at' => '12:00',
        ]);

        return true;

        Professional::findOrFail(6)->toggle_notification_type_active_param(1, 3);
        return true;

        return config('cocuisinage.notifications_types.planning');

        return Professional::findOrFail(1)
            ->establishments_permissions()
            ->where('establishment_id', 1)
            ->where('permission_id', 2)
            ->count() > 0;

        // return Professional::findOrFail(1)->establishments_permissions()
        //     ->where('establishment_id', 1) // request('establishment_id')
        //     ->where('permission_id', 3) // config('cocuisinage.permissions_ids.manage_permissions')
        //     ->count();

        return (new FCMService())->sendFCM(1, 1, 6, config('cocuisinage.notifications_types.planning'), 'Tâche accordée', 'Une nouvelle tâche vous a été accordée');

        $professional = Professional::firstOrCreate(
            [
                'email' => 'hamedemploye@gmail.com',
            ],
            [
                'first_name' => 'Hamed',
                'last_name' => 'Employe',
                'email' => 'hamedemploye@gmail.com',
                'password' => Hash::make('123456'),
                'company_id' => 1,
            ]
        );
        $professional->establishments_roles()->attach(
            5,
            [
                'role_id' => 3,
            ],
        );
        $professional->permissions()->attach(
            Role::findOrFail(3)->permissions,
            [
                'establishment_id' => 5,
            ],
        );

    }

    public function config_mobile()
    {
        try {
            $roles = Role::all();
            $permissions = Permission::all();

            return response()->json([
                'error' => false,
                'roles' => $roles,
                'permissions' => $permissions,
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function login()
    {
        try {
            request()->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt(request()->only(['email', 'password']))) {
                return response()->json([
                    'error' => true,
                    'message' => 'Email & Mot de passe non valides !',
                ], 200);
            }

            $professional = Professional::where('email', request('email'))->first();

            return response()->json([
                'error' => false,
                'token' => $professional->createToken("API TOKEN")->plainTextToken,
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function register()
    {
        try {
            request()->validate([
                'owner_email' => 'required|unique:professional,email|unique:invitations,email',
                'owner_first_name' => 'required',
                'owner_last_name' => 'required',
                'owner_password' => 'required',

                'company_email' => 'required|unique:companies,email',
                'company_name' => 'required',
                'company_phone_number' => 'required',
                'company_rib' => 'required',
                'company_siret' => 'required',
            ]);

            $company = Company::create([
                'name' => request('company_name'),
                'email' => request('company_email'),
                'phone_number' => request('company_phone_number'),
                'rib' => request('company_rib'),
                'siret' => request('company_siret'),
            ]);

            $professional = Professional::create([
                'email' => request('owner_email'),
                'first_name' => request('owner_first_name'),
                'last_name' => request('owner_last_name'),
                'password' => Hash::make(request('owner_password')),
                'company_id' => $company->id,
                'is_owner' => 1,
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Compte créé avec succès, veuillez vous connecter !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        try {

            Professional::where('id', auth()->user()->id)->update([
                'fcm_token' => '',
            ]);

            auth()->user()->tokens()->delete();

            return response()->json([
                'error' => false,
                'message' => 'Déconnexion avec succès !',
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
