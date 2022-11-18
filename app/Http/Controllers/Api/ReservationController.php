<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function store()
    {
        try {
            request()->validate([
                'establishment_id' => 'required',

                'client_name' => 'required',
                'client_phone_number' => 'required',
                'nb_people' => 'required',
                'day' => 'required|after:today',
                'hour' => 'required',
                'comment' => 'required',
            ]);

            $reservation = Reservation::create([
                'establishment_id' => request('establishment_id'),
                'client_name' => request('client_name'),
                'client_phone_number' => request('client_phone_number'),
                'nb_people' => request('nb_people'),
                'day' => request('day'),
                'hour' => request('hour'),
                'comment' => request('comment'),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Reservation créée avec succès !',
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
            $reservation = Reservation::findOrFail($id);

            $reservation->client_name = request('client_name');
            $reservation->client_phone_number = request('client_phone_number');
            $reservation->nb_people = request('nb_people');
            $reservation->day = request('day');
            $reservation->hour = request('hour');
            $reservation->comment = request('comment');

            $reservation->save();

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
            Reservation::destroy($id);

            return response()->json([
                'error' => false,
                'message' => 'Reservation supprimée avec succès !',
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
