<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Support\Collection;

class KitchenService
{
    /**
     * Nama stasiun produksi untuk sebuah menu berdasarkan kategori menu.
     */
    public function stationFor(Menu $menu): string
    {
        $category = strtolower((string) $menu->category);

        foreach (config('kitchen.stations', []) as $station => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($category, $keyword)) {
                    return $station;
                }
            }
        }

        return config('kitchen.default', 'Dapur Utama');
    }

    /**
     * Grup item sebuah pesanan per stasiun dapur.
     *
     * @return array<int, array{station: string, items: Collection}>
     */
    public function groupOrderByStation(Order $order): array
    {
        $groups = [];

        foreach ($order->items as $item) {
            $station = $this->stationFor($item->menu);
            $groups[$station][] = $item;
        }

        return collect($groups)
            ->map(fn ($items, $station) => [
                'station' => $station,
                'items' => collect($items),
            ])
            ->values()
            ->all();
    }

    /**
     * Grup beberapa pesanan per stasiun untuk KDS.
     *
     * @return array<int, array{station: string, entries: array<int, array{order: Order, items: Collection}>}>
     */
    public function groupOrdersByStation($orders): array
    {
        $groups = [];

        foreach ($orders as $order) {
            foreach ($this->groupOrderByStation($order) as $group) {
                $groups[$group['station']][] = [
                    'order' => $order,
                    'items' => $group['items'],
                ];
            }
        }

        return collect($groups)
            ->map(fn ($entries, $station) => [
                'station' => $station,
                'entries' => $entries,
            ])
            ->values()
            ->all();
    }
}
