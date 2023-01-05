<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function index($id)
    {
        // die;
        $boutique = Establishment::findOrFail($id);
        // if ($boutique->company_id != Auth::user()->company_id) {
        //     abort(404);
        // }

        $confirme = Commande::with('particulier', 'commandeProduct.establishmentProduct.product')
            ->where('establishment_id', $id)
            ->orderBy('created_at')
            ->where('status', 1)
            ->get();

        $enattente = Commande::with('particulier', 'commandeProduct.establishmentProduct.product')
            ->where('establishment_id', $id)
            ->orderBy('created_at')
            ->where('status', 0)
            ->get();

        $prete = Commande::with('particulier', 'commandeProduct.establishmentProduct.product')
            ->where('establishment_id', $id)
            ->orderBy('created_at')
            ->where('status', 3)
            ->get();

        $annule = Commande::with('particulier', 'commandeProduct.establishmentProduct.product')
            ->where('establishment_id', $id)
            ->orderBy('created_at')
            ->where('status', 2)
            ->get();

        return response()->json([
            'error' => false,
            'boutique' => $boutique,
            'annule' => $annule,
            'prete' => $prete,
            'enattente' => $enattente,
            'confirme' => $confirme,
            'message' => 'Mise à jour effectuée avec succès !',
        ], 200);
    }

    public function show($id, $commandeId)
    {
        $boutique = Establishment::findOrFail($id);
        if ($boutique->company_id != Auth::user()->company_id) {
            abort(404);
        }
        session(['establishmentId' => $boutique->id]);

        $commande = Commande::with('particulier', 'commandeProduct.establishmentProduct.product')->findOrFail($commandeId);
        $historiqueCMD = Commande::with('commandeProduct.establishmentProduct.product')
            ->where('particulier_id', $commande->particulier->id)
            ->whereHas('commandeProduct')
            ->orderBy('created_at', 'DESC')
            ->take(3)
            ->get();
        // dd($historiqueCMD);
        return view('boutique.commande.show', compact('boutique', 'commande', 'historiqueCMD'));
    }

    public function updateStatus($id, $commandeId)
    {
        try {
            $boutique = Establishment::findOrFail($id);
            // if ($boutique->company_id != Auth::user()->company_id) {
            //     abort(404);
            // }

            $commande = Commande::with('establishment', 'particulier')->findOrFail($commandeId);
            $commande->update([
                'status' => request('status'),
            ]);
            // $user = User::findOrFail();
            // $data['id'] = $commande->particulier_id;

            // $data['title'] = 'Votre commande a été mise à jour';
            // switch ($request->status) {
            //     case 2:
            //         $objet = "Votre commande a été annulée ";
            //         $data['body'] = "Votre commande " . $commandeId . " a été annulée pour le motif suivant : \" " . $commande->message . " \"";
            //         break;
            //     case 3:
            //         $objet = "Votre commande  est en cours de livraison";
            //         $data['body'] = "Ta commande N°" . $commandeId . " est  confirmée  auprès de " . $commande->establishment->name . ". Elle sera prête a etre récupérer a \"" . $commande->comming_hour . "\".";
            //         break;
            //     case 4:
            //         $objet = "Votre commande  est en cours de livraison";
            //         $data['body'] = "Votre commande " . $commandeId . " est en cours de livraison. Elle vous sera livrée á \" " . $request->comming_hour . " \"";
            //         break;
            //     case 1:
            //         $objet = "Votre commande  a été livrée";
            //         $data['body'] = "Votre commande " . $commandeId . " a été livrée, vous pouvez laisser un avis";
            //         break;
            //     default:
            //         # code...
            //         break;
            // }
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
}
