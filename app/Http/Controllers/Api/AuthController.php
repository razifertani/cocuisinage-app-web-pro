<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;

class AuthController extends Controller
{
    public function test()
    {

    }

    public function config_mobile()
    {
        try {

            $roles = Role::with('permissions')->get();
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

    /*
    public function register()
    {
    try {
    $validateUser = Validator::make(request()->all(),
    [
    'first_name' => 'required',
    'last_name' => 'required',
    'email' => 'required|email|unique:professionals',
    'password' => 'required',
    'company_id' => 'required',
    ]);

    if ($validateUser->fails()) {
    return response()->json([
    'error' => true,
    'message' => $validator->errors()->first(),
    ], 401);
    }

    Professional::create([
    'first_name' => request('first_name'),
    'last_name' => request('last_name'),
    'email' => request('email'),
    'password' => Hash::make(request('password')),

    'profile_photo_path' => request('profile_photo_path'),
    'phone_number' => request('phone_number'),
    'address_line_one' => request('address_line_one'),
    'address_line_two' => request('address_line_two'),
    'zip_code' => request('zip_code'),
    'country' => request('country'),

    'company_id' => request('company_id'),
    ]);

    return response()->json([
    'error' => false,
    'message' => 'Professionel créé avec succès !',
    ], 200);

    } catch (\Throwable$th) {
    report($th);
    return response()->json([
    'error' => true,
    'message' => $th->getMessage(),
    ], 500);
    }
    }
     */

    public function login()
    {
        try {
            $validateUser = Validator::make(
                request()->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ], 401);
            }

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
