<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendResetPasswordTokenMail;
use App\Models\Professional;
use DB;
use Hash;
use Illuminate\Http\Request;
use Mail;

class ForgotPasswordController extends Controller
{
    public function send_reset_password_email()
    {
        try {
            $credentials = request()->validate([
                'email' => 'required|email|exists:professionals',
            ]);

            $token = rand(1000, 9999);

            DB::table('password_resets')->insert([
                'email' => request('email'),
                'token' => $token,
            ]);

            Mail::to(request('email'))->send(new SendResetPasswordTokenMail($token));

            return response()->json([
                'error' => false,
                'message' => 'Lien envoyé, veuillez vérifier votre boite email !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function verify_code()
    {
        try {
            request()->validate([
                'email' => 'required|email|exists:professionals',
                'token' => 'required',
            ]);

            if (!DB::table('password_resets')
                ->where([
                    ['email', request('email')],
                    ['token', request('token')],
                ])
                ->exists()) {
                return response()->json([
                    'error' => true,
                    'message' => 'OTP invalide !',
                ], 400);
            }

            return response()->json([
                'error' => false,
                'message' => 'Code correcte !',
            ], 200);

        } catch (\Throwable$th) {
            report($th);
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function reset_password()
    {
        try {
            request()->validate([
                'email' => 'required|email|exists:professionals',
                'token' => 'required',
                'password' => 'required|confirmed',
            ]);

            if (!DB::table('password_resets')
                ->where([
                    ['email', request('email')],
                    ['token', request('token')],
                ])
                ->exists()) {
                return response()->json([
                    'error' => true,
                    'message' => 'OTP invalide !',
                ], 400);
            }

            Professional::where('email', request('email'))->update([
                'password' => Hash::make(request('password')),
            ]);

            DB::table('password_resets')->where('email', request('email'))->delete();

            return response()->json([
                'error' => false,
                'message' => 'Mot de passe mis à jour avec succès !',
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
