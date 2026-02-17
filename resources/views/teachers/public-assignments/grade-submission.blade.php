@extends('layouts.app')

@section('content')
<div class="container">
    <livewire:teachers.grade-public-assignment-submission :submission="$submission" />
</div>
@endsection
