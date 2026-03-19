<?php

namespace App\Console\Commands;

use App\Models\Leaderboard;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshLeaderboard extends Command
{
    protected $signature   = 'zuritym:refresh-leaderboard';
    protected $description = 'Refresh leaderboard rankings';

    public function handle(): void
    {
        $this->info('Refreshing leaderboard...');

        User::where('role','user')->chunk(200, function ($users) {
            foreach ($users as $user) {
                $total   = Transaction::where('user_id',$user->id)->where('amount','>',0)->sum('amount');
                $weekly  = Transaction::where('user_id',$user->id)->where('amount','>',0)->where('created_at','>=',now()->startOfWeek())->sum('amount');
                $monthly = Transaction::where('user_id',$user->id)->where('amount','>',0)->where('created_at','>=',now()->startOfMonth())->sum('amount');

                Leaderboard::updateOrCreate(
                    ['user_id' => $user->id],
                    ['total_points' => $total, 'weekly_points' => $weekly, 'monthly_points' => $monthly]
                );
            }
        });

        // Update ranks
        $rank = 1;
        Leaderboard::orderByDesc('total_points')->chunk(200, function ($entries) use (&$rank) {
            foreach ($entries as $entry) {
                $entry->update(['rank' => $rank++]);
            }
        });

        $this->info('Leaderboard refreshed successfully.');
    }
}
