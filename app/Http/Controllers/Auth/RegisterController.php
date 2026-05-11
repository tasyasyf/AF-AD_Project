<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:afad,executive,pc,admin'],
        ]);

        $data['is_active'] = true;
        $data['email_verified_at'] = now();

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    private function redirectByRole(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'executive' => redirect()->route('executive.dashboard'),
            'pc'        => redirect()->route('pc.dashboard'),
            'admin'     => redirect()->route('admin.dashboard'),
            default     => redirect()->route('afad.dashboard'),
        };
    }
}
