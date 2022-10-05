<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
// use App\Services\FCMService;
use Auth;
use Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function test()
    {
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

        // return (new FCMService())->sendFCM(1, 'Tâche accordée', 'Une nouvelle tâche vous a été accordée');
    }

    public function config_mobile()
    {
        try {
            // $roles = Role::whereIn('establishment_id', auth()->user()->company->establishments->pluck('id'))->with('permissions')->get();
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

            $professional = Professional::where('email', request('email'))
                ->first();

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

    public function logout()
    {
        try {
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
