<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function index(): View
    {
        return view('admin.inquiries.index', [
            'title' => 'Messages | Dezato Admin',
            'heading' => 'Messages from website',
            'active' => 'inquiries',
            'inquiries' => Inquiry::query()->latest()->paginate(20),
            'unreadCount' => Inquiry::query()->unread()->count(),
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        if (! $inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
        }

        $customerDigits = preg_replace('/\D+/', '', (string) $inquiry->phone) ?: '';
        $prefill = 'Hi'.($inquiry->name ? ' '.$inquiry->name : '').', thanks for contacting Dezato Cake House regarding your '.$inquiry->typeLabel().'.';

        return view('admin.inquiries.show', [
            'title' => 'Message | Dezato Admin',
            'heading' => $inquiry->typeLabel(),
            'active' => 'inquiries',
            'inquiry' => $inquiry,
            'whatsappUrl' => $customerDigits !== ''
                ? 'https://wa.me/'.$customerDigits.'?text='.rawurlencode($prefill)
                : null,
        ]);
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('status', 'Message deleted.');
    }
}
