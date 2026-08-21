<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $privacy = PrivacyPolicy::first();
        return view('admin.privacy.index', compact('privacy'));
    }

    public function update(Request $request)
    {
        // DEBUG: Cek data yang masuk
        // dd($request->all());
        
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $privacy = PrivacyPolicy::first();

            if ($privacy) {
                $privacy->update([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'version' => $validated['version'] ?? $privacy->version,
                    'effective_date' => $validated['effective_date'] ?? $privacy->effective_date,
                    'is_active' => $request->has('is_active') ? true : false,
                ]);
            } else {
                PrivacyPolicy::create([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'version' => $validated['version'] ?? '1.0',
                    'effective_date' => $validated['effective_date'] ?? now(),
                    'is_active' => $request->has('is_active') ? true : false,
                ]);
            }

            return redirect()
                ->route('admin.privacy.index')
                ->with('success', 'Kebijakan Privasi berhasil disimpan.');

        } catch (Throwable $e) {
            // DEBUG: Lihat error
            // dd($e->getMessage(), $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function toggle(Request $request)
    {
        try {
            $privacy = PrivacyPolicy::first();
            if ($privacy) {
                $privacy->update(['is_active' => !$privacy->is_active]);
                $status = $privacy->is_active ? 'diaktifkan' : 'dinonaktifkan';
                return redirect()
                    ->route('admin.privacy.index')
                    ->with('success', "Kebijakan Privasi berhasil {$status}.");
            }
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}