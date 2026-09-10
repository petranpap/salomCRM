@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Platform Admin</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">All Salons</h1>
        </div>
        <a href="{{ route('salons.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Salon
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

    {{-- Table --}}
    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ts-border-soft bg-ts-surface-low">
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Salon</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Owner</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Staff</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Clients</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden lg:table-cell">Appointments</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @forelse($salons as $salon)
                <tr class="hover:bg-ts-surface-low transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-ts-primary flex items-center justify-center text-white text-sm font-bold shrink-0">
                                {{ strtoupper(substr($salon->name, 0, 1)) }}
                            </span>
                            <div>
                                <p class="font-semibold text-ts-text">{{ $salon->name }}</p>
                                @if($salon->email)
                                <p class="text-[11px] text-ts-text-subtle">{{ $salon->email }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-ts-text-muted hidden md:table-cell">
                        {{ $salon->owner?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="text-sm font-semibold text-ts-text">{{ $salon->staff_count }}</span>
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="text-sm font-semibold text-ts-text">{{ $salon->client_count }}</span>
                    </td>
                    <td class="px-5 py-4 hidden lg:table-cell">
                        <span class="text-sm font-semibold text-ts-text">{{ $salon->appts_count }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($salon->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('salons.show', $salon) }}" class="text-xs font-semibold text-ts-primary hover:underline">View</a>
                            <a href="{{ route('salons.edit', $salon) }}" class="text-xs font-semibold text-ts-text-muted hover:text-ts-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('salons.destroy', $salon) }}"
                                  onsubmit="return confirm('Delete salon {{ addslashes($salon->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-ts-text-subtle text-sm">
                        No salons yet. <a href="{{ route('salons.create') }}" class="text-ts-primary font-semibold hover:underline">Create the first one.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($salons->hasPages())
        <div class="px-5 py-4 border-t border-ts-border-soft">
            {{ $salons->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
