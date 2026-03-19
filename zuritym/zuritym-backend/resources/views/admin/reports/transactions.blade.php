@extends('admin.layouts.app')
@section('title','Transaction Reports')
@section('page-title','Transaction Reports')
@section('content')
<div class="card">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px">
        <input name="from" type="date" class="form-control" style="max-width:170px" value="{{ request('from') }}">
        <input name="to" type="date" class="form-control" style="max-width:170px" value="{{ request('to') }}">
        <select name="type" class="form-select" style="max-width:160px">
            <option value="">All Types</option>
            @foreach(['earn','referral','bonus','promo_code','spin','scratch','task','offerwall','withdrawal'] as $t)
            <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        <a href="{{ route('admin.reports.export') }}" class="btn btn-success"><i class="fas fa-download"></i> Export CSV</a>
    </form>
    <div class="table-wrap"><table>
        <thead><tr><th>Txn ID</th><th>User</th><th>Amount</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td style="font-size:.75rem;color:#aaa">{{ $t->txn_id }}</td>
                <td>{{ $t->user?->name }}</td>
                <td><strong style="color:{{ $t->amount>0?'var(--success)':'var(--danger)' }}">{{ $t->amount>0?'+':'' }}{{ number_format($t->amount) }}</strong></td>
                <td><span class="badge badge-purple">{{ $t->type }}</span></td>
                <td><span class="badge badge-{{ $t->status=='completed'?'success':($t->status=='pending'?'warning':'danger') }}">{{ $t->status }}</span></td>
                <td style="font-size:.78rem;color:#aaa">{{ $t->created_at->format('M d, Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table></div>
    {{ $transactions->links() }}
</div>
@endsection