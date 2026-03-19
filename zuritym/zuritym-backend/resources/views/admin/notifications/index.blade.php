@extends('admin.layouts.app')
@section('title','Notifications')
@section('page-title','Push Notifications')
@section('content')
<div style="display:grid;grid-template-columns:1fr 380px;gap:20px">
<div class="card">
    <div class="card-title" style="margin-bottom:18px">Sent Notifications</div>
    <div class="table-wrap"><table>
        <thead><tr><th>Title</th><th>Body</th><th>Target</th><th>Sent To</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            @foreach($notifs as $n)
            <tr>
                <td><strong>{{ $n->title }}</strong></td>
                <td style="font-size:.82rem;max-width:200px;overflow:hidden;text-overflow:ellipsis">{{ Str::limit($n->body,60) }}</td>
                <td><span class="badge badge-purple">{{ $n->target }}</span></td>
                <td>{{ $n->sent_count }} users</td>
                <td><span class="badge badge-{{ $n->status=='sent'?'success':'danger' }}">{{ $n->status }}</span></td>
                <td style="font-size:.78rem;color:#aaa">{{ $n->created_at->format('M d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table></div>
</div>
<div class="card">
    <div class="card-title" style="margin-bottom:18px">Send Notification</div>
    <form action="{{ route('admin.notifications.send') }}" method="POST">@csrf
        <div class="form-group"><label class="form-label">Title *</label><input name="title" class="form-control" placeholder="Notification title" required></div>
        <div class="form-group"><label class="form-label">Message *</label><textarea name="body" class="form-control" rows="3" placeholder="Your message..." required></textarea></div>
        <div class="form-group"><label class="form-label">Target</label>
            <select name="target" class="form-select"><option value="all">All Users</option><option value="specific">Specific Users</option></select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%"><i class="fas fa-paper-plane"></i> Send Now</button>
    </form>
</div>
</div>
@endsection