<x-mail::message>
# {{ $inquiry->typeLabel() }}

@if ($inquiry->name)
**{{ $inquiry->name }}**<br>
@endif
{{ $inquiry->email }}@if($inquiry->phone) · {{ $inquiry->phone }}@endif

@if ($inquiry->event_date)
Event date: {{ $inquiry->event_date->format('d M Y') }}<br>
@endif
@if ($inquiry->guests)
Guests: {{ $inquiry->guests }}<br>
@endif
@if ($inquiry->flavour)
Flavour: {{ $inquiry->flavour }}<br>
@endif
@if ($inquiry->size)
Size: {{ $inquiry->size }}<br>
@endif
@if ($inquiry->message_on_cake)
Message on cake: {{ $inquiry->message_on_cake }}<br>
@endif

@if ($inquiry->notes)
{{ $inquiry->notes }}
@endif

<x-mail::button :url="route('admin.inquiries.show', $inquiry)">
Open in Admin
</x-mail::button>
</x-mail::message>
