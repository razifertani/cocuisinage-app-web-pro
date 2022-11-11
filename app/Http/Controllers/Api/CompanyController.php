<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    public function update($id)
    {
        try {
            $company = Company::findOrFail($id);

            $company->name = request('name');
            $company->email = request('email');
            $company->phone_number = request('phone_number');
            $company->rib = request('rib');
            $company->siret = request('siret');

            $company->save();

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
}
