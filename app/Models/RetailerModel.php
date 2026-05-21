<?php

namespace App\Models;

use CodeIgniter\Model;

class RetailerModel extends Model
{
    protected $table = 'retailers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'slug',
        'website_url',
        'logo_url',
        'is_active',
    ];
    protected $useTimestamps = true;
}
