@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Services</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">New Category</h1>
    </div>

    @unless(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
    <div class="mb-4 px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        This will be submitted to the owner for approval before it goes live.
    </div>
    @endunless

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('service-categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="e.g. Hair, Nails, Waxing…"
                       class="input-field @error('name') ring-2 ring-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Colour</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', '#7d523c') }}"
                               class="w-12 h-10 rounded-xl border border-ts-border-soft cursor-pointer bg-transparent">
                        <span class="text-xs text-ts-text-subtle">Shown on calendar &amp; badges</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="input-field @error('sort_order') ring-2 ring-red-400 @enderror">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Description</label>
                <textarea name="description" rows="3" placeholder="Optional description…"
                          class="input-field @error('description') ring-2 ring-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="rounded border-ts-border text-ts-primary focus:ring-ts-primary/30">
                <label for="is_active" class="text-sm text-ts-text-muted cursor-pointer">Active (visible when booking)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Create Category' : 'Submit for Approval' }}
                </button>
                <a href="{{ route('service-categories.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium text-ts-text-muted hover:bg-ts-surface-mid transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
