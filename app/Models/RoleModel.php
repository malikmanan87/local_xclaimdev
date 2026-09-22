<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'display_name', 'description', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    // ----------------------------------------------------------------
    // Get all roles along with user counts
    // ----------------------------------------------------------------
    public function getRolesWithUserCount(): array
    {
        return $this->db->table('roles r')
            ->select('r.*, COUNT(u.id) AS user_count')
            ->join('users u', 'u.role_id = r.id AND u.deleted_at IS NULL', 'left')
            ->groupBy('r.id')
            ->orderBy('r.id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
