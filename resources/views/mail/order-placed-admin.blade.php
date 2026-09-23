<x-mail::message>
# New order {{ $order->number }}

**{{ $order->customer_name }}** · {{ $order->phone }} · {{ $order->email }}

Total: **{{ pkr($order->total) }}** · {{ $order->methodLabel() }} · {{ \App\Support\PaymentMethods::label($order->payment_method) }}

@foreach ($order->items as $item)
- {{ $item->quantity }} × {{ $item->product_name }} - {{ pkr($item->line_total) }}
  @if ($item->isCustom())
  <br><small>{{ $item->optionsSummary() }}</small>
  @endif
@endforeach

<x-mail::button :url="route('admin.orders.show', $order)">
Open in Admin
</x-mail::button>

{{ config('dezato.brand.name', 'Dezato') }}
</x-mail::message>
