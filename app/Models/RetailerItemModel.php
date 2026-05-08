<?php

namespace App\Models;

use CodeIgniter\Model;

class RetailerItemModel extends Model
{
    protected $table = 'retailer_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'book_id',
        'retailer_id',
        'url',
        'original_id',
        'current_listed_price',
        'current_discounted_price',
        'current_effective_price',
        'in_stock',
        'is_active',
        'last_crawled_at',
    ];
    protected $useTimestamps = true;
}
