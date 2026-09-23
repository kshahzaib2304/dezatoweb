<?php

namespace App\Support;

use App\Mail\InquiryReceivedMail;
use App\Mail\OrderPlacedAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Inquiry;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends bakery/customer emails without failing the request if mail is misconfigured.
 */
final class Notifier
{
    public function orderPlaced(Order $order): void
    {
        $order->loadMissing('items');

        $this->safeSend(
            fn () => Mail::to($order->email)->send(new OrderPlacedCustomerMail($order)),
            'customer order confirmation',
            $order->number
        );

        $this->safeSend(
            fn () => Mail::to(BakeryProfile::notifyEmail())->send(new OrderPlacedAdminMail($order)),
            'admin order alert',
            $order->number
        );
    }

    public function orderStatusUpdated(Order $order, string $previousStatus): void
    {
        if (! SiteContent::statusEmailsEnabled()) {
            return;
        }

        if ($previousStatus === $order->status) {
            return;
        }

        $previousLabel = Order::STATUSES[$previousStatus]
            ?? ucfirst(str_replace('_', ' ', $previousStatus));

        $this->safeSend(
            fn () => Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $previousLabel)),
            'customer status update',
            $order->number
        );
    }

    public function inquiryReceived(Inquiry $inquiry): void
    {
        $this->safeSend(
            fn () => Mail::to(BakeryProfile::notifyEmail())->send(new InquiryReceivedMail($inquiry)),
            'admin inquiry alert',
            (string) $inquiry->id
        );
    }

    private function safeSend(callable $send, string $label, string $ref): void
    {
        try {
            $send();
        } catch (Throwable $exception) {
            Log::warning('Dezato mail failed: '.$label, [
                'ref' => $ref,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
