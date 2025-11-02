<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validasi email
        $request->validate(['email' => 'required|email|exists:users']);

        // 2. Menggunakan Password Broker untuk mengirim link reset
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3. Mengembalikan respons JSON berdasarkan status
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent successfully to your email.',
                // Catatan: Di API, terkadang lebih baik mengirim token daripada link penuh.
                // Jika Anda ingin mengirim token mentah, Anda perlu custom notification.
            ], 200);
        }

        // Jika ada masalah (misalnya email tidak ditemukan, meskipun sudah divalidasi)
        return response()->json([
            'message' => __($status)
        ], 400); 
    }

    public function resetPassword(Request $request)
    {
        // 1. Validasi semua data yang diperlukan
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // 2. Menggunakan Password Broker untuk mereset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Hapus semua token Sanctum lama untuk keamanan
                $user->tokens()->delete(); 
                
                // Update password
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // 3. Mengembalikan respons JSON berdasarkan status
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.'], 200);
        }

        // Jika token tidak valid, kadaluarsa, atau ada masalah lain
        return response()->json([
            'message' => __($status)
        ], 400); 
    }
}
