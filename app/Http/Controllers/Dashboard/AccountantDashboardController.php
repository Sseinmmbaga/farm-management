<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountantDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_payments' => 0,
            'completed_this_month' => 0,
            'total_amount_pending' => 0,
            'total_amount_paid' => 0,
        ];

        $recentPayments = collect([]);
        $pendingPayments = collect([]);

        return view('dashboard.accountant.index', compact('stats', 'recentPayments', 'pendingPayments'));
    }

    public function payments(Request $request)
    {
        $stats = [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0,
        ];

        $payments = collect([]); // Replace with actual payments query

        return view('dashboard.accountant.payments', compact('stats', 'payments'));
    }

    public function reports(Request $request)
    {
        $stats = [
            'total_reports' => 0,
            'generated_this_month' => 0,
            'pending_review' => 0,
        ];

        $reports = collect([]); // Replace with actual reports query

        return view('dashboard.accountant.reports', compact('stats', 'reports'));
    }
}
