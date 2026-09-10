@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Services</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Service Categories</h1>
        </div>
        <a href="{{ route('service-categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
            + Add Category
        </a>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-ts-surface-low border-b border-ts-border-soft">
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Category</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Description</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Order</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @forelse($categories as $category)
                <tr class="hover:bg-ts-surface-low transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="w-4 h-4 rounded-full shrink-0" style="background-color: {{ $category->color }}"></span>
                            <span class="font-semibold text-ts-text">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-ts-text-muted hidden sm:table-cell">
                        {{ Str::limit($category->description, 60) ?: '—' }}
                    </td>
                    <td class="px-5 py-4 text-ts-text-subtle font-mono text-xs">{{ $category->sort_order }}</td>
                    <td class="px-5 py-4">
                        @if($category->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-ts-surface-high text-ts-text-subtle">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right flex items-center justify-end gap-2">
                        <a href="{{ route('service-categories.edit', $category) }}"
                           class="text-xs font-semibold text-ts-primary hover:underline">Edit</a>
                        <form method="POST" action="{{ route('service-categories.destroy', $category) }}"
                              onsubmit="return confirm('{{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete this category?' : 'Request deletion of this category?' }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-ts-error hover:underline">
                                {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request Deletion' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-14 text-center text-ts-text-subtle text-sm">
                        No categories yet.
                        <a href="{{ route('service-categories.create') }}" class="text-ts-primary font-semibold hover:underline ml-1">Create the first one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if($categories->hasPages())
    <div class="pt-1">{{ $categories->links() }}</div>
    @endif

</div>
@endsection
