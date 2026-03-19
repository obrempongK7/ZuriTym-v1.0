@extends('admin.layouts.app')
@section('title', isset($task->id) ? 'Edit Task' : 'Add Task')
@section('page-title', isset($task->id) ? 'Edit Task' : 'Add Task')
@section('content')
<div style="max-width:700px"><div class="card">
<form action="{{ isset($task->id) ? route('admin.tasks.update',$task) : route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if(isset($task->id)) @method('PUT') @endif
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group"><label class="form-label">Title *</label><input name="title" class="form-control" value="{{ old('title',$task->title) }}" required></div>
        <div class="form-group"><label class="form-label">Type *</label>
            <select name="type" class="form-select">
                @foreach(['watch_video','visit_website','app_install','daily_offer','quiz','social_follow','survey','custom'] as $type)
                <option value="{{ $type }}" {{ old('type',$task->type)==$type?'selected':'' }}>{{ str_replace('_',' ',ucfirst($type)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group"><label class="form-label">Reward Points *</label><input name="reward_points" type="number" step="0.01" class="form-control" value="{{ old('reward_points',$task->reward_points) }}" required></div>
        <div class="form-group"><label class="form-label">Timer (seconds)</label><input name="timer_seconds" type="number" class="form-control" value="{{ old('timer_seconds',$task->timer_seconds??0) }}"></div>
        <div class="form-group"><label class="form-label">Daily Limit</label><input name="daily_limit" type="number" class="form-control" value="{{ old('daily_limit',$task->daily_limit??1) }}"></div>
        <div class="form-group"><label class="form-label">Sort Order</label><input name="sort_order" type="number" class="form-control" value="{{ old('sort_order',$task->sort_order??0) }}"></div>
    </div>
    <div class="form-group"><label class="form-label">Action URL</label><input name="action_url" class="form-control" value="{{ old('action_url',$task->action_url) }}" placeholder="https://..."></div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description',$task->description) }}</textarea></div>
    <div class="form-group"><label class="form-label">Icon Image</label><input name="icon" type="file" class="form-control" accept="image/*"></div>
    <div style="display:flex;gap:20px;margin-bottom:18px">
        <label class="form-check"><input type="checkbox" name="is_active" {{ old('is_active',$task->is_active??true)?'checked':'' }}> Active</label>
        <label class="form-check"><input type="checkbox" name="requires_screenshot" {{ old('requires_screenshot',$task->requires_screenshot??false)?'checked':'' }}> Require Screenshot</label>
        <label class="form-check"><input type="checkbox" name="is_verified" {{ old('is_verified',$task->is_verified??false)?'checked':'' }}> Manual Verify</label>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($task->id)?'Update':'Create' }} Task</button>
    <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline" style="margin-left:8px">Cancel</a>
</form>
</div></div>
@endsection