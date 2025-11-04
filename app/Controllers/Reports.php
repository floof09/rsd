<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Reports extends BaseController
{
    public function index()
    {
        // Admin only
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        return view('admin/reports');
    }

    public function data()
    {
        // Admin only
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $db = \Config\Database::connect();
        $days = (int) ($this->request->getGet('days') ?? 30);
        if (!in_array($days, [7, 30, 90], true)) { $days = 30; }

        // Status breakdown
        $startDT = new \DateTime('-' . ($days - 1) . ' days');
        $statusRows = $db->table('applications')
            ->select('status, COUNT(*) as count')
            ->where('created_at >=', $startDT->format('Y-m-d') . ' 00:00:00')
            ->groupBy('status')
            ->get()->getResultArray();
        $status = [];
        foreach ($statusRows as $r) {
            $key = $r['status'] ?: 'unknown';
            $status[$key] = (int) $r['count'];
        }

        // Company over time (last 30 days)
        $start = new \DateTime('-' . ($days - 1) . ' days');
        $days = [];
        for ($i = 0; $i < (int) (new \DateInterval('P' . ($days ?: 30) . 'D'))->d + 0 ?: 0; $i++) {}
        // simpler: build labels loop using selected window length
        $window = (int) ($this->request->getGet('days') ?? 30);
        if (!in_array($window, [7,30,90], true)) { $window = 30; }
        for ($i = 0; $i < $window; $i++) {
            $d = (clone $start)->modify("+{$i} day")->format('Y-m-d');
            $days[] = $d;
        }
        $companyRows = $db->table('applications')
            ->select("DATE(created_at) as day, company_name, COUNT(*) as count")
            ->where('created_at >=', $start->format('Y-m-d') . ' 00:00:00')
            ->groupBy('day, company_name')
            ->orderBy('day', 'ASC')
            ->get()->getResultArray();
        $companies = [];
        foreach ($companyRows as $r) {
            $c = $r['company_name'] ?: 'Unknown';
            if (!isset($companies[$c])) { $companies[$c] = array_fill_keys($days, 0); }
            $day = $r['day'];
            if (isset($companies[$c][$day])) {
                $companies[$c][$day] = (int) $r['count'];
            }
        }
        // Normalize to arrays for Chart.js
        $companySeries = [];
        foreach ($companies as $name => $map) {
            $companySeries[] = [
                'label' => $name,
                'data' => array_values(array_intersect_key($map, array_flip($days))),
            ];
        }

        // Interviewer productivity (top 10 by applications created)
        $interviewerRows = $db->table('applications a')
            ->select('a.interviewed_by, COALESCE(CONCAT(u.first_name, " ", u.last_name), "Unknown") as name, COUNT(*) as count')
            ->join('users u', 'u.id = a.interviewed_by', 'left')
            ->where('a.created_at >=', $start->format('Y-m-d') . ' 00:00:00')
            ->groupBy('a.interviewed_by, name')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->get()->getResultArray();
        $interviewers = [
            'labels' => array_map(fn($r) => $r['name'], $interviewerRows),
            'data' => array_map(fn($r) => (int) $r['count'], $interviewerRows),
        ];

        return $this->response->setJSON([
            'status' => $status,
            'companyOverTime' => [
                'labels' => $days,
                'series' => $companySeries,
            ],
            'interviewers' => $interviewers,
        ]);
    }

    public function export()
    {
        // Admin only
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        $days = (int) ($this->request->getGet('days') ?? 30);
        if (!in_array($days, [7, 30, 90], true)) { $days = 30; }
        $start = new \DateTime('-' . ($days - 1) . ' days');

        $rows = $db->table('applications a')
            ->select('a.id, a.company_name, a.first_name, a.last_name, a.email_address, a.status, a.created_at, a.recruiter_email, u.first_name as interviewer_first, u.last_name as interviewer_last')
            ->join('users u', 'u.id = a.interviewed_by', 'left')
            ->where('a.created_at >=', $start->format('Y-m-d') . ' 00:00:00')
            ->orderBy('a.created_at', 'ASC')
            ->get()->getResultArray();

        $filename = 'applications_last_' . $days . '_days_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $fp = fopen('php://temp', 'w+');
        fputcsv($fp, ['ID','Company','First Name','Last Name','Email','Status','Created At','Recruiter Email','Interviewer']);
        foreach ($rows as $r) {
            $interviewer = trim(($r['interviewer_first'] ?? '') . ' ' . ($r['interviewer_last'] ?? ''));
            fputcsv($fp, [
                $r['id'],
                $r['company_name'],
                $r['first_name'],
                $r['last_name'],
                $r['email_address'],
                $r['status'],
                $r['created_at'],
                $r['recruiter_email'],
                $interviewer,
            ]);
        }
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        return $this->response->setStatusCode(200)
            ->setHeader('Content-Type', $headers['Content-Type'])
            ->setHeader('Content-Disposition', $headers['Content-Disposition'])
            ->setBody($csv);
    }
}
