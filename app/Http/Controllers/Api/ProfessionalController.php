<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

// use App\Models\Company;
// use App\Models\Establishment;
// use App\Models\Professional;
// use Hash;

// use Auth;

// use Illuminate\Validation\Rule;

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

                'plannings',
                'company.establishments.professionals.plannings.tasks',

                'tasks',
                // 'company.establishments.plannings',
                //  'roles.permissions'
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

}
