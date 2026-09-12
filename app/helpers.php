<?php

use App\Support\Money;

if (! function_exists('pkr')) {
    function pkr(float|int|string $amount): string
    {
        return Money::format($amount);
    }
}
