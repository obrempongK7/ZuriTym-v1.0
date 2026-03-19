@extends('admin.layouts.app')
@section('title','Withdrawal Review')
@section('page-title','Review Withdrawal')
@section('content')
<div style="max-width:700px">
<div class="card">
    <div class="card-header"><span class="card-title">Withdrawal #{{ $withdrawal->withdrawal_id }}</span>
        <span class="badge badge-{{ $withdrawal->status=='pending'?'warning':($withdrawal->status=='paid'||$withdrawal->status=='approved'?'success':'danger') }}">{{ ucfirst($withdrawal->status) }}</span>
    </div>
    <table style="width:100%;margin-bottom:18px"><tbody>
        <tr><td style="color:#888;padding:8px 0">User</td><td><strong>{{ $withdrawal->user?->name }}</strong> ({{ $withdrawal->user?->email }})</td></tr>
        <tr><td style="color:#888;padding:8px 0">Amount</td><td><strong>{{ number_format($withdrawal->amount_points) }} points</strong></td></tr>
        <tr><td style="color:#888;padding:8px 0">Cash Value</td><td>{{ $withdrawal->amount_cash }}</td></tr>
        <tr><td style="color:#888;padding:8px 0">Method</td><td><span class="badge badge-info">{{ strtoupper($withdrawal->payment_method) }}</span></td></tr>
        <tr><td style="color:#888;padding:8px 0">Details</td><td><pre style="background:#f8f9ff;padding:10px;border-radius:6px;font-size:.8rem">{{ json_encode($withdrawal->payment_details, JSON_PRETTY_PRINT) }}</pre></td></tr>
        <tr><td style="color:#888;padding:8px 0">Requested</td><td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td></tr>
    </tbody></table>
    @if($withdrawal->screenshot)
    <div style="margin-bottom:18px"><strong>Payment Screenshot:</strong><br><img src="{{ asset('storage/withdrawals/'.$withdrawal->screenshot) }}" style="max-width:100%;border-radius:8px;margin-top:8px"></div>
    @endif
    @if($withdrawal->status == 'pending')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <form action="{{ route('admin.withdrawals.approve',$withdrawal) }}" method="POST">@csrf
            <div class="form-group"><label class="form-label">Admin Note</label><textarea name="note" class="form-control" rows="2" placeholder="Optional note..."></textarea></div>
            <button class="btn btn-success" style="width:100%"><i class="fas fa-check"></i> Approve & Pay</button>
        </form>
        <form action="{{ route('admin.withdrawals.reject',$withdrawal) }}" method="POST">@csrf
            <div class="form-group"><label class="form-label">Rejection Reason *</label><textarea name="reason" class="form-control" rows="2" placeholder="Tell user why..." required></textarea></div>
            <button class="btn btn-danger" style="width:100%"><i class="fas fa-times"></i> Reject & Refund</button>
        </form>
    </div>
    @else
    <div class="alert alert-{{ $withdrawal->status=='paid'||$withdrawal->status=='approved'?'success':'danger' }}">
        <i class="fas fa-info-circle"></i> This withdrawal has been {{ $withdrawal->status }}.
        @if($withdrawal->admin_note) Note: {{ $withdrawal->admin_note }} @endif
        @if($withdrawal->rejection_reason) Reason: {{ $withdrawal->rejection_reason }} @endif
    </div>
    @endif
</div></div>
@endsection