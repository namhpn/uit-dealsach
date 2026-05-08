<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class ComparisonService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function offersForBook(int $bookId): array
    {
        $offers = $this->db->table('retailer_items')
            ->select([
                'retailer_items.*',
                'retailers.name AS retailer_name',
                'retailers.slug AS retailer_slug',
            ])
            ->join('retailers', 'retailers.id = retailer_items.retailer_id')
            ->where('retailer_items.book_id', $bookId)
            ->where('retailer_items.is_active', 1)
            ->orderBy('retailer_items.in_stock', 'DESC')
            ->orderBy('retailer_items.current_effective_price', 'ASC')
            ->get()
            ->getResultArray();

        return $this->markLowestAvailable($offers);
    }

    /**
     * @param list<array<string, mixed>> $offers
     *
     * @return list<array<string, mixed>>
     */
    public function markLowestAvailable(array $offers): array
    {
        $lowest = null;
        foreach ($offers as $offer) {
            if (! (bool) ($offer['in_stock'] ?? false)) {
                continue;
            }

            $price = $offer['current_effective_price'] ?? $offer['effective_price'] ?? null;
            if ($price === null || (int) $price <= 0) {
                continue;
            }

            $lowest = $lowest === null ? (int) $price : min($lowest, (int) $price);
        }

        foreach ($offers as &$offer) {
            $price = $offer['current_effective_price'] ?? $offer['effective_price'] ?? null;
            $offer['is_lowest_available'] = $lowest !== null
                && (bool) ($offer['in_stock'] ?? false)
                && (int) $price === $lowest;
            $offer['lowest_available_price'] = $lowest;
        }

        return $offers;
    }

    public function effectivePrice(?int $listedPrice, ?int $discountedPrice): ?int
    {
        if ($discountedPrice !== null && $discountedPrice > 0) {
            return $discountedPrice;
        }

        if ($listedPrice !== null && $listedPrice > 0) {
            return $listedPrice;
        }

        return null;
    }
}
