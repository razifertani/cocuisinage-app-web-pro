<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;

class ReservationController extends Controller
{
    public function store()
    {
        try {
            logger(request('day'));
            request()->validate([
                'establishment_id' => 'required',

                'client_name' => 'required',
                'client_phone_number' => 'required',
                'nb_people' => 'required',
                'day' => 'required|after_or_equal:now',
                'hour' => 'required',
            ]);

            $reservation = Reservation::create([
                'establishment_id' => request('establishment_id'),
                'client_name' => request('client_name'),
                'client_phone_number' => request('client_phone_number'),
                'nb_people' => request('nb_people'),
                'day' => request('day'),
                'hour' => request('hour'),
                'comment' => request('comment') ?? '',
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

    public function assign_table_to_reservation($id, $table_id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $table = Table::findOrFail($table_id);

            if ($table->is_free_for_day_and_hour(request('day'), request('hour'))) {
                $reservation->table_id = $table_id;
                $reservation->save();
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Table is not free at ' . request('hour') . ' !',
                ], 200);
            }

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
