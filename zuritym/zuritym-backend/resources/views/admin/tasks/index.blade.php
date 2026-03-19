@extends('admin.layouts.app')
@section('title','Tasks')
@section('page-title','Task Management')
@section('content')
<div class="page-header">
    <div><div class="page-title">Tasks</div></div>
    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Task</a>
</div>
<div class="card"><div class="table-wrap"><table>
    <thead><tr><th>Title</th><th>Type</th><th>Reward</th><th>Daily Limit</th><th>Completions</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($tasks as $t)
        <tr>
            <td><strong>{{ $t->title }}</strong></td>
            <td><span class="badge badge-info">{{ str_replace('_',' ',strtoupper($t->type)) }}</span></td>
            <td><strong>{{ $t->reward_points }} pts</strong></td>
            <td>{{ $t->daily_limit }}/day</td>
            <td>{{ $t->completion_count }}</td>
            <td>@if($t->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
            <td style="display:flex;gap:6px">
                <a href="{{ route('admin.tasks.edit',$t) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                <form action="{{ route('admin.tasks.destroy',$t) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table></div></div>
@endsection