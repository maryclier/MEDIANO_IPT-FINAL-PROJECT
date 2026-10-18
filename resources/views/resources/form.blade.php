@extends('layouts.app')

@section('content')
    @php
        $isEditing = $resource !== null;
        $formAction = $isEditing ? route($resourceName.'.update', $resource) : route($resourceName.'.store');
    @endphp
    <div class="mb-4">
        <h1 class="h2 page-title mb-1">{{ $title }}</h1>
        <p class="text-secondary mb-0">Enter the details below. Fields marked required must be completed.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if($isEditing)
                    @method('PUT')
                @endif
                <div class="row g-3">
                    @foreach($fields as $field)
                        @php
                            $name = $field['name'];
                            $type = $field['type'] ?? 'text';
                            $value = old($name, $resource?->{$name} ?? ($field['value'] ?? ''));
                            $wide = $type === 'textarea' ? 'col-12' : 'col-md-6';
                        @endphp
                        <div class="{{ $wide }}">
                            <label class="form-label" for="{{ $name }}">
                                {{ $field['label'] }}@if($field['required'] ?? false) <span class="text-danger">*</span>@endif
                            </label>
                            @if($type === 'textarea')
                                <textarea class="form-control" id="{{ $name }}" name="{{ $name }}" rows="3" @required($field['required'] ?? false)>{{ $value }}</textarea>
                            @elseif($type === 'select')
                                <select class="form-select" id="{{ $name }}" name="{{ $name }}" @required($field['required'] ?? false)>
                                    @unless($field['required'] ?? false)
                                        <option value="">Select {{ strtolower($field['label']) }}</option>
                                    @endunless
                                    @foreach($field['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input class="form-control" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" @required($field['required'] ?? false)
                                    @foreach($field['attributes'] ?? [] as $attribute => $attributeValue) {{ $attribute }}="{{ $attributeValue }}" @endforeach>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">{{ $isEditing ? 'Save changes' : 'Save record' }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route($resourceName.'.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
