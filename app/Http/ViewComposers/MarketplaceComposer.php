<?php

namespace App\Http\ViewComposers;

use App\Models\Marketplace;
use Illuminate\View\View;

class MarketplaceComposer
{
    public function compose(View $view)
    {
        $marketplaces = Marketplace::active()->get();
        $view->with('marketplaces', $marketplaces);
    }
}