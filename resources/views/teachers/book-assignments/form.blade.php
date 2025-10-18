@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Create Book-Based Assignment</h1>
        <p class="text-muted">Generate questions from books or uploaded content</p>
    </div>

    <livewire:teachers.book-based-assignment />
</div>
@endsection

