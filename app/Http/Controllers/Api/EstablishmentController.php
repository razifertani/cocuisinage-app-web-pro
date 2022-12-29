<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\NotificationType;

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
                'img' => $this->upload_image(auth()->user()->id),
            ]);

            auth()->user()->attach_role($establishment->id, config('cocuisinage.role_owner_id'));
            auth()->user()->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment->id, 'active' => 1]);

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

    public function update($id)
    {
        try {
            $establishment = Establishment::findOrFail($id);

            $establishment->name = request('name');
            $establishment->city = request('city');
            // $establishment->longitude = request('longitude');
            // $establishment->latitude = request('latitude');
            if (request()->hasFile('image')) {
                $establishment->image_path = $this->upload_image(auth()->user()->id);
            }

            $establishment->save();

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

    public function update_booking_duration($id, $booking_duration)
    {
        try {
            $establishment = Establishment::findOrFail($id);

            $establishment->booking_duration = $booking_duration;

            $establishment->save();

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
            Establishment::destroy($id);

            return response()->json([
                'error' => false,
                'message' => 'Boutique supprimée avec succès !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function update_schedule($id)
    {
        try {
            $establishment = Establishment::with('schedules')->findOrFail($id);

            if (request('part') == '1') {
                $establishment->schedules()->where('day', request('day'))->update([
                    'begin' => request('begin'),
                    'ending' => request('ending'),
                ]);

            } else if (request('part') == '2') {
                $establishment->schedules()->where('day', request('day'))->update([
                    'second_begin' => request('second_begin'),
                    'second_end' => request('second_end'),
                ]);

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

}
