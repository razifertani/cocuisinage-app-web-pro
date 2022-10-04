<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function store()
    {
        try {
            request()->validate([
                'establishment_id' => 'required',
                'name' => 'required',
            ]);

            if (!auth()->user()->hasRole(Role::find(1)->name)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Seul le patron peut ajouter des rôles !',
                ], 401);
            }

            $role = Role::firstOrCreate([
                'establishment_id' => request('establishment_id'),
                'name' => request('name'),
                'guard_name' => 'web',
            ]);

            $role->syncPermissions(Permission::all()->pluck('id'));

            return response()->json([
                'error' => false,
                'message' => 'Rôle créé avec succès !',
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
            request()->validate([
                'name' => 'required',
            ]);

            if (!auth()->user()->hasRole(Role::find(1)->name)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Seul le patron peut modifier des rôles !',
                ], 401);
            }
            // Role "Patron"
            if ($id == 1) {
                return response()->json([
                    'error' => true,
                    'message' => 'Le rôle du patron ne peut pas être modifié !',
                ], 401);
            }

            $role = Role::where('id', $id)->update([
                'name' => request('name'),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Rôle modifié avec succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            request()->validate([
                'establishment_id' => 'required',
            ]);

            if ($id == 1) {
                return response()->json([
                    'error' => true,
                    'message' => 'Le rôle du patron ne peut pas être modifié !',
                ], 401);
            }

            $professionals_count = DB::table('professional_roles_in_establishment')->where([
                ['establishment_id', request('establishment_id')],
                ['role_id', $id],
            ])->count();

            if ($professionals_count > 0) {
                return response()->json([
                    'error' => true,
                    'message' => 'Ce rôle est déjà affecté à ' . $professionals_count . ' professionels !',
                ], 200);

            }

            Role::destroy($id);

            return response()->json([
                'error' => false,
                'message' => 'Rôle supprimé avec succès !',
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
