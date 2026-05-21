<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;

class RedirectController extends BaseController
{
    public function go(int $id)
    {
        helper('url');

        if ($id <= 0) {
            throw PageNotFoundException::forPageNotFound('Liên kết nhà bán không hợp lệ.');
        }

        $db = Database::connect();
        $item = $db->table('retailer_items')
            ->select([
                'retailer_items.id',
                'retailer_items.book_id',
                'retailer_items.url',
                'retailer_items.is_active',
                'books.slug AS book_slug',
            ])
            ->join('books', 'books.id = retailer_items.book_id')
            ->where('retailer_items.id', $id)
            ->get()
            ->getRowArray();

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound('Không tìm thấy liên kết nhà bán.');
        }

        if (! (bool) $item['is_active'] || empty($item['url'])) {
            return redirect()
                ->to(site_url('sach/' . $item['book_slug']))
                ->with('error', 'Liên kết nhà bán hiện không khả dụng. Vui lòng chọn nhà bán khác.');
        }

        $ipAddress = (string) $this->request->getIPAddress();
        $db->table('outbound_clicks')->insert([
            'retailer_item_id' => (int) $item['id'],
            'book_id' => (int) $item['book_id'],
            'ip_address' => $ipAddress,
            'ip_hash' => hash('sha256', $ipAddress . '|' . (string) (env('encryption.key') ?: env('app.baseURL', 'dealsach'))),
            'user_agent' => (string) $this->request->getUserAgent(),
            'referrer' => (string) ($this->request->getServer('HTTP_REFERER') ?? ''),
            'clicked_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to($item['url']);
    }
}
