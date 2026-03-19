@extends('admin.layouts.app')
@section('title','Withdrawals')
@section('page-title','Withdrawal Requests')
@section('content')
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
    <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#f39c12,#fdcb6e)"><i class="fas fa-clock"></i></div><div class="stat-info"><div class="value">{{ $stats['pending'] }}</div><div class="label">Pending</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#3498db,#74b9ff)"><i class="fas fa-check"></i></div><div class="stat-info"><div class="value">{{ $stats['approved'] }}</div><div class="label">Approved</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#2ecc71,#55efc4)"><i class="fas fa-money-bill"></i></div><div class="stat-info"><div class="value">{{ $stats['paid'] }}</div><div class="label">Paid</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#e74c3c,#fd79a8)"><i class="fas fa-times"></i></div><div class="stat-info"><div class="value">{{ $stats['rejected'] }}</div><div class="label">Rejected</div></div></div>
</div>
<div class="card">
    <form method="GET" style="margin-bottom:16px;display:flex;gap:10px">
        <select name="status" class="form-select" style="max-width:160px">
            <option value="">All</option>
            @foreach(['pending','approved','paid','rejected'] as $s)
            <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary">Filter</button>
    </form>
    <div class="table-wrap"><table>
        <thead><tr><th>ID</th><th>User</th><th>Points</th><th>Method</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
            @foreach($withdrawals as $w)
            <tr>
                <td style="font-size:.78rem;color:#aaa">{{ $w->withdrawal_id }}</td>
                <td>{{ $w->user?->name }}</td>
                <td><strong>{{ number_format($w->amount_points) }}</strong></td>
                <td><span class="badge badge-info">{{ strtoupper($w->payment_method) }}</span></td>
                <td><span class="badge badge-{{ $w->status=='pending'?'warning':($w->status=='paid'||$w->status=='approved'?'success':'danger') }}">{{ ucfirst($w->status) }}</span></td>
                <td style="font-size:.8rem;color:#aaa">{{ $w->created_at->format('M d, Y') }}</td>
                <td><a href="{{ route('admin.withdrawals.show',$w) }}" class="btn btn-sm btn-primary">Review</a></td>
            </tr>
            @endforeach
        </tbody>
    </table></div>
    {{ $withdrawals->links() }}
</div>
@endsection