@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    @include('onboarding._progress')

    <div class="mb-6">
        <h1 class="font-display text-2xl font-normal text-ts-text">Welcome! Let's set up {{ $salon->name }}</h1>
        <p class="text-xs text-ts-text-subtle mt-1">A few quick things before you get started — you can change any of this later in Settings.</p>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('onboarding.store', ['step' => 'details']) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Salon Name *</label>
                <input type="text" name="name" value="{{ old('name', $salon->name) }}" required
                       class="input-field @error('name') ring-2 ring-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $salon->phone) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $salon->email) }}" class="input-field">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Address</label>
                <input type="text" name="address" value="{{ old('address', $salon->address) }}" class="input-field">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Continue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
