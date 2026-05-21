<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Services\CatalogService;

class HomeController extends BaseController
{
    public function index(): string
    {
        helper(['currency', 'text']);

        $catalog = new CatalogService();

        return view('public/home', [
            'pageTitle' => 'So sánh giá sách trực tuyến',
            'metaDescription' => 'DealSach giúp tìm giá sách tốt nhất từ Fahasa, Tiki, Shopee và Nhà sách Phương Nam.',
            'stats' => $catalog->homepageStats(),
            'featuredBooks' => $catalog->featuredBooks(8),
            'retailers' => $catalog->activeRetailers(),
        ]);
    }
}
