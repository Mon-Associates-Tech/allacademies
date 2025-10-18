@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Book-Based Assignments</h1>
            <p class="text-muted">Manage your book-based assignments</p>
        </div>
        <a href="{{ route('teachers.book-assignments.form', $teacher) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Assignment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Your Assignments</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="table_search" class="form-control float-right"
                               placeholder="Search assignments...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($assignments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Book</th>
                                <th>Questions</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Period</th>
                                <th>Submissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <a href="{{ route('teacher.assignments.show', $assignment) }}">
                                            {{ $assignment->title }}
                                        </a>
                                        @if($assignment->type === 'book_based')
                                            <span class="badge bg-info">Book-Based</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->book)
                                            {{ $assignment->book->title }}
                                        @else
                                            Custom Content
                                        @endif
                                    </td>
                                    <td>{{ $assignment->questions_count ?? 0 }}</td>
                                    <td>{{ $assignment->duration_in_minutes }} min</td>
                                    <td>
                                        <span class="badge
                                            @if($assignment->status === 'published') bg-success
                                            @elseif($assignment->status === 'draft') bg-warning
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $assignment->starts_at->format('M j, Y') }} -
                                        {{ $assignment->ends_at->format('M j, Y') }}
                                    </td>
                                    <td>
                                        {{ $assignment->submissions_count ?? 0 }} submitted
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teacher.assignments.show', $assignment) }}"
                                               class="btn btn-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.assignments.edit', $assignment) }}"
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger"
                                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                    wire:click="deleteAssignment({{ $assignment->id }})"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center p-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h4>No book-based assignments found</h4>
                    <p class="text-muted">
                        Create your first book-based assignment to generate questions from books or content.
                    </p>
                    <a href="{{ route('teachers.book-assignments.form', $teacher) }}"
                       class="btn btn-primary">Create Assignment</a>
                </div>
            @endif
        </div>

        @if($assignments->hasPages())
            <div class="card-footer clearfix">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
