<?php

namespace App\Controllers;

use App\Models\SystemLogModel;

class SystemLogs extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $logModel = new SystemLogModel();
        
        // Pagination settings
        $perPage = (int) ($this->request->getGet('perPage') ?? 25);
        if ($perPage <= 0) { $perPage = 25; }

        // Build query with user join and order, then paginate
        $logs = $logModel
            ->select('system_logs.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->orderBy('system_logs.created_at', 'DESC')
            ->paginate($perPage, 'logs');

        $data = [
            'logs' => $logs,
            'total_logs' => $logModel->countAll(),
            'pager' => $logModel->pager,
            'perPage' => $perPage,
        ];

        return view('admin/system_logs', $data);
    }

    /**
     * Filter logs by module
     */
    public function filterByModule($module)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $logModel = new SystemLogModel();
        
        $perPage = (int) ($this->request->getGet('perPage') ?? 25);
        if ($perPage <= 0) { $perPage = 25; }

        $logs = $logModel
            ->select('system_logs.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->where('system_logs.module', $module)
            ->orderBy('system_logs.created_at', 'DESC')
            ->paginate($perPage, 'logs');

        $data = [
            'logs' => $logs,
            'total_logs' => (clone $logModel)->where('module', $module)->countAllResults(),
            'filter_module' => $module,
            'pager' => $logModel->pager,
            'perPage' => $perPage,
        ];

        return view('admin/system_logs', $data);
    }

    /**
     * Clear old logs (older than 30 days)
     */
    public function clearOldLogs()
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $logModel = new SystemLogModel();
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        $deleted = $logModel->where('created_at <', $thirtyDaysAgo)->delete();

        return redirect()->to('admin/system-logs')->with('success', "Deleted $deleted old log entries.");
    }
}
