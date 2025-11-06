<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemLogModel extends Model
{
    protected $table = 'system_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    /**
     * Log a system activity
     */
    public function logActivity($action, $module, $description = null, $userId = null)
    {
        $data = [
            'user_id' => $userId ?? session()->get('user_id'),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Be defensive: if the system_logs table is missing or unavailable,
        // avoid throwing and crashing auth flows. Fall back to file logging.
        try {
            return $this->insert($data);
        } catch (\Throwable $e) {
            // Log to file instead of failing the request
            $safeMsg = sprintf(
                'SystemLog DB write failed: %s | action=%s module=%s user_id=%s',
                $e->getMessage(),
                (string) $action,
                (string) $module,
                (string) ($data['user_id'] ?? 'null')
            );
            if (function_exists('log_message')) {
                log_message('error', $safeMsg);
            }
            return false;
        }
    }

    /**
     * Get logs with user information
     */
    public function getLogsWithUsers($limit = 100, $offset = 0)
    {
        return $this->select('system_logs.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->orderBy('system_logs.created_at', 'DESC')
            ->limit($limit, $offset)
            ->find();
    }

    /**
     * Get logs by module
     */
    public function getLogsByModule($module, $limit = 50)
    {
        return $this->where('module', $module)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get logs by user
     */
    public function getLogsByUser($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity($limit = 10)
    {
        return $this->getLogsWithUsers($limit, 0);
    }
}
