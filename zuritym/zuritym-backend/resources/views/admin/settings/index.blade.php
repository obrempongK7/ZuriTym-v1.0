@extends('admin.layouts.app')
@section('title','Settings')
@section('page-title','App Settings')
@section('content')
<div style="max-width:800px">
<form action="{{ route('admin.settings.update') }}" method="POST">@csrf
<div class="card">
    <div class="card-title" style="margin-bottom:18px">General Settings</div>
    @foreach($settings->get('general',[]) as $s)
    <div class="form-group">
        <label class="form-label">{{ ucwords(str_replace('_',' ',$s->key)) }}</label>
        <input name="settings[{{ $s->key }}]" class="form-control" value="{{ $s->value }}">
        @if($s->description)<small style="color:#aaa">{{ $s->description }}</small>@endif
    </div>
    @endforeach
</div>
<div class="card">
    <div class="card-title" style="margin-bottom:18px">Wallet & Rewards</div>
    @foreach($settings->get('wallet',[]) as $s)
    <div class="form-group">
        <label class="form-label">{{ ucwords(str_replace('_',' ',$s->key)) }}</label>
        <input name="settings[{{ $s->key }}]" class="form-control" value="{{ $s->value }}">
    </div>
    @endforeach
    @foreach($settings->get('spin',[]) as $s)
    <div class="form-group">
        <label class="form-label">{{ ucwords(str_replace('_',' ',$s->key)) }}</label>
        <input name="settings[{{ $s->key }}]" class="form-control" value="{{ $s->value }}">
    </div>
    @endforeach
    @foreach($settings->get('scratch',[]) as $s)
    <div class="form-group">
        <label class="form-label">{{ ucwords(str_replace('_',' ',$s->key)) }}</label>
        <input name="settings[{{ $s->key }}]" class="form-control" value="{{ $s->value }}">
    </div>
    @endforeach
</div>
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
</form></div>
@endsection