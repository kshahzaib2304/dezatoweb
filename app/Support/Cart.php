<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

final class Cart
{
    public const SESSION_KEY = 'dezato.cart';

    public function __construct(private readonly Session $session) {}

    /**
     * @return array<string, array{product_id: string, quantity: int}>
     */
    public function items(): array
    {
        $items = $this->session->get(self::SESSION_KEY, []);

        return is_array($items) ? $items : [];
    }

    public function count(): int
    {
        return (int) collect($this->items())->sum('quantity');
    }

    public function add(string $productId, int $quantity = 1): void
    {
        $quantity = max(1, min(99, $quantity));
        $items = $this->items();

        if (isset($items[$productId])) {
            $items[$productId]['quantity'] = min(99, $items[$productId]['quantity'] + $quantity);
        } else {
            $items[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $this->session->put(self::SESSION_KEY, $items);
    }

    public function update(string $productId, int $quantity): void
    {
        $items = $this->items();

        if (! isset($items[$productId])) {
            return;
        }

        if ($quantity < 1) {
            unset($items[$productId]);
        } else {
            $items[$productId]['quantity'] = min(99, $quantity);
        }

        $this->session->put(self::SESSION_KEY, $items);
    }

    public function remove(string $productId): void
    {
        $items = $this->items();
        unset($items[$productId]);
        $this->session->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<int, array{
     *     product_id: string,
     *     quantity: int,
     *     product: array,
     *     line_total: float
     * }>
     */
    public function lines(): Collection
    {
        return collect($this->items())
            ->map(function (array $item): ?array {
                $product = Catalog::findProduct($item['product_id']);

                if ($product === null) {
                    return null;
                }

                $quantity = (int) $item['quantity'];

                return [
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'product' => $product,
                    'line_total' => round((float) $product['price'] * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    public function subtotal(): float
    {
        return round((float) $this->lines()->sum('line_total'), 2);
    }
}
