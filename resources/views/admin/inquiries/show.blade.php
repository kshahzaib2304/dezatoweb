@extends('layouts.admin')

@section('content')
    <p class="admin-back"><a href="{{ route('admin.inquiries.index') }}">← Back to messages</a></p>

    <div class="admin-split">
        <section class="admin-panel">
            <h2>Details</h2>
            <dl class="admin-detail">
                <div><dt>Type</dt><dd>{{ $inquiry->typeLabel() }}</dd></div>
                <div><dt>Name</dt><dd>{{ $inquiry->name ?: '-' }}</dd></div>
                <div><dt>Email</dt><dd><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div>
                <div><dt>Phone</dt><dd>@if($inquiry->phone)<a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a>@else - @endif</dd></div>
                @if ($inquiry->event_date)
                    <div><dt>Event date</dt><dd>{{ $inquiry->event_date->format('d M Y') }}</dd></div>
                @endif
                @if ($inquiry->guests)
                    <div><dt>Guests</dt><dd>{{ $inquiry->guests }}</dd></div>
                @endif
                @if ($inquiry->flavour)
                    <div><dt>Flavour</dt><dd>{{ $inquiry->flavour }}</dd></div>
                @endif
                @if ($inquiry->size)
                    <div><dt>Size</dt><dd>{{ $inquiry->size }}</dd></div>
                @endif
                @if ($inquiry->message_on_cake)
                    <div><dt>Cake message</dt><dd>{{ $inquiry->message_on_cake }}</dd></div>
                @endif
                <div><dt>Received</dt><dd>{{ optional($inquiry->created_at)->format('d M Y, h:i A') }}</dd></div>
            </dl>
            @if ($inquiry->notes)
                <h2 class="admin-section-gap">Notes</h2>
                <p>{{ $inquiry->notes }}</p>
            @endif
        </section>

        <section class="admin-panel">
            <h2>Reply</h2>
            <p class="admin-lead">Tap a button to contact the customer from your phone or email app.</p>
            <div class="admin-form__actions">
                <a class="btn btn--primary" href="mailto:{{ $inquiry->email }}">Email customer</a>
                @if ($inquiry->phone)
                    <a class="btn btn--outline" href="tel:{{ $inquiry->phone }}">Call</a>
                @endif
                @if ($whatsappUrl)
                    <a class="btn btn--outline" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">WhatsApp customer</a>
                @endif
            </div>
            <form method="post" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this message?');">
                @csrf
                @method('DELETE')
                <button class="text-btn text-btn--danger" type="submit">Delete message</button>
            </form>
        </section>
    </div>
@endsection
