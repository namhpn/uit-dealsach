<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminAuthLogModel extends Model
{
    protected $table = 'admin_auth_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['admin_id', 'ip_address', 'user_agent', 'status', 'created_at'];
    protected $useTimestamps = false;
}
