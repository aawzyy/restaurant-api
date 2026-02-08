<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Langkah 1: Login & Kirim OTP
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek apakah user ada di database
        $user = User::where('email', $request->email)->first();

        // Cek password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial tidak valid.',
            ], 401);
        }

        // Generate 6 digit OTP
        $otp = rand(111111, 666666);

        // Update OTP di database
        $user->update([
            'otp_code' => $otp,
        ]);

        // Kirim Email Asli lewat Gmail
        try {
            Mail::to($user->email)->send(new SendOtpMail($otp, $user->name));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kode OTP telah dikirim ke email Anda.',
            'email' => $user->email,
        ]);
    }

    // Di dalam Class AuthController
    public function googleLogin(Request $request)
    {
        // 1. Validasi Token masuk
        $request->validate([
            'access_token' => 'required',
        ]);

        try {
            // 2. Tanya ke Google: "Ini token punya siapa?"
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);
            
            // 3. Cari di Database User kita: "Ada gak email ini?"
            $user = User::where('email', $googleUser->getEmail())->first();

            // --- BAGIAN PENTING (PENJAGA PINTU) ---
            // Jika user KOSONG (tidak ketemu), langsung TOLAK (401)
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses Ditolak: Email Anda (' . $googleUser->getEmail() . ') belum terdaftar di sistem. Hubungi Admin.'
                ], 401); 
            }
            // ---------------------------------------

            // 4. Jika user ADA, update Google ID-nya (biar sinkron)
            $user->update([
                'google_id' => $googleUser->getId(),
                // Opsional: Update nama juga kalau mau nama sesuai Google
                // 'name' => $googleUser->getName(), 
            ]);

            // 5. Buat Token Akses (Passport/Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // 6. Kirim jawaban sukses ke Flutter
            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil',
                'access_token' => $token,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Langkah 2: Verifikasi OTP & Berikan Token
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cari user yang email dan OTP-nya cocok
        $user = User::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP salah atau sudah kadaluarsa.',
            ], 401);
        }

        // Hapus OTP setelah digunakan (keamanan)
        $user->update(['otp_code' => null]);

        // Buat Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Langkah 3: Logout
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout.',
        ]);
    }
}
