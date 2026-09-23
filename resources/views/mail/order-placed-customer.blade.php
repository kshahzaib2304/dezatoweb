<x-mail::message>
# Thanks for your order

Hi {{ $order->customer_name }},

We received order **{{ $order->number }}** for **{{ pkr($order->total) }}**.

**{{ $order->methodLabel() }}**@if($order->location_name) · {{ $order->location_name }}@endif

@foreach ($order->items as $item)
- {{ $item->quantity }} × {{ $item->product_name }} — {{ pkr($item->line_total) }}
@endforeach

We’ll update you as your order moves from baking to delivery / pickup.

Thanks,<br>
{{ config('dezato.brand.name', 'Dezato Cake House') }}
</x-mail::message>
