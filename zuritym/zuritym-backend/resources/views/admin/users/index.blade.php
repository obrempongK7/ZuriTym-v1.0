@extends('admin.layouts.app')
@section('title','Users')
@section('page-title','User Management')
@section('content')
<div class="page-header">
    <div><div class="page-title">Users</div><div class="page-subtitle">Manage all registered users</div></div>
</div>
<div class="card">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px">
        <input name="search" class="form-control" style="max-width:260px" placeholder="Search name, email..." value="{{ request('search') }}">
        <select name="status" class="form-select" style="max-width:150px">
            <option value="">All Status</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="blocked" {{ request('status')=='blocked'?'selected':'' }}>Blocked</option>
        </select>
        <button class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Reset</a>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>User</th><th>Email</th><th>Balance</th><th>Referrals</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td style="color:#aaa;font-size:.8rem">{{ $u->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar-sm">{{ substr($u->name,0,1) }}</div>
                            <div>
                                <div style="font-weight:500">{{ $u->name }}</div>
                                <div style="font-size:.75rem;color:#aaa">{{ $u->country ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.83rem;color:#666">{{ $u->email }}</td>
                    <td><strong>{{ number_format($u->wallet?->balance??0) }} pts</strong></td>
                    <td>{{ $u->total_referrals }}</td>
                    <td>@if($u->is_blocked)<span class="badge badge-danger">Blocked</span>@else<span class="badge badge-success">Active</span>@endif</td>
                    <td style="font-size:.8rem;color:#aaa">{{ $u->created_at->format('M d, Y') }}</td>
                    <td><a href="{{ route('admin.users.show',$u) }}" class="btn btn-sm btn-primary">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
