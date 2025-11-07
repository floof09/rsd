<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'status', 'form_schema',
    ];

    protected $useTimestamps = true;
    protected $dateFormat   = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[150]',
        'status' => 'permit_empty|in_list[active,inactive]',
    ];

    public function activeList(): array
    {
        return $this->where('status', 'active')->orderBy('name', 'ASC')->findAll();
    }

    public function getSchemaArray(?int $id): array
    {
        if (!$id) return [];
        $row = $this->select('form_schema')->find($id);
        if (!$row || empty($row['form_schema'])) return [];
        $decoded = json_decode($row['form_schema'], true);
        return is_array($decoded) ? $decoded : [];
    }
}
