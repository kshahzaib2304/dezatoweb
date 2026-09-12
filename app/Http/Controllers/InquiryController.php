<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        // Persist later (mail / CRM). For now validate and acknowledge.
        $request->session()->flash(
            'status',
            'Thanks - we received your request and will contact you shortly.'
        );

        return back();
    }
}
