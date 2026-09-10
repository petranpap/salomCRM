@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Menu</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Services</h1>
        </div>
        <a href="{{ route('services.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Service
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Category filter tabs --}}
    @if($categories->isNotEmpty())
    <div class="flex items-center gap-2 overflow-x-auto pb-1 -mx-1 px-1">
        <button onclick="filterCategory('all')" data-cat="all"
                class="cat-filter active flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition whitespace-nowrap
                       bg-ts-primary text-white border-ts-primary">
            All
        </button>
        @foreach($categories as $cat)
        <button onclick="filterCategory('{{ $cat->id }}')" data-cat="{{ $cat->id }}"
                class="cat-filter flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border border-ts-border-soft text-ts-text-muted hover:border-ts-primary hover:text-ts-primary bg-white transition whitespace-nowrap">
            @if($cat->color)
            <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $cat->color }}"></span>
            @endif
            {{ $cat->name }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- Service cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="services-grid">
        @forelse($services as $service)
        <div class="service-card bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk flex flex-col justify-between gap-4 hover:shadow-silk-md transition-shadow"
             data-cat="{{ $service->category_id }}">
            <div>
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($service->category)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                              style="background-color: {{ $service->category->color ? $service->category->color . '22' : '#f2f0eb' }}; color: {{ $service->category->color ?? '#7d523c' }}">
                            @if($service->category->color)
                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: {{ $service->category->color }}"></span>
                            @endif
                            {{ $service->category->name }}
                        </span>
                        @endif
                        @if(!$service->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Inactive</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[11px] text-ts-text-subtle font-mono">{{ $service->duration_min }}min</span>
                    </div>
                </div>
                <h3 class="font-display text-lg font-normal text-ts-text">{{ $service->name }}</h3>
                <p class="font-display text-2xl font-normal text-ts-primary mt-1">€{{ number_format($service->base_price, 2) }}</p>
            </div>
            <div class="flex gap-2 pt-3 border-t border-ts-border-soft">
                <a href="{{ route('services.edit', $service) }}"
                   class="flex-1 text-center py-2 text-xs font-semibold text-ts-primary border border-ts-primary rounded-xl hover:bg-ts-primary hover:text-white transition">
                    Edit
                </a>
                <form action="{{ route('services.destroy', $service) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('{{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request deletion of' }} {{ addslashes($service->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full py-2 text-xs font-semibold text-ts-text-muted border border-ts-border-soft rounded-xl hover:border-red-300 hover:text-red-500 transition">
                        {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request Deletion' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-16 text-center text-ts-text-subtle">
            <p class="text-sm">No services yet.</p>
            <a href="{{ route('services.create') }}" class="mt-2 inline-block text-ts-primary text-sm font-semibold hover:underline">
                Add the first service →
            </a>
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
function filterCategory(catId) {
    document.querySelectorAll('.cat-filter').forEach(btn => {
        const isActive = btn.dataset.cat === catId;
        btn.classList.toggle('bg-ts-primary', isActive);
        btn.classList.toggle('text-white', isActive);
        btn.classList.toggle('border-ts-primary', isActive);
        btn.classList.toggle('text-ts-text-muted', !isActive);
        btn.classList.toggle('border-ts-border-soft', !isActive);
        btn.classList.toggle('bg-white', !isActive);
    });

    document.querySelectorAll('.service-card').forEach(card => {
        card.style.display = (catId === 'all' || card.dataset.cat === catId) ? '' : 'none';
    });
}
</script>
@endpush
