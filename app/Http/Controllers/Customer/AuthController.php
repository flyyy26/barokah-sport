<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ============================================
    // REGISTER PAGE
    // ============================================

    public function showRegister()
    {
        return view('customer.auth.register');
    }

    // ============================================
    // REGISTER
    // ============================================

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()
            ->route('customer.home')
            ->with('success', 'Registrasi berhasil. Selamat berbelanja!');
    }

    // ============================================
    // LOGIN PAGE
    // ============================================

    public function showLogin()
    {
        return view('customer.auth.login');
    }

    // ============================================
    // LOGIN - Support Email OR Phone
    // ============================================

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string', // bisa email atau phone
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        // Cari user berdasarkan email atau phone
        $customer = Customer::where('email', $validated['login'])
            ->orWhere('phone', $validated['login'])
            ->first();

        if ($customer && Hash::check($validated['password'], $customer->password)) {
            Auth::guard('customer')->login($customer, $remember);
            $request->session()->regenerate();

            return redirect()
                ->intended(route('customer.home'))
                ->with('success', 'Selamat datang kembali!');
        }

        return back()
            ->withErrors([
                'login' => 'Email/Phone atau password salah.',
            ])
            ->onlyInput('login');
    }

    // ============================================
    // LOGOUT
    // ============================================

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('customer.login')
            ->with('success', 'Anda berhasil logout.');
    }

    // ============================================
    // GUEST REGISTER - Auto register dari checkout
    // ============================================

    public function guestRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
        ]);

        // Generate password default (phone number)
        $defaultPassword = substr(preg_replace('/[^0-9]/', '', $validated['phone']), -6);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return $customer;
    }
}