@extends('admin.layouts.app')
@section('title', 'Dashboard – ZuriTym Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#6C63FF,#a29bfe)"><i class="fas fa-users"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['total_users']) }}</div><div class="label">Total Users</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#2ecc71,#55efc4)"><i class="fas fa-user-check"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['active_users']) }}</div><div class="label">Active Users</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f39c12,#fdcb6e)"><i class="fas fa-user-plus"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['new_today']) }}</div><div class="label">New Today</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#e74c3c,#fd79a8)"><i class="fas fa-ban"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['blocked_users']) }}</div><div class="label">Blocked Users</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#3498db,#74b9ff)"><i class="fas fa-coins"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['total_points_issued']) }}</div><div class="label">Total Pts Issued</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#e17055,#fab1a0)"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['pending_withdrawals']) }}</div><div class="label">Pending Withdrawals</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#fd79a8,#e84393)"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['fraud_alerts']) }}</div><div class="label">Fraud Alerts</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#00b894,#00cec9)"><i class="fas fa-check-double"></i></div>
        <div class="stat-info"><div class="value">{{ number_format($stats['paid_withdrawals']) }}</div><div class="label">Paid Withdrawals</div></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <!-- Pending Withdrawals -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">⏳ Pending Withdrawals</span>
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Points</th><th>Method</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($pendingWithdrawals as $w)
                    <tr>
                        <td>{{ $w->user?->name }}</td>
                        <td><strong>{{ number_format($w->amount_points) }}</strong></td>
                        <td><span class="badge badge-info">{{ strtoupper($w->payment_method) }}</span></td>
                        <td><a href="{{ route('admin.withdrawals.show', $w) }}" class="btn btn-sm btn-primary">Review</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#aaa;padding:20px">No pending withdrawals 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 Recent Users</span>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Balance</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($recentUsers as $u)
                    <tr>
                        <td><a href="{{ route('admin.users.show', $u) }}" style="color:var(--primary);text-decoration:none;font-weight:500">{{ $u->name }}</a></td>
                        <td style="font-size:.8rem;color:#888">{{ $u->email }}</td>
                        <td><strong>{{ number_format($u->wallet?->balance ?? 0) }} pts</strong></td>
                        <td>
                            @if($u->is_blocked)
                                <span class="badge badge-danger">Blocked</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Activity Chart -->
<div class="card">
    <div class="card-header"><span class="card-title">📈 7-Day Activity</span></div>
    <canvas id="activityChart" height="80"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
const data = @json($chartData);
new Chart(document.getElementById('activityChart'), {
    type:'line',
    data:{
        labels: data.map(d=>d.date),
        datasets:[
            {label:'New Users',data:data.map(d=>d.users),borderColor:'#6C63FF',backgroundColor:'rgba(108,99,255,.1)',tension:.4,fill:true},
            {label:'Transactions',data:data.map(d=>d.txns),borderColor:'#2ecc71',backgroundColor:'rgba(46,204,113,.1)',tension:.4,fill:true},
        ]
    },
    options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.05)'}},x:{grid:{display:false}}}}
});
</script>
@endpush
