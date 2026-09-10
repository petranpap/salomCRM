<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $todos = Todo::with('assignee')
            ->when($user->isSuperAdmin(), fn($q) => $q)
            ->when($user->hasRole('owner'), function ($q) use ($user) {
                $q->whereHas('assignee', fn($u) => $u->where('salon_id', $user->salon_id))
                  ->orWhereNull('assigned_to');
            })
            ->when(!$user->hasRole('super_admin', 'owner'), fn($q) => $q->where('assigned_to', $user->id))
            ->orderByRaw("FIELD(status, 'open', 'done')")
            ->orderBy('due_date')
            ->paginate(20);

        $staffMembers = $user->hasRole('owner', 'super_admin')
            ? User::whereIn('role', User::SALON_ROLES)
                ->when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
                ->orderBy('name')->get()
            : collect();

        return view('todos.index', compact('todos', 'staffMembers'));
    }

    public function create()
    {
        $user = Auth::user();
        $users = User::whereIn('role', User::SALON_ROLES)
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('name')->get();

        return view('todos.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'due_date'    => 'nullable|date',
            'type'        => 'required|in:inventory,ops',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Todo::create([
            'title'       => $request->title,
            'due_date'    => $request->due_date,
            'type'        => $request->type,
            'assigned_to' => $request->assigned_to ?? Auth::id(),
            'status'      => 'open',
        ]);

        return redirect()->route('todos.index')->with('success', 'Task created.');
    }

    public function show(Todo $todo)
    {
        return redirect()->route('todos.index');
    }

    public function edit(Todo $todo)
    {
        $user = Auth::user();
        $users = User::whereIn('role', User::SALON_ROLES)
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('name')->get();

        return view('todos.edit', compact('todo', 'users'));
    }

    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'required|in:open,done',
        ]);

        $todo->update($request->only('title', 'due_date', 'status'));

        return redirect()->route('todos.index')->with('success', 'Task updated.');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return redirect()->route('todos.index')->with('success', 'Task deleted.');
    }
}
