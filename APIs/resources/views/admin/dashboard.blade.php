@extends('layouts.admin')

@section('content')
<h1>Welcome to WhatsApp Bot Dashboard</h1>
<div class="row">
    <div class="col-md-4">
        <div class="card text-bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Bots</h5>
                <p class="card-text display-6">3</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Messages Sent</h5>
                <p class="card-text display-6">1200</p>
            </div>
        </div>
    </div>
</div>
@endsection
