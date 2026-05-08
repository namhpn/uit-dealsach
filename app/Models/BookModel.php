<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'title',
        'slug',
        'isbn',
        'format',
        'language',
        'description',
        'publisher_id',
        'cover_image_url',
        'is_active',
        'deleted_at',
    ];
    protected $useTimestamps = true;
}
