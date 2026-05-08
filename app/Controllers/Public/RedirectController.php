<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;

class RedirectController extends BaseController
{
    public function go(int $id)
    {
        $db = Database::connect();
        $item = $db->table('retailer_items')
            ->select('id, url, is_active')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if ($item === null || ! (bool) $item['is_active'] || empty($item['url'])) {
            throw PageNotFoundException::forPageNotFound('Không tìm thấy liên kết nhà bán.');
        }

        $db->table('outbound_clicks')->insert([
            'retailer_item_id' => (int) $item['id'],
            'ip_address' => (string) $this->request->getIPAddress(),
            'user_agent' => (string) $this->request->getUserAgent(),
            'clicked_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to($item['url']);
    }
}
