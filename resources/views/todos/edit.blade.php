@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Tasks</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Edit Task</h1>
    </div>

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form action="{{ route('todos.update', $todo) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Title *</label>
                <input type="text" name="title" value="{{ old('title', $todo->title) }}" required class="input-field">
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Status</label>
                    <select name="status" class="input-field">
                        <option value="open" {{ $todo->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="done" {{ $todo->status === 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $todo->due_date?->format('Y-m-d')) }}" class="input-field">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Assign To</label>
                <select name="assigned_to" class="input-field">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $todo->assigned_to == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    Save Changes
                </button>
                <a href="{{ route('todos.index') }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
                <form method="POST" action="{{ route('todos.destroy', $todo) }}" class="ml-auto"
                      onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 border border-red-200 transition">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
