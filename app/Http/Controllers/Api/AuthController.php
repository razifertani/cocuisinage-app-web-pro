<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Establishment;
use App\Models\Professional;
use Auth;
use Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function test()
    {

        return Establishment::with('reservations.table')->with('tables.reservations')->get();

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
