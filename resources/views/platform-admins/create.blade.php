@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">
            <a href="{{ route('platform-admins.index') }}" class="hover:text-ts-primary transition">Super Admins</a>
            <span class="mx-1">·</span> Platform Admin
        </p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">New Super Admin</h1>
        <p class="text-xs text-ts-text-subtle mt-1">
            Full, unrestricted access to every salon on the platform. They'll be asked to set
            their own password on first login.
        </p>
    </div>

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form method="POST" action="{{ route('platform-admins.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition @error('name') border-red-400 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition @error('email') border-red-400 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Temporary Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition @error('password') border-red-400 @enderror">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    Create Super Admin
                </button>
                <a href="{{ route('platform-admins.index') }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
