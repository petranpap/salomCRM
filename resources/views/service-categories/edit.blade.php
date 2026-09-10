@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Services</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Edit {{ $serviceCategory->name }}</h1>
    </div>

    @unless(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
    <div class="mb-4 px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        Changes will be submitted to the owner for approval before they go live.
    </div>
    @endunless

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('service-categories.update', $serviceCategory) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Name *</label>
                <input type="text" name="name" value="{{ old('name', $serviceCategory->name) }}"
                       class="input-field @error('name') ring-2 ring-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Colour</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $serviceCategory->color) }}"
                               class="w-12 h-10 rounded-xl border border-ts-border-soft cursor-pointer bg-transparent">
                        <span class="text-xs text-ts-text-subtle">Calendar &amp; badge colour</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $serviceCategory->sort_order) }}" min="0"
                           class="input-field @error('sort_order') ring-2 ring-red-400 @enderror">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="input-field @error('description') ring-2 ring-red-400 @enderror">{{ old('description', $serviceCategory->description) }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       {{ old('is_active', $serviceCategory->is_active) ? 'checked' : '' }}
                       class="rounded border-ts-border text-ts-primary focus:ring-ts-primary/30">
                <label for="is_active" class="text-sm text-ts-text-muted cursor-pointer">Active</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Save Changes' : 'Submit for Approval' }}
                </button>
                <a href="{{ route('service-categories.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium text-ts-text-muted hover:bg-ts-surface-mid transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="mt-5 bg-ts-surface border border-ts-error-light rounded-card-lg p-5 shadow-silk">
        <p class="text-xs font-bold text-ts-error mb-3">Danger Zone</p>
        <form action="{{ route('service-categories.destroy', $serviceCategory) }}" method="POST"
              onsubmit="return confirm('{{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request deletion of' }} {{ addslashes($serviceCategory->name) }}? Services using this category will lose their assignment.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium text-ts-error border border-ts-error-light hover:bg-ts-error-light transition">
                {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete Category' : 'Request Deletion' }}
            </button>
        </form>
    </div>
</div>
@endsection
