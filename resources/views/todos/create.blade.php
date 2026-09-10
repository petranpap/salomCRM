@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Tasks</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">New Task</h1>
    </div>

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form action="{{ route('todos.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="input-field @error('title') ring-2 ring-red-400 @enderror"
                       placeholder="What needs to be done?">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Type *</label>
                    <select name="type" class="input-field">
                        <option value="ops" {{ old('type') === 'ops' ? 'selected' : '' }}>Operations</option>
                        <option value="inventory" {{ old('type') === 'inventory' ? 'selected' : '' }}>Inventory</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="input-field">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Assign To</label>
                <select name="assigned_to" class="input-field">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', auth()->id()) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    Create Task
                </button>
                <a href="{{ route('todos.index') }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
