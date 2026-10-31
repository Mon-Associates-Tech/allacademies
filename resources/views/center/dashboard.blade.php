@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Multi-Center Dashboard</h1>
        @if($isAdmin && $allCenters->count() > 1)
            <form method="GET" class="form-inline">
                <select name="center_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Centers</option>
                    @foreach($allCenters as $center)
                        <option value="{{ $center->id }}" {{ $selectedCenterId == $center->id ? 'selected' : '' }}>
                            {{ $center->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    {{-- Center Cards --}}
    <div class="row">
        @foreach($centerStats as $stats)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ $stats['name'] }}</strong>
                        <span class="badge {{ $stats['is_active'] ? 'badge-success' : 'badge-danger' }}">
                            {{ $stats['is_active'] ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-2">
                                <div class="text-muted small">Candidates</div>
                                <div class="h4">{{ $stats['total_candidates'] }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-muted small">Submitted</div>
                                <div class="h4">{{ $stats['submitted'] }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-muted small">Completion</div>
                                <div class="h4">{{ $stats['completion_rate'] }}%</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-muted small">Avg Score</div>
                                <div class="h4">{{ $stats['avg_score'] ?? 'N/A' }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-muted small">Flagged</div>
                                <div class="h4 text-danger">{{ $stats['flagged_proctoring'] }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-muted small">Backlog</div>
                                <div class="h4 {{ $stats['backlog'] > 0 ? 'text-warning' : '' }}">{{ $stats['backlog'] }}</div>
                            </div>
                        </div>
                        <hr>
                        <div class="small text-muted">
                            <div>Last synced: {{ $stats['last_sync_at'] ? $stats['last_sync_at']->diffForHumans() : 'Never' }}</div>
                            <div>Last activity: {{ $stats['last_activity_at'] ? $stats['last_activity_at']->diffForHumans() : 'None' }}</div>
                            @if($stats['parity_failures'] > 0)
                                <div class="text-danger">Parity failures: {{ $stats['parity_failures'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('center.show', $stats['id']) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Parity Alerts --}}
    @if($parityAlerts->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-exclamation-triangle"></i> Parity Alerts ({{ $parityAlerts->count() }})
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Submission</th>
                            <th>Exam</th>
                            <th>Center Score</th>
                            <th>Central Score</th>
                            <th>Checked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parityAlerts as $alert)
                            <tr>
                                <td>{{ $alert->participant_name ?? 'Unknown' }}</td>
                                <td>{{ $alert->exam?->title ?? 'Unknown' }}</td>
                                <td>{{ $alert->score }}</td>
                                <td>{{ $alert->central_score }}</td>
                                <td>{{ $alert->parity_checked_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Cross-Center Duplicates --}}
    @if(count($duplicateDetails) > 0)
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-exclamation-circle"></i> Cross-Center Duplicates ({{ count($duplicateDetails) }})
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Index Number</th>
                            <th>Name</th>
                            <th>Centers</th>
                            <th>Exam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($duplicateDetails as $indexNumber => $submissions)
                            <tr>
                                <td>{{ $indexNumber }}</td>
                                <td>{{ $submissions->first()->participant_name ?? 'Unknown' }}</td>
                                <td>{{ $submissions->pluck('center_id')->unique()->count() }} centers</td>
                                <td>{{ $submissions->first()->exam?->title ?? 'Unknown' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
