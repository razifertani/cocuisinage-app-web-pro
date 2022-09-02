<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Models\Establishment;
// use App\Models\Company;
use App\Models\Professional;
use Auth;
use Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;

class AuthController extends Controller
{
    public function test()
    {
        // $professional = Company::with('establishments', 'professionals')->findOrFail(1);
        // $professional = Establishment::with('company', 'professionals')->findOrFail(1);
        // $professional = Professional::with('company.establishments', 'establishments_roles', 'establishments_permissions', 'roles.permissions')
        //     ->findOrFail(1);

        // $professional->establishments_roles()->attach(
        //     1,
        //     [
        //         'role_id' => 5,
        //         'model_type' => 'App\Models\Professional',
        //     ]
        // );

        // $professional->permissions()->attach(
        //     Role::findOrFail(5)->permissions,
        //     [
        //         'establishment_id' => 1,
        //         'model_type' => 'App\Models\Professional',
        //     ]
        // );

        return response()->json([
            'data' => $professional,
        ]);
    }

    public function config()
    {
        $role1 = Role::create(['name' => 'Patron']);
        $role2 = Role::create(['name' => 'Responsable salle']);
        $role3 = Role::create(['name' => 'Responsable cuisine']);
        $role4 = Role::create(['name' => 'Serveur']);
        $role5 = Role::create(['name' => 'Cuisinier']);
        $role6 = Role::create(['name' => 'Plongeur']);
        $permission1 = Permission::create(['name' => 'Manage order status']);
        $permission2 = Permission::create(['name' => 'Manage order preparation']);
        $permission3 = Permission::create(['name' => 'Add collaborators']);
        $permission4 = Permission::create(['name' => 'Add tasks to the team']);
        $permission5 = Permission::create(['name' => 'Manage roles']);
        $permission6 = Permission::create(['name' => 'Manage the store']);
        $permission7 = Permission::create(['name' => 'Add products']);
        $permission8 = Permission::create(['name' => 'Manage the wallet']);
        $permission9 = Permission::create(['name' => 'Manage transfers and transactions']);
        $permission10 = Permission::create(['name' => 'Add a recipe']);
        $permission11 = Permission::create(['name' => 'Favorite recipes']);
        $permission12 = Permission::create(['name' => 'Change store']);

        // $role1 = Role::where(['name' => 'Patron'])->first();
        // $role2 = Role::where(['name' => 'Responsable salle'])->first();
        // $role3 = Role::where(['name' => 'Responsable cuisine'])->first();
        // $role4 = Role::where(['name' => 'Serveur'])->first();
        // $role5 = Role::where(['name' => 'Cuisinier'])->first();
        // $role6 = Role::where(['name' => 'Plongeur'])->first();
        // $permission1 = Permission::where(['name' => 'Manage order status'])->first();
        // $permission2 = Permission::where(['name' => 'Manage order preparation'])->first();
        // $permission3 = Permission::where(['name' => 'Add collaborators'])->first();
        // $permission4 = Permission::where(['name' => 'Add tasks to the team'])->first();
        // $permission5 = Permission::where(['name' => 'Manage roles'])->first();
        // $permission6 = Permission::where(['name' => 'Manage the store'])->first();
        // $permission7 = Permission::where(['name' => 'Add products'])->first();
        // $permission8 = Permission::where(['name' => 'Manage the wallet'])->first();
        // $permission9 = Permission::where(['name' => 'Manage transfers and transactions'])->first();
        // $permission10 = Permission::where(['name' => 'Add a recipe'])->first();
        // $permission11 = Permission::where(['name' => 'Favorite recipes'])->first();
        // $permission12 = Permission::where(['name' => 'Change store'])->first();

        $role1->syncPermissions([
            $permission1, $permission2, $permission3, $permission4, $permission5, $permission6,
            $permission7, $permission8, $permission9, $permission10, $permission11, $permission12,
        ]);

        $role2->syncPermissions([
            $permission2, $permission3, $permission4, $permission5,
            $permission6, $permission7, $permission10, $permission11,
        ]);

        $role3->syncPermissions([
            $permission2, $permission3, $permission4, $permission5,
            $permission6, $permission7, $permission10, $permission11,
        ]);

        $role4->syncPermissions([
            $permission2, $permission3, $permission4, $permission5,
            $permission6, $permission7, $permission10, $permission11,
        ]);

        $role5->syncPermissions([
            $permission2, $permission4, $permission10,
        ]);

        $role6->syncPermissions([
            $permission2, $permission4, $permission10,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Roles & Permissions are successfully created !',
        ], 200);
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
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if (!Auth::attempt(request()->only(['email', 'password']))) {
                return response()->json([
                    'error' => true,
                    'message' => 'Email & Password does not match with our record',
                ], 200);
            }

            $professional = Professional::
                with('roles.permissions', 'permissions')
                ->where('email', request('email'))
                ->first();

            $professional->allPermissions = $professional->getAllPermissions()->pluck('id');

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

    public static function user()
    {
        try {

            $user = request()->user();
            $user->load('roles.permissions', 'permissions');
            $user->allPermissions = $user->getAllPermissions()->pluck('id');

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
