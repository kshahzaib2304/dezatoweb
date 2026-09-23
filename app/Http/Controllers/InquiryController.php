<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\Inquiry;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function __construct(private readonly Notifier $notifier) {}

    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $inquiry = Inquiry::query()->create([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'event_date' => $data['event_date'] ?? null,
            'guests' => $data['guests'] ?? null,
            'notes' => $data['notes'] ?? null,
            'flavour' => $data['flavour'] ?? null,
            'size' => $data['size'] ?? null,
            'message_on_cake' => $data['message_on_cake'] ?? null,
            'is_read' => false,
        ]);

        $this->notifier->inquiryReceived($inquiry);

        return back()->with(
            'status',
            'Thanks - we received your message and will contact you shortly.'
        );
    }
}
