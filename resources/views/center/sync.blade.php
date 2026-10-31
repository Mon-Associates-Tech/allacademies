@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Sync Status</h1>
        <form method="POST" action="{{ route('center.sync.push') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Push Now</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Pull Status</div>
                <div class="card-body">
                    <p>Last pull: {{ $lastPull ? $lastPull->started_at->diffForHumans() : 'Never' }}</p>
                    <p>Status: <span class="badge {{ $lastPull && $lastPull->status === 'success' ? 'badge-success' : 'badge-danger' }}">
                        {{ $lastPull ? $lastPull->status : 'No data' }}
                    </span></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Push Status</div>
                <div class="card-body">
                    <p>Last push: {{ $lastPush ? $lastPush->started_at->diffForHumans() : 'Never' }}</p>
                    <p>Backlog: <span class="badge {{ $backlog > 0 ? 'badge-warning' : 'badge-success' }}">{{ $backlog }}</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Exam Sync States --}}
    @if($examSyncStates->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">Exam Sync State</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Exam</th>
                            <th>Applied Revision</th>
                            <th>State Version</th>
                            <th>Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($examSyncStates as $state)
                            <tr>
                                <td>{{ $state->exam?->title ?? $state->exam_uuid }}</td>
                                <td>{{ $state->applied_revision }}</td>
                                <td>{{ $state->applied_state_version }}</td>
                                <td>{{ $state->applied_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Failed Syncs --}}
    @if($failedSyncs->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">Failed Syncs</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Direction</th>
                            <th>Started</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($failedSyncs as $run)
                            <tr>
                                <td>{{ $run->direction }}</td>
                                <td>{{ $run->started_at->diffForHumans() }}</td>
                                <td class="text-danger">{{ Str::limit($run->error ?? 'Unknown error', 100) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
