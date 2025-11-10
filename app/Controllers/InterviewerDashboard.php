<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\SystemLogModel;

class InterviewerDashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is interviewer
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();

        $userId = (int) session()->get('user_id');

        // Use rolling 7-day window for trend calculations
        $now = new \DateTime('now');
        $currentStart = (clone $now)->modify('-7 days');
        $prevStart = (clone $currentStart)->modify('-7 days');

        // Helper closure to compute trend percentage (vs previous period)
        $trend = function (int $cur, int $prev): int {
            if ($prev <= 0) { return $cur > 0 ? 100 : 0; }
            return (int) round((($cur - $prev) / $prev) * 100);
        };

        // Compute totals scoped to this interviewer
        $totalAllTime = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->countAllResults();

        $igtAllTime = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('company_name', 'IGT')
            ->countAllResults();

        $everiseAllTime = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('company_name', 'Everise')
            ->countAllResults();

        // Windowed counts for trends
        $totalCur = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('created_at >=', $currentStart->format('Y-m-d H:i:s'))
            ->where('created_at <', $now->format('Y-m-d H:i:s'))
            ->countAllResults();
        $totalPrev = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('created_at >=', $prevStart->format('Y-m-d H:i:s'))
            ->where('created_at <', $currentStart->format('Y-m-d H:i:s'))
            ->countAllResults();

        $igtCur = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('company_name', 'IGT')
            ->where('created_at >=', $currentStart->format('Y-m-d H:i:s'))
            ->where('created_at <', $now->format('Y-m-d H:i:s'))
            ->countAllResults();
        $igtPrev = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('company_name', 'IGT')
            ->where('created_at >=', $prevStart->format('Y-m-d H:i:s'))
            ->where('created_at <', $currentStart->format('Y-m-d H:i:s'))
            ->countAllResults();

        $everiseCur = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('company_name', 'Everise')
            ->where('created_at >=', $currentStart->format('Y-m-d H:i:s'))
            ->where('created_at <', $now->format('Y-m-d H:i:s'))
            ->countAllResults();
        $everisePrev = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->where('company_name', 'Everise')
            ->where('created_at >=', $prevStart->format('Y-m-d H:i:s'))
            ->where('created_at <', $currentStart->format('Y-m-d H:i:s'))
            ->countAllResults();

        // Get recent applications (scoped to interviewer)
        $recent = (new ApplicationModel())
            ->where('interviewed_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        // Package data for the view
        $data = [
            'total_applications' => $totalAllTime,
            'igt_applications' => $igtAllTime,
            'rsd_applications' => $everiseAllTime,
            'recent_applications' => $recent,
            // Trends (positive => up, negative => down)
            'trend_total' => $trend($totalCur, $totalPrev),
            'trend_rsd' => $trend($everiseCur, $everisePrev),
            'trend_igt' => $trend($igtCur, $igtPrev),
            // Time window labels if needed later
            'trend_window_label' => 'Last 7 days vs previous 7 days',
        ];

            // Use clean dashboard view (isolated from legacy corruption)
        return view('interviewer/dashboard2', $data);
    }
}   
