<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()->orderBy('is_default', 'desc')->get();

        return view('customer.account.index', compact('customer', 'addresses'));
    }

    // ============================================
    // RIWAYAT PESANAN
    // ============================================

    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        
        // 🔥 AMBIL DATA ORDER DARI DATABASE
        $orders = Order::with(['items.product', 'items.variant'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.account.orders', compact('orders'));
    }

    // ============================================
    // ADDRESS CRUD
    // ============================================

    public function createAddress()
    {
        return view('customer.account.addresses.create');
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        $customer = Auth::guard('customer')->user();

        if ($customer->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        if (isset($validated['is_default']) && $validated['is_default']) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $customer->addresses()->create($validated);

        return redirect()
            ->route('customer.account')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function editAddress(CustomerAddress $address)
    {
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        // 🔥 Debug: cek data address
        \Log::info('Edit Address Data:', [
            'id' => $address->id,
            'postal_code' => $address->postal_code,
            'city' => $address->city,
            'province' => $address->province,
        ]);

        return view('customer.account.addresses.edit', compact('address'));
    }

    public function updateAddress(Request $request, CustomerAddress $address)
    {
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        $customer = Auth::guard('customer')->user();

        if (isset($validated['is_default']) && $validated['is_default']) {
            $customer->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()
            ->route('customer.account')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroyAddress(CustomerAddress $address)
    {
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $firstAddress = Auth::guard('customer')
                ->user()
                ->addresses()
                ->first();

            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        return redirect()
            ->route('customer.account')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    public function showOrder(Order $order)
    {
        // Pastikan order milik customer yang login
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('customer.account.order-detail', compact('order'));
    }
}