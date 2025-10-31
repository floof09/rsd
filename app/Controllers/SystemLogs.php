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
        
        $data = [
            'logs' => $logModel->getLogsWithUsers(100),
            'total_logs' => $logModel->countAll(),
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
        
        $data = [
            'logs' => $logModel->getLogsByModule($module),
            'total_logs' => $logModel->where('module', $module)->countAllResults(),
            'filter_module' => $module,
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
