@if (! empty($fulfillmentSummary))
    <div class="fulfillment-bar">
        <div class="container fulfillment-bar__inner">
            <p>
                <span class="fulfillment-bar__label">Ordering</span>
                {{ $fulfillmentSummary }}
            </p>
            <button type="button" data-fulfillment-open>Change</button>
        </div>
    </div>
@endif
