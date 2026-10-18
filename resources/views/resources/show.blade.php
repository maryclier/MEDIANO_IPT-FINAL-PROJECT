@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 page-title mb-1">{{ $title }}</h1>
            <p class="text-secondary mb-0">Review this clinic record.</p>
        </div>
        <a class="btn btn-primary" href="{{ route($resourceName.'.edit', $resource) }}">Edit record</a>
    </div>

    <div class="card">
        <dl class="row mb-0 p-4">
            @foreach($details as $label => $value)
                <dt class="col-sm-4 col-lg-3 py-2">{{ $label }}</dt>
                <dd class="col-sm-8 col-lg-9 py-2 mb-0">{{ $value ?? '—' }}</dd>
            @endforeach
        </dl>
    </div>
    <a class="btn btn-outline-secondary mt-3" href="{{ route($resourceName.'.index') }}">Back to {{ strtolower($title) }} list</a>
@endsection
