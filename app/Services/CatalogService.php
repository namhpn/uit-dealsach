<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class CatalogService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return array{books: list<array<string, mixed>>, total: int, page: int, perPage: int, totalPages: int}
     */
    public function searchBooks(array $filters, int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $base = $this->baseBookQuery($filters);
        $total = (int) $base->select('COUNT(DISTINCT books.id) AS total', false)->get()->getRow('total');

        $rows = $this->baseBookQuery($filters)
            ->select([
                'books.id',
                'books.title',
                'books.slug',
                'books.isbn',
                'books.format',
                'books.language',
                'books.description',
                'books.cover_image_url',
                'books.updated_at',
                'publishers.name AS publisher_name',
                'GROUP_CONCAT(DISTINCT authors.name ORDER BY authors.name SEPARATOR ", ") AS authors',
                'GROUP_CONCAT(DISTINCT categories.name ORDER BY book_categories.is_primary DESC, categories.name SEPARATOR ", ") AS categories',
                'COUNT(DISTINCT retailer_items.id) AS offer_count',
                'COUNT(DISTINCT CASE WHEN retailer_items.in_stock = 1 AND retailer_items.is_active = 1 THEN retailer_items.id END) AS available_offer_count',
                'MIN(CASE WHEN retailer_items.in_stock = 1 AND retailer_items.is_active = 1 THEN retailer_items.current_effective_price END) AS lowest_price',
                'MAX(retailer_items.last_crawled_at) AS last_crawled_at',
            ])
            ->groupBy('books.id')
            ->orderBy('MIN(CASE WHEN retailer_items.in_stock = 1 AND retailer_items.is_active = 1 THEN retailer_items.current_effective_price END) IS NULL', 'ASC', false)
            ->orderBy('MIN(CASE WHEN retailer_items.in_stock = 1 AND retailer_items.is_active = 1 THEN retailer_items.current_effective_price END)', 'ASC', false)
            ->orderBy('books.updated_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return [
            'books' => array_map([$this, 'normalizeBookRow'], $rows),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function featuredBooks(int $limit = 8): array
    {
        return $this->searchBooks([], 1, $limit)['books'];
    }

    public function findBookBySlug(string $slug): ?array
    {
        $book = $this->baseBookQuery([])
            ->select([
                'books.*',
                'publishers.name AS publisher_name',
                'GROUP_CONCAT(DISTINCT authors.name ORDER BY authors.name SEPARATOR ", ") AS authors',
                'GROUP_CONCAT(DISTINCT categories.name ORDER BY book_categories.is_primary DESC, categories.name SEPARATOR ", ") AS categories',
            ])
            ->where('books.slug', $slug)
            ->groupBy('books.id')
            ->get()
            ->getRowArray();

        if ($book === null) {
            return null;
        }

        $book = $this->normalizeBookRow($book);
        $offers = (new ComparisonService($this->db))->offersForBook((int) $book['id']);
        $lowest = null;

        foreach ($offers as $offer) {
            if ((bool) $offer['in_stock'] && $offer['current_effective_price'] !== null) {
                $price = (int) $offer['current_effective_price'];
                $lowest = $lowest === null ? $price : min($lowest, $price);
            }
        }

        $book['offers'] = $offers;
        $book['lowest_price'] = $lowest;
        $book['last_crawled_at'] = $this->latestCrawlForBook((int) $book['id']);

        return $book;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function activeRetailers(): array
    {
        return $this->db->table('retailers')
            ->select('id, name, slug, website_url')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function activeCategories(): array
    {
        return $this->db->table('categories')
            ->select('id, name, slug')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * @return array<string, int>
     */
    public function homepageStats(): array
    {
        return [
            'books' => (int) $this->db->table('books')->where('is_active', 1)->where('deleted_at', null)->countAllResults(),
            'retailers' => (int) $this->db->table('retailers')->where('is_active', 1)->countAllResults(),
            'offers' => (int) $this->db->table('retailer_items')->where('is_active', 1)->countAllResults(),
            'trackingRules' => (int) $this->db->table('tracking_rules')->where('is_active', 1)->countAllResults(),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function baseBookQuery(array $filters): \CodeIgniter\Database\BaseBuilder
    {
        $builder = $this->db->table('books')
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->join('book_authors', 'book_authors.book_id = books.id', 'left')
            ->join('authors', 'authors.id = book_authors.author_id', 'left')
            ->join('book_categories', 'book_categories.book_id = books.id', 'left')
            ->join('categories', 'categories.id = book_categories.category_id', 'left')
            ->join('retailer_items', 'retailer_items.book_id = books.id', 'left')
            ->join('retailers', 'retailers.id = retailer_items.retailer_id', 'left')
            ->where('books.is_active', 1)
            ->where('books.deleted_at', null);

        $keyword = trim((string) ($filters['q'] ?? ''));
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('books.title', $keyword)
                ->orLike('books.isbn', $keyword)
                ->orLike('authors.name', $keyword)
                ->groupEnd();
        }

        $retailers = array_values(array_filter((array) ($filters['retailer'] ?? [])));
        if ($retailers !== []) {
            $builder->whereIn('retailers.slug', $retailers);
        }

        $category = trim((string) ($filters['category'] ?? ''));
        if ($category !== '') {
            $builder->where('categories.slug', $category);
        }

        $stock = (string) ($filters['stock'] ?? '');
        if ($stock === 'in_stock') {
            $builder->where('retailer_items.in_stock', 1);
        } elseif ($stock === 'out_of_stock') {
            $builder->where('retailer_items.in_stock', 0);
        }

        return $builder;
    }

    private function latestCrawlForBook(int $bookId): ?string
    {
        $value = $this->db->table('retailer_items')
            ->selectMax('last_crawled_at')
            ->where('book_id', $bookId)
            ->get()
            ->getRow('last_crawled_at');

        return $value ?: null;
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private function normalizeBookRow(array $row): array
    {
        $row['authors'] = $row['authors'] ?: 'Chưa rõ tác giả';
        $row['categories'] = $row['categories'] ?: '';
        $row['publisher_name'] = $row['publisher_name'] ?: 'Chưa rõ NXB';
        $row['lowest_price'] = isset($row['lowest_price']) ? (int) $row['lowest_price'] : null;
        $row['offer_count'] = isset($row['offer_count']) ? (int) $row['offer_count'] : 0;
        $row['available_offer_count'] = isset($row['available_offer_count']) ? (int) $row['available_offer_count'] : 0;

        return $row;
    }
}
