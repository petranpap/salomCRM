@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-espresso-subtle dark:text-espresso-muted mb-1">Services</p>
            <h1 class="font-display text-3xl font-semibold text-espresso dark:text-cream">{{ $service->name }}</h1>
        </div>
        <a href="{{ route('services.index') }}" class="btn-outline text-sm">← Back</a>
    </div>
    <div class="card space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-espresso-muted">Category</span><p class="font-medium">{{ $service->category?->name ?? '—' }}</p></div>
            <div><span class="text-espresso-muted">Duration</span><p class="font-medium">{{ $service->duration_min }} min</p></div>
            <div><span class="text-espresso-muted">Base Price</span><p class="font-medium text-rose-sand">€{{ number_format($service->base_price, 2) }}</p></div>
            <div><span class="text-espresso-muted">Status</span>
                <span class="{{ $service->is_active ? 'status-confirmed' : 'badge bg-gray-100 text-gray-500' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <a href="{{ route('services.edit', $service) }}" class="btn-primary">Edit Service</a>
        </div>
    </div>
</div>
@endsection
