<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller {
    public function index(Request $request) {
        $query = Withdrawal::with('user');
        if ($request->status) $query->where('status', $request->status);
        $withdrawals = $query->latest()->paginate(20)->withQueryString();
        $stats = [
            'pending' => Withdrawal::where('status','pending')->count(),
            'approved'=> Withdrawal::where('status','approved')->count(),
            'paid'    => Withdrawal::where('status','paid')->count(),
            'rejected'=> Withdrawal::where('status','rejected')->count(),
        ];
        return view('admin.withdrawals.index', compact('withdrawals','stats'));
    }
    public function show(Withdrawal $withdrawal) {
        $withdrawal->load('user.wallet');
        return view('admin.withdrawals.show', compact('withdrawal'));
    }
    public function approve(Request $request, Withdrawal $withdrawal) {
        if ($withdrawal->status !== 'pending') return back()->with('error','Not pending.');
        DB::transaction(function() use ($withdrawal, $request) {
            $withdrawal->update([
                'status'=>'approved',
                'admin_note'=>$request->note,
                'processed_by'=>auth()->id(),
                'processed_at'=>now(),
            ]);
            $wallet = $withdrawal->user->wallet;
            $wallet->decrement('pending_withdrawal', $withdrawal->amount_points);
            $wallet->increment('total_withdrawn', $withdrawal->amount_points);
        });
        return back()->with('success','Withdrawal approved.');
    }
    public function reject(Request $request, Withdrawal $withdrawal) {
        if ($withdrawal->status !== 'pending') return back()->with('error','Not pending.');
        DB::transaction(function() use ($withdrawal, $request) {
            $withdrawal->update([
                'status'=>'rejected',
                'rejection_reason'=>$request->reason,
                'processed_by'=>auth()->id(),
                'processed_at'=>now(),
            ]);
            // Refund points
            $wallet = $withdrawal->user->wallet;
            $wallet->increment('balance', $withdrawal->amount_points);
            $wallet->decrement('pending_withdrawal', $withdrawal->amount_points);
            $withdrawal->user->creditWallet(0,'refund',"Withdrawal #{$withdrawal->withdrawal_id} rejected & refunded.");
        });
        return back()->with('success','Withdrawal rejected & refunded.');
    }
}
