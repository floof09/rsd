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
        'company_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email_address',
        'phone_number',
        'viber_number',
        'street_address',
        'barangay',
        'municipality',
        'province',
        'birthdate',
        'bpo_experience',
        'educational_attainment',
        'resume_path',
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
        'first_name' => 'required|min_length[2]',
        'middle_name' => 'permit_empty|max_length[100]',
        'last_name' => 'required|min_length[2]',
        'suffix' => 'permit_empty|max_length[20]',
        'email_address' => 'required|valid_email',
        'phone_number' => 'permit_empty|min_length[10]',
        'street_address' => 'permit_empty',
        'educational_attainment' => 'permit_empty',
        'bpo_experience' => 'permit_empty',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
}
