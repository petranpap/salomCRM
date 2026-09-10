@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <p class="section-label">Salon</p>
            <h1 class="page-title">Staff</h1>
        </div>
        <a href="{{ route('staff.create') }}" class="btn-primary">+ Add Staff</a>
    </div>

    <div class="card !p-0 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border-warm dark:border-border-warm-dark">
                    <th class="text-left px-5 py-3 section-label font-semibold">Name</th>
                    <th class="text-left px-5 py-3 section-label font-semibold">Email</th>
                    <th class="text-left px-5 py-3 section-label font-semibold">Role</th>
                    <th class="text-left px-5 py-3 section-label font-semibold">Job Title</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th class="text-left px-5 py-3 section-label font-semibold">Salon</th>
                    @endif
                    <th class="text-left px-5 py-3 section-label font-semibold">Specialty</th>
                    <th class="text-left px-5 py-3 section-label font-semibold">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-warm/50 dark:divide-border-warm-dark/50">
                @forelse($staff as $member)
                <tr class="hover:bg-cream-soft/50 dark:hover:bg-cream-dark-soft/30 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if($member->staffProfile?->color)
                                <span class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                      style="background-color: {{ $member->staffProfile->color }}">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </span>
                            @else
                                <span class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center bg-rose-sand-light text-rose-sand text-xs font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </span>
                            @endif
                            <span class="font-medium text-espresso dark:text-cream">{{ $member->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-espresso-muted">{{ $member->email }}</td>
                    <td class="px-5 py-4">
                        <span class="badge
                            @if($member->role === 'super_admin') bg-red-100 text-red-700
                            @elseif($member->role === 'owner') bg-purple-100 text-purple-700
                            @else bg-rose-sand-light text-rose-sand @endif">
                            {{ $member->role === 'super_admin' ? 'Super Admin' : ucfirst($member->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-espresso-muted">{{ $member->staffProfile?->job_title ?? '—' }}</td>
                    @if(auth()->user()->isSuperAdmin())
                    <td class="px-5 py-4 text-espresso-muted">{{ $member->salon?->name ?? '—' }}</td>
                    @endif
                    <td class="px-5 py-4 text-espresso-muted">{{ $member->staffProfile?->specialty ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            @if($member->staffProfile?->is_active ?? true)
                                <span class="badge bg-emerald-50 text-emerald-700">Active</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                            @if($member->isLocked())
                                <span class="badge bg-amber-50 text-amber-700" title="Locked until {{ $member->locked_until->format('d/m/Y H:i') }}">Locked</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('staff.edit', $member) }}" class="btn-ghost text-xs py-1.5 px-3">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-espresso-muted">
                        No staff members yet. <a href="{{ route('staff.create') }}" class="text-rose-sand hover:underline">Add the first one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if($staff->hasPages())
        <div class="pt-2">{{ $staff->links() }}</div>
    @endif
</div>
@endsection
