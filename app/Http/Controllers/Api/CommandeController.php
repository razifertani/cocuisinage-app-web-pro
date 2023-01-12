<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Establishment;

class CommandeController extends Controller
{
    public function updateStatus($id, $commandeId)
    {
        try {
            $establishment = Establishment::findOrFail($id);

            $commande = Commande::findOrFail($commandeId);

            $commande->status = request('status');

            $commande->save();

            return response()->json([
                'error' => false,
                'message' => 'Commande mise à jour succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateProductStatus($id, $commandeProductId)
    {
        try {
            $establishment = Establishment::findOrFail($id);

            $command_product = CommandProduct::findOrFail($commandeProductId);

            $command_product->status = request('status');

            $command_product->save();

            return response()->json([
                'error' => false,
                'message' => 'Produit mise à jour succès !',
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
