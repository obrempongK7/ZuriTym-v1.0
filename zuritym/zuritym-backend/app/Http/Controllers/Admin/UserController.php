<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role','user')->with('wallet');

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")
                                      ->orWhere('email','like',"%$s%")
                                      ->orWhere('username','like',"%$s%"));
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->country) $query->where('country', $request->country);

        $users = $query->latest()->paginate(25)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('wallet','transactions','userTasks.task','withdrawals','fraudLogs','referrals');
        $stats = [
            'tasks_completed' => $user->userTasks()->where('status','completed')->count(),
            'total_spins'     => $user->spinHistories()->count(),
            'total_scratches' => $user->scratchCards()->where('is_scratched',true)->count(),
            'fraud_score'     => $user->fraud_score,
        ];
        return view('admin.users.show', compact('user','stats'));
    }

    public function update(Request $request, User $user)
    {
        $v = Validator::make($request->all(), [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,'.$user->id,
            'status'  => 'required|in:active,inactive,blocked',
            'country' => 'nullable|string|max:5',
        ]);
        if ($v->fails()) return back()->withErrors($v)->withInput();

        $user->update($request->only(['name','email','status','country','phone']));
        return back()->with('success', 'User updated successfully.');
    }

    public function block(Request $request, User $user)
    {
        $reason = $request->reason ?? 'Blocked by admin.';
        $user->blockUser($reason);
        return back()->with('success', "User {$user->name} has been blocked.");
    }

    public function unblock(User $user)
    {
        $user->update([
            'is_blocked'   => false,
            'status'       => 'active',
            'block_reason' => null,
            'blocked_at'   => null,
        ]);
        if ($user->wallet) {
            $user->wallet->update(['is_locked' => false, 'lock_reason' => null]);
        }
        return back()->with('success', "User {$user->name} has been unblocked.");
    }

    public function creditWallet(Request $request, User $user)
    {
        $v = Validator::make($request->all(), [
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:200',
        ]);
        if ($v->fails()) return back()->withErrors($v);

        $user->creditWallet($request->amount, 'admin_credit', $request->description);
        return back()->with('success', "Credited {$request->amount} points to {$user->name}.");
    }

    public function debitWallet(Request $request, User $user)
    {
        $v = Validator::make($request->all(), [
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:200',
        ]);
        if ($v->fails()) return back()->withErrors($v);

        $result = $user->debitWallet($request->amount, 'admin_debit', $request->description);
        if (!$result) return back()->with('error', 'Insufficient balance.');
        return back()->with('success', "Debited {$request->amount} points from {$user->name}.");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
