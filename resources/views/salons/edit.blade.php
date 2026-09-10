@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Platform Admin</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Edit — {{ $salon->name }}</h1>
    </div>

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form method="POST" action="{{ route('salons.update', $salon) }}" class="space-y-5">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Salon Name *</label>
                    <input type="text" name="name" value="{{ old('name', $salon->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $salon->phone) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $salon->email) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address', $salon->address) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', $salon->timezone) }}"
                           placeholder="Europe/Nicosia"
                           class="w-full px-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-ts-text text-sm focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition">
                </div>

                <div class="flex items-center gap-3 mt-auto pb-1">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $salon->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-ts-primary"></div>
                    </label>
                    <span class="text-sm font-medium text-ts-text-muted">Active</span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    Save Changes
                </button>
                <a href="{{ route('salons.show', $salon) }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
                <form method="POST" action="{{ route('salons.destroy', $salon) }}" class="ml-auto"
                      onsubmit="return confirm('Delete this salon? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 border border-red-200 transition">
                        Delete Salon
                    </button>
                </form>
            </div>
        </form>
    </div>

</div>
@endsection
