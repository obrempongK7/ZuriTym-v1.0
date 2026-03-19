<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::where('role','user')->count(),
            'active_users'       => User::where('role','user')->where('status','active')->count(),
            'blocked_users'      => User::where('is_blocked', true)->count(),
            'new_today'          => User::whereDate('created_at', today())->count(),
            'new_this_week'      => User::where('created_at','>=', now()->startOfWeek())->count(),
            'total_transactions' => Transaction::count(),
            'total_points_issued'=> Transaction::where('amount','>',0)->sum('amount'),
            'pending_withdrawals'=> Withdrawal::where('status','pending')->count(),
            'paid_withdrawals'   => Withdrawal::where('status','paid')->count(),
            'fraud_alerts'       => FraudLog::where('is_reviewed', false)->where('severity','high')->count(),
        ];

        $recentUsers = User::where('role','user')
                           ->latest()
                           ->take(10)
                           ->get();

        $pendingWithdrawals = Withdrawal::with('user')
                                        ->where('status','pending')
                                        ->latest()
                                        ->take(5)
                                        ->get();

        $recentTransactions = Transaction::with('user')
                                         ->latest()
                                         ->take(10)
                                         ->get();

        // Chart data: last 7 days signups
        $chartData = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'date'  => $date->format('M d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'txns'  => Transaction::whereDate('created_at', $date)->count(),
            ];
        });

        return view('admin.dashboard', compact(
            'stats','recentUsers','pendingWithdrawals','recentTransactions','chartData'
        ));
    }
}
