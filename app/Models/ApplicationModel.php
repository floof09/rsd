<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'first_name',
        'last_name',
        'contact_number',
        'email_address',
        'home_address',
        'municipality',
        'educational_attainment',
        'bpo_experience',
        'status',
        'reviewed_by',
        'review_notes',
        'reviewed_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|min_length[2]',
        'last_name' => 'required|min_length[2]',
        'contact_number' => 'required|min_length[10]',
        'email_address' => 'required|valid_email',
        'municipality' => 'required',
        'educational_attainment' => 'required',
        'bpo_experience' => 'required',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getApplicationWithUser($applicationId)
    {
        return $this->select('applications.*, users.email as user_email')
                    ->join('users', 'users.id = applications.user_id')
                    ->where('applications.id', $applicationId)
                    ->first();
    }

    public function getPendingApplications()
    {
        return $this->where('status', 'pending')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getUserApplication($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function approveApplication($applicationId, $adminId, $notes = null)
    {
        return $this->update($applicationId, [
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'review_notes' => $notes,
            'reviewed_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function rejectApplication($applicationId, $adminId, $notes = null)
    {
        return $this->update($applicationId, [
            'status' => 'rejected',
            'reviewed_by' => $adminId,
            'review_notes' => $notes,
            'reviewed_at' => date('Y-m-d H:i:s')
        ]);
    }
}
