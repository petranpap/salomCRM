@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Services</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Edit — {{ $service->name }}</h1>
    </div>

    @unless(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
    <div class="px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        Changes will be submitted to the owner for approval before they go live.
    </div>
    @endunless

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form action="{{ route('services.update', $service) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')

            @if($salons->isNotEmpty())
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Salon</label>
                <select name="salon_id" class="input-field">
                    <option value="">— Unassigned —</option>
                    @foreach($salons as $salon)
                        <option value="{{ $salon->id }}" {{ old('salon_id', $service->salon_id) == $salon->id ? 'selected' : '' }}>{{ $salon->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Service Name *</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                       class="input-field @error('name') ring-2 ring-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Category</label>
                    <select name="category_id" class="input-field">
                        <option value="">No category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Duration (min) *</label>
                    <input type="number" name="duration_min" value="{{ old('duration_min', $service->duration_min) }}" min="5" required
                           class="input-field @error('duration_min') ring-2 ring-red-400 @enderror">
                    @error('duration_min')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Base Price (€) *</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ts-text-subtle font-semibold text-sm">€</span>
                    <input type="number" name="base_price" value="{{ old('base_price', $service->base_price) }}" min="0" step="0.01" required
                           class="input-field pl-7 @error('base_price') ring-2 ring-red-400 @enderror">
                </div>
                @error('base_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $service->is_active) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-ts-primary"></div>
                </label>
                <span class="text-sm text-ts-text-muted">Active — visible when booking</span>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Save Changes' : 'Submit for Approval' }}
                </button>
                <a href="{{ route('services.index') }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
                <form method="POST" action="{{ route('services.destroy', $service) }}" class="ml-auto"
                      onsubmit="return confirm('{{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request deletion of' }} {{ addslashes($service->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 border border-red-200 transition">
                        {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request Deletion' }}
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
