@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">{{ $center->name }}</h1>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Center Info</div>
                <div class="card-body">
                    <p><strong>Code:</strong> {{ $center->code }}</p>
                    <p><strong>Active:</strong> {{ $center->is_active ? 'Yes' : 'No' }}</p>
                    <p><strong>Last seen:</strong> {{ $center->last_seen_at ? $center->last_seen_at->diffForHumans() : 'Never' }}</p>
                    <p><strong>App version:</strong> {{ $center->app_version ?? 'Unknown' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">Statistics</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="text-muted small">Candidates</div>
                            <div class="h4">{{ $stats['total_candidates'] }}</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted small">Submitted</div>
                            <div class="h4">{{ $stats['submitted'] }}</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted small">Completion</div>
                            <div class="h4">{{ $stats['completion_rate'] }}%</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted small">Avg Score</div>
                            <div class="h4">{{ $stats['avg_score'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="card mb-4">
        <div class="card-header">Recent Submissions</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Exam</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSubmissions as $sub)
                        <tr>
                            <td>{{ $sub->participant_name ?? 'Unknown' }}</td>
                            <td>{{ $sub->exam?->title ?? 'Unknown' }}</td>
                            <td>{{ $sub->status }}</td>
                            <td>{{ $sub->score ?? 'N/A' }}</td>
                            <td>{{ $sub->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
