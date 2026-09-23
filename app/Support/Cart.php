<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

final class Cart
{
    public const SESSION_KEY = 'dezato.cart';

    public function __construct(private readonly Session $session) {}

    /**
     * @return array<string, array<string, mixed>>
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

        if (isset($items[$productId]) && ($items[$productId]['type'] ?? 'catalog') === 'catalog') {
            $items[$productId]['quantity'] = min(99, (int) $items[$productId]['quantity'] + $quantity);
        } else {
            $items[$productId] = [
                'type' => 'catalog',
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $this->session->put(self::SESSION_KEY, $items);
    }

    /**
     * @param  array{
     *     key: string,
     *     name: string,
     *     unit_price: int|float,
     *     summary?: string|null,
     *     options?: array<string, mixed>,
     *     image?: string|null
     * }  $custom
     */
    public function addCustom(array $custom, int $quantity = 1): string
    {
        $key = $custom['key'];
        $quantity = max(1, min(99, $quantity));
        $items = $this->items();

        $items[$key] = [
            'type' => 'custom',
            'product_id' => $key,
            'quantity' => $quantity,
            'name' => $custom['name'],
            'unit_price' => (float) $custom['unit_price'],
            'summary' => $custom['summary'] ?? null,
            'options' => $custom['options'] ?? [],
            'image' => $custom['image'] ?? null,
        ];

        $this->session->put(self::SESSION_KEY, $items);

        return $key;
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
     *     product: array<string, mixed>,
     *     line_total: float,
     *     type: string,
     *     options: ?array,
     *     is_custom: bool
     * }>
     */
    public function lines(): Collection
    {
        return collect($this->items())
            ->map(function (array $item): ?array {
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $type = (string) ($item['type'] ?? 'catalog');

                if ($type === 'custom') {
                    $unitPrice = (float) ($item['unit_price'] ?? 0);
                    $image = $item['image'] ?? null;
                    $publicImage = $image && ! str_starts_with((string) $image, 'http')
                        ? (str_starts_with((string) $image, 'storage/') ? $image : 'storage/'.$image)
                        : 'images/home/hero.jpg';

                    return [
                        'product_id' => (string) $item['product_id'],
                        'quantity' => $quantity,
                        'type' => 'custom',
                        'is_custom' => true,
                        'options' => is_array($item['options'] ?? null) ? $item['options'] : [],
                        'image_path' => $item['image'] ?? null,
                        'product' => [
                            'id' => (string) $item['product_id'],
                            'name' => (string) ($item['name'] ?? 'Custom Cake'),
                            'price' => $unitPrice,
                            'image' => $publicImage,
                            'description' => (string) ($item['summary'] ?? ''),
                        ],
                        'line_total' => round($unitPrice * $quantity, 2),
                    ];
                }

                $product = Catalog::findProduct((string) $item['product_id']);

                if ($product === null) {
                    return null;
                }

                return [
                    'product_id' => (string) $item['product_id'],
                    'quantity' => $quantity,
                    'type' => 'catalog',
                    'is_custom' => false,
                    'options' => null,
                    'image_path' => null,
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
