<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::withCount('usages')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $now = now();
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', $now);
            } elseif ($request->status === 'upcoming') {
                $query->where('start_date', '>', $now);
            }
        }

        $vouchers = $query->paginate(10)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucher($request);
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_public'] = $request->boolean('is_public');

        Voucher::create($validated);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $this->validateVoucher($request, $voucher->id);
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_public'] = $request->boolean('is_public');

        // Reset max_discount_amount jika tipe fixed
        if ($validated['discount_type'] === 'fixed') {
            $validated['max_discount_amount'] = null;
        }

        $voucher->update($validated);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        if ($voucher->usages()->exists()) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', 'Voucher tidak dapat dihapus karena sudah pernah digunakan pada transaksi.');
        }

        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }

    public function toggleStatus(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $voucher->is_active,
            'message'   => 'Status voucher berhasil diubah.'
        ]);
    }

    private function validateVoucher(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'code'                   => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($ignoreId)],
            'description'            => ['nullable', 'string', 'max:500'],
            'terms_and_conditions'   => ['nullable', 'string'],
            'discount_type'          => ['required', 'in:fixed,percentage'],
            'discount_value'         => ['required', 'numeric', 'min:0'],
            'max_discount_amount'    => ['nullable', 'required_if:discount_type,percentage', 'numeric', 'min:0'],
            'min_transaction_amount' => ['required', 'numeric', 'min:0'],
            'usage_limit'            => ['nullable', 'integer', 'min:1'],
            'limit_per_user'         => ['required', 'integer', 'min:1'],
            'start_date'             => ['required', 'date'],
            'end_date'               => ['required', 'date', 'after_or_equal:start_date'],
            'is_active'              => ['nullable', 'boolean'],
        ]);
    }
}