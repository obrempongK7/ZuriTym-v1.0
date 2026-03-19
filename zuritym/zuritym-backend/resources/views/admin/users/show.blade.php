@extends('admin.layouts.app')
@section('title','User: '.$user->name)
@section('page-title','User Detail')
@section('content')
<div style="display:grid;grid-template-columns:320px 1fr;gap:20px">
    <div>
        <div class="card" style="text-align:center">
            <div class="avatar-sm" style="width:64px;height:64px;font-size:1.4rem;margin:0 auto 12px">{{ substr($user->name,0,1) }}</div>
            <h3>{{ $user->name }}</h3>
            <p style="color:#aaa;font-size:.85rem">{{ $user->email }}</p>
            <div style="margin:14px 0">
                @if($user->is_blocked)
                    <span class="badge badge-danger">Blocked</span>
                    <form action="{{ route('admin.users.unblock',$user) }}" method="POST" style="display:inline;margin-left:8px">@csrf <button class="btn btn-sm btn-success">Unblock</button></form>
                @else
                    <span class="badge badge-success">Active</span>
                    <button onclick="document.getElementById('block-form').style.display='block'" class="btn btn-sm btn-danger" style="margin-left:8px">Block</button>
                @endif
            </div>
            @if(!$user->is_blocked)
            <div id="block-form" style="display:none;margin-top:12px">
                <form action="{{ route('admin.users.block',$user) }}" method="POST">@csrf
                    <input name="reason" class="form-control" placeholder="Block reason..." style="margin-bottom:8px">
                    <button class="btn btn-danger btn-sm" style="width:100%">Confirm Block</button>
                </form>
            </div>
            @endif
        </div>
        <div class="card">
            <div class="card-title" style="margin-bottom:14px">Wallet</div>
            <table style="width:100%"><tbody>
                <tr><td>Balance</td><td><strong>{{ number_format($user->wallet?->balance??0) }} pts</strong></td></tr>
                <tr><td>Total Earned</td><td>{{ number_format($user->wallet?->total_earned??0) }} pts</td></tr>
                <tr><td>Fraud Score</td><td><span class="badge {{ $stats['fraud_score']>50?'badge-danger':($stats['fraud_score']>20?'badge-warning':'badge-success') }}">{{ $stats['fraud_score'] }}</span></td></tr>
            </tbody></table>
            <div style="margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <form action="{{ route('admin.users.credit',$user) }}" method="POST">@csrf
                    <input name="amount" class="form-control" placeholder="Points" style="margin-bottom:6px">
                    <input name="description" class="form-control" placeholder="Reason" style="margin-bottom:6px">
                    <button class="btn btn-success btn-sm" style="width:100%">+ Credit</button>
                </form>
                <form action="{{ route('admin.users.debit',$user) }}" method="POST">@csrf
                    <input name="amount" class="form-control" placeholder="Points" style="margin-bottom:6px">
                    <input name="description" class="form-control" placeholder="Reason" style="margin-bottom:6px">
                    <button class="btn btn-danger btn-sm" style="width:100%">- Debit</button>
                </form>
            </div>
        </div>
    </div>
    <div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:20px">
            <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#6C63FF,#a29bfe)"><i class="fas fa-tasks"></i></div><div class="stat-info"><div class="value">{{ $stats['tasks_completed'] }}</div><div class="label">Tasks Done</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#f39c12,#fdcb6e)"><i class="fas fa-dharmachakra"></i></div><div class="stat-info"><div class="value">{{ $stats['total_spins'] }}</div><div class="label">Spins</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#2ecc71,#55efc4)"><i class="fas fa-ticket-alt"></i></div><div class="stat-info"><div class="value">{{ $stats['total_scratches'] }}</div><div class="label">Scratches</div></div></div>
        </div>
        <div class="card">
            <div class="card-title" style="margin-bottom:14px">Recent Transactions</div>
            <div class="table-wrap"><table><thead><tr><th>Txn ID</th><th>Amount</th><th>Type</th><th>Description</th><th>Date</th></tr></thead><tbody>
                @foreach($user->transactions->take(15) as $t)
                <tr>
                    <td style="font-size:.75rem;color:#aaa">{{ $t->txn_id }}</td>
                    <td><strong style="color:{{ $t->amount>0?'var(--success)':'var(--danger)' }}">{{ $t->amount>0?'+':'' }}{{ number_format($t->amount) }}</strong></td>
                    <td><span class="badge badge-purple">{{ $t->type }}</span></td>
                    <td style="font-size:.82rem">{{ $t->description }}</td>
                    <td style="font-size:.78rem;color:#aaa">{{ $t->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody></table></div>
        </div>
    </div>
</div>
@endsection