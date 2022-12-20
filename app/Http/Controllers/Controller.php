<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function upload_image($professional_id)
    {
        try {
            request()->validate([
                'image' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ]);

            $imageName = request('image')->hashName();

            request('image')->storeAs('professionals/' . $professional_id, $imageName, 's3');

            $link = Storage::cloud()->url($imageName);

            return $imageName;

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
