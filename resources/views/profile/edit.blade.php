@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Account</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Profile</h1>
    </div>

    @if($user->must_change_password)
    <div class="px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        This is a temporary password — set your own below before you can use the rest of the app.
    </div>
    @endif

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    @if($staffProfile)
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.working-hours-form')
        </div>
    </div>
    @endif

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
