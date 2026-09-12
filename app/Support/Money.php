<?php

namespace App\Support;

final class Money
{
    /**
     * Format an amount in Pakistani Rupees (₨).
     */
    public static function format(float|int|string $amount): string
    {
        return '₨ '.number_format((float) $amount, 0);
    }
}
