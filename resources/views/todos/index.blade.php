@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Tasks</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">To-Do List</h1>
        </div>
        <a href="{{ route('todos.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Task
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
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ts-border-soft bg-ts-surface-low">
                    <th class="w-12 px-5 py-3"></th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Task</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Type</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Assigned To</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Due</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @forelse($todos as $todo)
                @php
                    $isDone    = $todo->status === 'done';
                    $isOverdue = !$isDone && $todo->due_date && $todo->due_date->isPast();
                @endphp
                <tr class="hover:bg-ts-surface-low transition-colors {{ $isDone ? 'opacity-60' : '' }}">
                    {{-- Quick toggle checkbox --}}
                    <td class="px-5 py-4 w-12">
                        <form method="POST" action="{{ route('todos.update', $todo) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="title" value="{{ $todo->title }}">
                            <input type="hidden" name="due_date" value="{{ $todo->due_date?->format('Y-m-d') }}">
                            <input type="hidden" name="status" value="{{ $isDone ? 'open' : 'done' }}">
                            <button type="submit"
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                                           {{ $isDone ? 'border-emerald-400 bg-emerald-400' : 'border-ts-border-soft hover:border-ts-primary' }}">
                                @if($isDone)
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-4">
                        <p class="font-medium text-ts-text {{ $isDone ? 'line-through text-ts-text-subtle' : '' }}">
                            {{ $todo->title }}
                        </p>
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $todo->type === 'inventory' ? 'bg-blue-50 text-blue-700' : 'bg-ts-surface-high text-ts-text-muted' }}">
                            {{ ucfirst($todo->type) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-ts-text-muted text-xs hidden md:table-cell">
                        {{ $todo->assignee?->name ?? 'Unassigned' }}
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell">
                        @if($todo->due_date)
                        <span class="text-xs font-medium {{ $isOverdue ? 'text-red-600' : 'text-ts-text-subtle' }}">
                            {{ $todo->due_date->format('M j') }}
                            @if($isOverdue)<span class="text-[10px] ml-1">(overdue)</span>@endif
                        </span>
                        @else
                        <span class="text-xs text-ts-text-subtle">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('todos.edit', $todo) }}"
                               class="text-xs font-semibold text-ts-text-muted hover:text-ts-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('todos.destroy', $todo) }}"
                                  onsubmit="return confirm('Delete this task?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-14 text-center text-ts-text-subtle text-sm">
                        No tasks yet.
                        <a href="{{ route('todos.create') }}" class="text-ts-primary font-semibold hover:underline">Create the first one.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($todos->hasPages())
        <div class="px-5 py-4 border-t border-ts-border-soft">{{ $todos->links() }}</div>
        @endif
    </div>

</div>
@endsection
