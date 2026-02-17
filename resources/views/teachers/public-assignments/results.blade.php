@extends('layouts.app')

@section('content')
<div class="container">
    <livewire:teachers.view-public-assignment-results :assignment="$assignment" />
</div>
@endsection
