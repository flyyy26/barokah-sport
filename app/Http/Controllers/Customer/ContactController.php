<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $faqs = Faq::active()->ordered()->get();
        $categories = [
            'umum' => 'Umum',
            'produk' => 'Produk',
            'pengiriman' => 'Pengiriman',
            'pembayaran' => 'Pembayaran',
            'garansi' => 'Garansi & Retur',
        ];
        
        return view('customer.contact.index', compact('setting', 'faqs', 'categories'));
    }
}