@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>Payment options - keep it simple</strong>
        <ol class="admin-steps">
            <li>Tick the methods customers may choose at checkout.</li>
            <li>For Bank / JazzCash / Easypaisa, fill in your account details below - customers will see them when they order.</li>
            <li>When someone pays by transfer, open the order and tap <strong>Mark as paid</strong>.</li>
            <li>Card / online JazzCash API keys (for later) live under <strong>Email, logins &amp; links</strong> - not here.</li>
            <li>Card online payments need a payment partner later - leave unticked for now.</li>
        </ol>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.payments.update') }}">
        @csrf
        @method('PUT')

        <section class="admin-panel">
            <h2>Which methods to offer</h2>
            <ul class="admin-pay-list">
                @foreach ($catalog as $method)
                    <li>
                        <label class="check-inline">
                            <input
                                type="checkbox"
                                name="methods[]"
                                value="{{ $method['id'] }}"
                                @checked(in_array($method['id'], $enabled, true))
                            >
                            <span>
                                <strong>{{ $method['label'] }}</strong>
                                <small class="admin-muted">{{ $method['hint'] }}</small>
                                @if ($method['live'])
                                    <span class="admin-badge admin-badge--ok">Works now</span>
                                @else
                                    <span class="admin-badge">Needs partner later</span>
                                @endif
                            </span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="admin-panel">
            <h2>Bank transfer details</h2>
            <p class="admin-lead">Shown when the customer chooses Bank transfer.</p>
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="bank-name">Account title</label>
                    <input id="bank-name" class="field-input" type="text" name="instructions[bank_transfer][account_name]" value="{{ old('instructions.bank_transfer.account_name', $instructions['bank_transfer']['account_name']) }}">
                </div>
                <div class="form-row">
                    <label class="field-label" for="bank-bank">Bank name</label>
                    <input id="bank-bank" class="field-input" type="text" name="instructions[bank_transfer][bank_name]" value="{{ old('instructions.bank_transfer.bank_name', $instructions['bank_transfer']['bank_name']) }}" placeholder="e.g. HBL, Meezan">
                </div>
                <div class="form-row">
                    <label class="field-label" for="bank-number">Account number</label>
                    <input id="bank-number" class="field-input" type="text" name="instructions[bank_transfer][account_number]" value="{{ old('instructions.bank_transfer.account_number', $instructions['bank_transfer']['account_number']) }}">
                </div>
                <div class="form-row">
                    <label class="field-label" for="bank-iban">IBAN (optional)</label>
                    <input id="bank-iban" class="field-input" type="text" name="instructions[bank_transfer][iban]" value="{{ old('instructions.bank_transfer.iban', $instructions['bank_transfer']['iban']) }}">
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="bank-notes">Extra instructions</label>
                    <textarea id="bank-notes" class="field-input" name="instructions[bank_transfer][notes]" rows="2">{{ old('instructions.bank_transfer.notes', $instructions['bank_transfer']['notes']) }}</textarea>
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <h2>JazzCash details</h2>
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="jc-name">Account name</label>
                    <input id="jc-name" class="field-input" type="text" name="instructions[jazzcash][account_name]" value="{{ old('instructions.jazzcash.account_name', $instructions['jazzcash']['account_name']) }}">
                </div>
                <div class="form-row">
                    <label class="field-label" for="jc-number">JazzCash number</label>
                    <input id="jc-number" class="field-input" type="text" name="instructions[jazzcash][account_number]" value="{{ old('instructions.jazzcash.account_number', $instructions['jazzcash']['account_number']) }}" placeholder="03XX XXXXXXX">
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="jc-notes">Extra instructions</label>
                    <textarea id="jc-notes" class="field-input" name="instructions[jazzcash][notes]" rows="2">{{ old('instructions.jazzcash.notes', $instructions['jazzcash']['notes']) }}</textarea>
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <h2>Easypaisa details</h2>
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="ep-name">Account name</label>
                    <input id="ep-name" class="field-input" type="text" name="instructions[easypaisa][account_name]" value="{{ old('instructions.easypaisa.account_name', $instructions['easypaisa']['account_name']) }}">
                </div>
                <div class="form-row">
                    <label class="field-label" for="ep-number">Easypaisa number</label>
                    <input id="ep-number" class="field-input" type="text" name="instructions[easypaisa][account_number]" value="{{ old('instructions.easypaisa.account_number', $instructions['easypaisa']['account_number']) }}" placeholder="03XX XXXXXXX">
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="ep-notes">Extra instructions</label>
                    <textarea id="ep-notes" class="field-input" name="instructions[easypaisa][notes]" rows="2">{{ old('instructions.easypaisa.notes', $instructions['easypaisa']['notes']) }}</textarea>
                </div>
            </div>
        </section>

        <div class="admin-form__actions">
            <button class="btn btn--primary" type="submit">Save payment options</button>
        </div>
    </form>
@endsection
