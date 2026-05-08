<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookModel;
use Config\Database;

class BookCrudController extends BaseController
{
    public function index(): string
    {
        helper('currency');

        $q = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 12;
        $db = Database::connect();

        $base = $db->table('books')
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->where('books.deleted_at', null);

        if ($q !== '') {
            $base->groupStart()->like('books.title', $q)->orLike('books.isbn', $q)->groupEnd();
        }

        $total = (int) $base->select('COUNT(DISTINCT books.id) AS total', false)->get()->getRow('total');

        $builder = $db->table('books')
            ->select('books.*, publishers.name AS publisher_name, COUNT(DISTINCT retailer_items.id) AS offer_count, MIN(CASE WHEN retailer_items.in_stock = 1 THEN retailer_items.current_effective_price END) AS lowest_price')
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->join('retailer_items', 'retailer_items.book_id = books.id AND retailer_items.is_active = 1', 'left')
            ->where('books.deleted_at', null);

        if ($q !== '') {
            $builder->groupStart()->like('books.title', $q)->orLike('books.isbn', $q)->groupEnd();
        }

        $books = $builder
            ->groupBy('books.id')
            ->orderBy('books.updated_at', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return view('admin/books/index', [
            'pageTitle' => 'Quản lý sách',
            'books' => $books,
            'q' => $q,
            'total' => $total,
            'page' => $page,
            'totalPages' => (int) max(1, ceil($total / $perPage)),
        ]);
    }

    public function createForm(): string
    {
        return view('admin/books/form', [
            'pageTitle' => 'Thêm sách',
            'book' => null,
            'publishers' => $this->publishers(),
            'action' => site_url(env('dealsach.adminPath', 'ds-admin') . '/books'),
        ]);
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng kiểm tra lại thông tin sách.');
        }

        (new BookModel())->insert($this->payload());

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books'))->with('success', 'Đã thêm sách mới.');
    }

    public function show(int $id): string
    {
        helper('currency');

        $book = $this->book($id);
        $db = Database::connect();
        $offers = $db->table('retailer_items')
            ->select('retailer_items.*, retailers.name AS retailer_name')
            ->join('retailers', 'retailers.id = retailer_items.retailer_id')
            ->where('book_id', $id)
            ->orderBy('retailer_name', 'ASC')
            ->get()
            ->getResultArray();

        $snapshots = $db->table('price_snapshots')
            ->select('price_snapshots.*, retailers.name AS retailer_name')
            ->join('retailer_items', 'retailer_items.id = price_snapshots.retailer_item_id')
            ->join('retailers', 'retailers.id = retailer_items.retailer_id')
            ->where('retailer_items.book_id', $id)
            ->orderBy('price_snapshots.id', 'DESC')
            ->get(12)
            ->getResultArray();

        return view('admin/books/show', [
            'pageTitle' => 'Chi tiết sách',
            'book' => $book,
            'offers' => $offers,
            'snapshots' => $snapshots,
        ]);
    }

    public function edit(int $id): string
    {
        return view('admin/books/form', [
            'pageTitle' => 'Sửa sách',
            'book' => $this->book($id),
            'publishers' => $this->publishers(),
            'action' => site_url(env('dealsach.adminPath', 'ds-admin') . '/books/' . $id),
        ]);
    }

    public function update(int $id)
    {
        $this->book($id);

        if (! $this->validate($this->rules($id))) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng kiểm tra lại thông tin sách.');
        }

        (new BookModel())->update($id, $this->payload());

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books/' . $id))->with('success', 'Đã cập nhật sách.');
    }

    public function delete(int $id)
    {
        $this->book($id);
        (new BookModel())->delete($id);

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books'))->with('success', 'Đã xóa sách.');
    }

    /**
     * @return array<string, string>
     */
    private function rules(?int $id = null): array
    {
        $slugRule = 'required|min_length[3]|max_length[255]|is_unique[books.slug]';
        if ($id !== null) {
            $slugRule = 'required|min_length[3]|max_length[255]|is_unique[books.slug,id,' . $id . ']';
        }

        return [
            'title' => 'required|min_length[2]|max_length[255]',
            'slug' => $slugRule,
            'isbn' => 'permit_empty|max_length[20]',
            'publisher_id' => 'permit_empty|integer',
            'format' => 'permit_empty|max_length[50]',
            'language' => 'required|max_length[50]',
            'description' => 'permit_empty',
            'cover_image_url' => 'permit_empty|valid_url_strict|max_length[255]',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'title' => trim((string) $this->request->getPost('title')),
            'slug' => trim((string) $this->request->getPost('slug')),
            'isbn' => trim((string) $this->request->getPost('isbn')) ?: null,
            'format' => trim((string) $this->request->getPost('format')) ?: null,
            'language' => trim((string) $this->request->getPost('language')) ?: 'Tiếng Việt',
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'publisher_id' => $this->request->getPost('publisher_id') ?: null,
            'cover_image_url' => trim((string) $this->request->getPost('cover_image_url')) ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function book(int $id): array
    {
        $book = Database::connect()->table('books')
            ->select('books.*, publishers.name AS publisher_name')
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->where('books.id', $id)
            ->where('books.deleted_at', null)
            ->get()
            ->getRowArray();

        if ($book === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Không tìm thấy sách.');
        }

        return $book;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function publishers(): array
    {
        return Database::connect()->table('publishers')
            ->select('id, name')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
