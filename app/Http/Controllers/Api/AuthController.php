<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Auth;
use Hash;
use Spatie\Permission\Models\Role;
use Validator;

class AuthController extends Controller
{

    public function test()
    {
        $role = Role::create(['name' => 'Restaurateur']);
        return $role;
    }
    public function register()
    {
        try {
            $validateUser = Validator::make(request()->all(),
                [
                    // 'name' => 'required',
                    'email' => 'required|email|unique:professional,email',
                    'password' => 'required',

                    // 'first_name',
                    // 'last_name',
                    // 'email',
                    // 'password',
                    // 'phone_number',
                    // 'address_line_one',
                    // 'address_line_two',
                    // 'country',
                    // 'state',
                    // 'zip_code',
                    // 'profile_photo_path',
                    // 'cov_photo_path',
                    // 'company_id',
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            $user = Professional::create([
                'first_name' => request('first_name'),
                'last_name' => request('last_name'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
                'phone_number' => request('phone_number'),
                'address_line_one' => request('address_line_one'),
                'address_line_two' => request('address_line_two'),
                'country' => request('country'),
                // 'state' => request('state'),
                'zip_code' => request('zip_code'),
                'profile_photo_path' => request('profile_photo_path'),
                // 'cov_photo_path' => request('cov_photo_path'),
                'company_id' => request('company_id'),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Professional Created Successfully',
            ], 200);

        } catch (\Throwable$th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function login()
    {
        try {
            $validateUser = Validator::make(request()->all(),
                [

                    'email' => 'required|email',
                    'password' => 'required',
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if (!Auth::attempt(request()->only(['email', 'password']))) {
                return response()->json([
                    'error' => true,
                    'message' => 'Email & Password does not match with our record.',
                ], 200);
            }

            $professional = Professional::
                with('roles')
                ->where('email', request('email'))->first();

            return response()->json([
                'error' => false,
                'token' => $professional->createToken("API TOKEN")->plainTextToken,
                'data' => $professional,
            ], 200);

        } catch (\Throwable$th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function user()
    {
        try {

            $user = request()->user();
            $user->load('roles');

            $role = Role::where('name', 'Restaurateur')->first();
            // $user->syncRoles([$role]);

            return response()->json([
                'error' => false,
                'data' => $user,
            ], 200);

        } catch (\Throwable$th) {
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
                'message' => 'Logged out',
            ], 200);

        } catch (\Throwable$th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
