<x-mail::message>
# Order update

Hi {{ $order->customer_name }},

Your order **{{ $order->number }}** moved from **{{ $previousLabel }}** to **{{ $order->statusLabel() }}**.

@if ($order->status === 'out_for_delivery')
It’s on the way (or ready for pickup, depending on your order type).
@elseif ($order->status === 'delivered')
We hope you enjoy every bite.
@elseif ($order->status === 'cancelled')
If this was unexpected, please call the bakery.
@endif

Thanks,<br>
{{ config('dezato.brand.name', 'Dezato Cake House') }}
</x-mail::message>
