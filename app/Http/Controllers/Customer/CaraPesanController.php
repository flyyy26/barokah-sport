<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class CaraPesanController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        
        // Ambil FAQ terkait cara pesan jika ada
        $faqs = Faq::with('category')
            ->active()
            ->ordered()
            ->get();
        
        $categories = FaqCategory::active()
            ->ordered()
            ->get();

        return view('customer.cara-pesan', compact('setting', 'faqs', 'categories'));
    }
}