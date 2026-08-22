<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login customer
     */
    public function showLogin()
    {
        // 🔥 CEK JIKA SUDAH LOGIN SEBAGAI CUSTOMER
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account');
        }
        
        // 🔥 CEK JIKA SUDAH LOGIN SEBAGAI ADMIN (GUARD DEFAULT)
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('customer.auth.login');
    }

    /**
     * Proses login customer
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 🔥 GUNAKAN guard('customer') UNTUK LOGIN
        if (Auth::guard('customer')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('customer')->user();

            // 🔥 HAPUS SESSION CART & WISHLIST
            session()->forget('cart');
            session()->forget('wishlist');
            session()->forget('is_buy_now');
            session()->forget('old_cart_backup');

            return redirect()
                ->route('customer.account')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Email atau password salah.');
    }

    /**
     * Menampilkan halaman registrasi customer
     */
    public function showRegister()
    {
        // 🔥 CEK JIKA SUDAH LOGIN SEBAGAI CUSTOMER
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account');
        }

        return view('customer.auth.register');
    }

    /**
     * Proses registrasi customer
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
            ]);

            // 🔥 GUNAKAN guard('customer') UNTUK LOGIN
            Auth::guard('customer')->login($user);

            return redirect()
                ->route('customer.account')
                ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Registrasi gagal: ' . $e->getMessage());
        }
    }

    /**
     * Proses logout customer
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('customer.login')
            ->with('success', 'Berhasil keluar.');
    }
}