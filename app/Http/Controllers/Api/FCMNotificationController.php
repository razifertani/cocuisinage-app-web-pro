<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establishment;

class EstablishmentController extends Controller
{
    public function store()
    {
        try {
            request()->validate([
                'company_id' => 'required',
                'name' => 'required',
                'city' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
            ]);

            $establishment = Establishment::create([
                'company_id' => request('company_id'),
                'name' => request('name'),
                'city' => request('city'),
                'longitude' => request('longitude'),
                'latitude' => request('latitude'),
                'image_path' => $this->upload_image(auth()->user()->id),
            ]);

            auth()->user()->attach_role($establishment->id, config('cocuisinage.role_owner_id'));

            return response()->json([
                'error' => false,
                'message' => 'Boutique créée avec succès !',
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
