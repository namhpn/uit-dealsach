<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class AjaxController extends BaseController
{
    public function bookSearch()
    {
        $q = trim((string) $this->request->getGet('q'));
        $builder = Database::connect()->table('books')
            ->select('id, title, slug, isbn')
            ->where('deleted_at', null)
            ->orderBy('title', 'ASC')
            ->limit(8);

        if ($q !== '') {
            $builder->groupStart()
                ->like('title', $q)
                ->orLike('isbn', $q)
                ->groupEnd();
        }

        return $this->response->setJSON($builder->get()->getResultArray());
    }
}
