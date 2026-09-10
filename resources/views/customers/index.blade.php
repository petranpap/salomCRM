@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-espresso-subtle dark:text-espresso-muted mb-1">Directory</p>
            <h1 class="font-display text-3xl font-semibold text-espresso dark:text-cream">Customers</h1>
        </div>
        <a href="{{ route('customers.create') }}" class="btn-primary">+ New Customer</a>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream-soft dark:bg-cream-dark-soft border-b border-border-warm dark:border-border-warm-dark">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-espresso-muted">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-espresso-muted">Phone</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-espresso-muted hidden md:table-cell">Email</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-espresso-muted hidden lg:table-cell">Salon</th>
                    @endif
                    <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-espresso-muted hidden sm:table-cell">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-warm dark:divide-border-warm-dark">
                @forelse($customers as $customer)
                <tr class="hover:bg-cream-soft dark:hover:bg-cream-dark-soft transition">
                    <td class="px-6 py-4 font-medium text-espresso dark:text-cream">
                        <a href="{{ route('customers.show', $customer) }}" class="hover:text-rose-sand transition">{{ $customer->name }}</a>
                    </td>
                    <td class="px-6 py-4 text-espresso-muted">{{ $customer->phone ?? '—' }}</td>
                    <td class="px-6 py-4 text-espresso-muted hidden md:table-cell">{{ $customer->email ?? '—' }}</td>
                    @if(auth()->user()->isSuperAdmin())
                    <td class="px-6 py-4 text-espresso-muted hidden lg:table-cell">{{ $customer->salon?->name ?? '—' }}</td>
                    @endif
                    <td class="px-6 py-4 hidden sm:table-cell">
                        @if($customer->flag_type === 'vip')
                            <span class="ts-badge-flag-vip">VIP</span>
                        @elseif($customer->flag_type === 'late')
                            <span class="ts-badge-flag-late">Often Late</span>
                        @elseif($customer->flag_type === 'no_show')
                            <span class="ts-badge-flag-noshow">No-Show Risk</span>
                        @elseif($customer->flag_type === 'flagged')
                            <span class="ts-badge-flag-flagged">Flagged</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('customers.edit', $customer) }}" class="text-xs text-rose-sand hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-espresso-muted">
                        No customers yet. <a href="{{ route('customers.create') }}" class="text-rose-sand hover:underline">Add the first one.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if($customers->hasPages())
        <div class="mt-4">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
