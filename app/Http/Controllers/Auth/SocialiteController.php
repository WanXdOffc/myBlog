<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google setelah autentikasi.
     *
     * Logika:
     * 1. Jika email sudah ada di DB → hubungkan google_id-nya (login).
     * 2. Jika email belum ada → buat user baru (register via Google).
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }

        // Cari user berdasarkan google_id terlebih dahulu
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // User sudah terdaftar dengan Google ID ini, langsung login
            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'));
        }

        // Cek apakah email sudah terdaftar (akun manual yang belum dihubungkan)
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            // Hubungkan google_id ke akun yang sudah ada
            $existingUser->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $existingUser->avatar ?? $googleUser->getAvatar(),
            ]);

            Auth::login($existingUser, remember: true);

            return redirect()->intended(route('dashboard'));
        }

        // Buat user baru dari data Google
        $newUser = User::create([
            'name'              => $googleUser->getName(),
            'email'             => $googleUser->getEmail(),
            'google_id'         => $googleUser->getId(),
            'avatar'            => $googleUser->getAvatar(),
            'email_verified_at' => now(), // Email Google sudah terverifikasi
            'password'          => null,  // Tidak ada password untuk akun Google
        ]);

        Auth::login($newUser, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
