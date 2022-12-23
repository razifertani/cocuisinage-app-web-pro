<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;

class TableController extends Controller
{
    public function store()
    {
        try {
            request()->validate([
                'establishment_id' => 'required',

                'name' => 'required',
                'nb_people' => 'required',
            ]);

            $table = Table::create([
                'establishment_id' => request('establishment_id'),
                'name' => request('name'),
                'client_phone_number' => request('client_phone_number'),
                'nb_people' => request('nb_people'),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Réservation créée avec succès !',
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
            $table = Table::findOrFail($id);

            $table->name = request('name');
            $table->nb_people = request('nb_people');

            $table->save();

            return response()->json([
                'error' => false,
                'message' => 'Mise à jour effectuée avec succès !',
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
            Table::destroy($id);

            return response()->json([
                'error' => false,
                'message' => 'Table supprimée avec succès !',
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
