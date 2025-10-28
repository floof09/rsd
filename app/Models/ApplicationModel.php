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
        'company_name',
        'full_name',
        'email_address',
        'phone_number',
        'viber_number',
        'address',
        'birthdate',
        'bpo_experience',
        'educational_attainment',
        'recruiter_email',
        'interviewed_by',
        'status',
        'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_name' => 'required|min_length[2]',
        'full_name' => 'required|min_length[2]',
        'email_address' => 'required|valid_email',
        'phone_number' => 'permit_empty|min_length[10]',
        'address' => 'permit_empty',
        'educational_attainment' => 'permit_empty',
        'bpo_experience' => 'permit_empty',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
}
