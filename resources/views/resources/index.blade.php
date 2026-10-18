@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 page-title mb-1">{{ $title }}</h1>
            <p class="text-secondary mb-0">Manage school clinic {{ strtolower($title) }}.</p>
        </div>
        <a class="btn btn-primary" href="{{ route($resourceName.'.create') }}">Add {{ rtrim($title, 's') }}</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($columns as $column)
                            <th scope="col">{{ $column['label'] }}</th>
                        @endforeach
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            @foreach($columns as $column)
                                @php($value = data_get($record, $column['key']))
                                <td>{{ $value instanceof \DateTimeInterface ? $value->format('M j, Y') : ($value ?? '—') }}</td>
                            @endforeach
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route($resourceName.'.show', $record) }}">View</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route($resourceName.'.edit', $record) }}">Edit</a>
                                <form class="d-inline" action="{{ route($resourceName.'.destroy', $record) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="py-5 text-center text-secondary">
                                No {{ strtolower($title) }} found. Add your first record to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())
        <div class="mt-4">{{ $records->links('pagination::bootstrap-5') }}</div>
    @endif
@endsection
