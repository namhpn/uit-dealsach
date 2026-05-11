<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookModel;
use Config\Database;

class BookCrudController extends BaseController
{
    private BookModel $books;

    public function __construct()
    {
        $this->books = new BookModel();
    }

    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $builder = $this->books
            ->select('books.*, publishers.name AS publisher_name')
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->orderBy('books.updated_at', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('books.title', $q)
                ->orLike('books.isbn', $q)
                ->groupEnd();
        }

        return view('admin/books/index', [
            'pageTitle' => 'Quản lý sách',
            'books' => $builder->paginate(12),
            'pager' => $this->books->pager,
            'q' => $q,
        ]);
    }

    public function new(): string
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
        $data = $this->validatedPayload();
        if ($data === null) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng kiểm tra lại thông tin sách.');
        }

        $data['slug'] = $this->uniqueSlug((string) $data['title']);
        $this->books->insert($data);

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books'))->with('success', 'Đã thêm sách mới.');
    }

    public function show(int $id)
    {
        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books/' . $id . '/edit'));
    }

    public function edit(int $id): string
    {
        $book = $this->books->find($id);
        if ($book === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Không tìm thấy sách.');
        }

        return view('admin/books/form', [
            'pageTitle' => 'Sửa sách',
            'book' => $book,
            'publishers' => $this->publishers(),
            'action' => site_url(env('dealsach.adminPath', 'ds-admin') . '/books/' . $id),
        ]);
    }

    public function update(int $id)
    {
        $book = $this->books->find($id);
        if ($book === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Không tìm thấy sách.');
        }

        $data = $this->validatedPayload();
        if ($data === null) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng kiểm tra lại thông tin sách.');
        }

        $newSlug = $this->slugify((string) $data['title']);
        if ($newSlug !== (string) $book['slug']) {
            $data['slug'] = $this->uniqueSlug((string) $data['title'], $id);
        }

        $this->books->update($id, $data);

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books'))->with('success', 'Đã cập nhật sách.');
    }

    public function delete(int $id)
    {
        if ($this->books->find($id) === null) {
            return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books'))->with('error', 'Không tìm thấy sách cần xóa.');
        }

        $this->books->delete($id);

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/books'))->with('success', 'Đã xóa sách khỏi danh sách demo.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function validatedPayload(): ?array
    {
        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
            'isbn' => 'permit_empty|max_length[20]',
            'publisher_id' => 'required|integer',
            'format' => 'permit_empty|max_length[50]',
            'language' => 'permit_empty|max_length[50]',
            'description' => 'permit_empty',
            'cover_image_url' => 'permit_empty|valid_url_strict|max_length[255]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        return [
            'title' => trim((string) $this->request->getPost('title')),
            'isbn' => trim((string) $this->request->getPost('isbn')) ?: null,
            'publisher_id' => (int) $this->request->getPost('publisher_id'),
            'format' => trim((string) $this->request->getPost('format')) ?: 'Bìa mềm',
            'language' => trim((string) $this->request->getPost('language')) ?: 'Tiếng Việt',
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'cover_image_url' => trim((string) $this->request->getPost('cover_image_url')) ?: null,
            'is_active' => (bool) $this->request->getPost('is_active'),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function publishers(): array
    {
        return Database::connect()->table('publishers')->where('is_active', 1)->orderBy('name', 'ASC')->get()->getResultArray();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = $this->slugify($title);
        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function slugify(string $title): string
    {
        helper('text');
        $slug = url_title(convert_accented_characters($title), '-', true);
        return $slug !== '' ? $slug : 'sach-' . time();
    }

    private function slugExists(string $slug, ?int $ignoreId): bool
    {
        $builder = $this->books->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->withDeleted()->countAllResults() > 0;
    }
}
