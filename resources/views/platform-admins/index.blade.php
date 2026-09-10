@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Platform Admin</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Super Admins</h1>
        </div>
        <a href="{{ route('platform-admins.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Super Admin
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

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk overflow-hidden">
        <div class="divide-y divide-ts-border-soft">
            @forelse($admins as $admin)
            <div class="flex items-center gap-3 px-5 py-4">
                <span class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-700 text-sm font-bold shrink-0">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </span>
                <div class="flex-1">
                    <p class="font-semibold text-ts-text">{{ $admin->name }}</p>
                    <p class="text-[11px] text-ts-text-subtle">{{ $admin->email }}</p>
                </div>
                @if($admin->isLocked())
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700"
                      title="Locked until {{ $admin->locked_until->format('d/m/Y H:i') }}">Locked</span>
                @endif
                @if($admin->id === auth()->id())
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-ts-surface-low text-ts-text-muted">You</span>
                @elseif($admin->isLocked())
                <form action="{{ route('platform-admins.unlock', $admin) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold px-3 py-1.5 rounded-full border border-ts-border-soft text-ts-text-muted hover:bg-ts-surface-low transition">
                        Unlock
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="px-5 py-12 text-center text-ts-text-subtle text-sm">No super admins yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
