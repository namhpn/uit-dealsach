<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Services\CatalogService;
use CodeIgniter\Exceptions\PageNotFoundException;

class BookController extends BaseController
{
    public function index(): string
    {
        helper(['currency', 'text']);

        $catalog = new CatalogService();
        $filters = [
            'q' => trim((string) $this->request->getGet('q')),
            'retailer' => (array) $this->request->getGet('retailer'),
            'category' => trim((string) $this->request->getGet('category')),
            'stock' => trim((string) $this->request->getGet('stock')),
        ];
        $page = (int) ($this->request->getGet('page') ?? 1);
        $result = $catalog->searchBooks($filters, $page, 12);

        return view('public/catalog', [
            'pageTitle' => 'Danh mục sách',
            'metaDescription' => 'Tìm kiếm, lọc và so sánh giá sách từ nhiều nhà bán tại Việt Nam.',
            'filters' => $filters,
            'result' => $result,
            'retailers' => $catalog->activeRetailers(),
            'categories' => $catalog->activeCategories(),
            'queryParams' => $this->request->getGet() ?? [],
        ]);
    }

    public function show(string $slug): string
    {
        helper(['currency', 'text']);

        $book = (new CatalogService())->findBookBySlug($slug);
        if ($book === null) {
            throw PageNotFoundException::forPageNotFound('Không tìm thấy sách.');
        }

        return view('public/detail', [
            'pageTitle' => $book['title'],
            'metaDescription' => character_limiter(strip_tags((string) ($book['description'] ?? 'So sánh giá sách ' . $book['title'])), 155),
            'book' => $book,
        ]);
    }
}
