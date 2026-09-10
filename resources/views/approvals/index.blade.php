@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Owner</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Approvals</h1>
        <p class="text-xs text-ts-text-subtle mt-1">Changes submitted by staff wait here until you approve or reject them.</p>
    </div>

    {{-- ── Pending ── --}}
    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        <div class="px-5 py-4 border-b border-ts-border-soft flex items-center justify-between">
            <h2 class="font-display text-base font-normal text-ts-text">Pending</h2>
            <span class="text-xs text-ts-text-subtle">{{ $pending->count() }} awaiting review</span>
        </div>

        @if($pending->isEmpty())
        <div class="px-5 py-12 text-center text-ts-text-subtle text-sm">Nothing to review right now.</div>
        @else
        <div class="divide-y divide-ts-border-soft">
            @foreach($pending as $change)
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">{{ $change->subjectLabel() }} · {{ ucfirst($change->action) }}</p>
                        <p class="font-medium text-ts-text mt-0.5">{{ $change->title() }}</p>
                        <p class="text-xs text-ts-text-subtle mt-1">
                            Submitted by {{ $change->submitter?->name ?? 'Unknown' }} · {{ $change->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('approvals.approve', $change) }}">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition">
                                Approve
                            </button>
                        </form>
                        <button type="button" onclick="document.getElementById('reject-{{ $change->id }}').classList.toggle('hidden')"
                                class="px-4 py-2 rounded-xl text-xs font-semibold border border-ts-border text-ts-text-muted hover:bg-ts-surface-mid transition">
                            Reject
                        </button>
                    </div>
                </div>

                @if($change->action === 'delete')
                <p class="text-xs text-ts-error mt-3">This {{ strtolower($change->subjectLabel()) }} will be permanently deleted.</p>
                @elseif($change->changes())
                <table class="w-full text-xs mt-3">
                    <tbody class="divide-y divide-ts-border-soft">
                        @foreach($change->changes() as $row)
                        <tr>
                            <td class="py-1.5 pr-3 text-ts-text-subtle w-40">{{ $row['label'] }}</td>
                            @if($change->action === 'update')
                            <td class="py-1.5 pr-3 text-ts-text-subtle line-through">{{ $row['from'] ?? '—' }}</td>
                            <td class="py-1.5 text-ts-text font-medium">→ {{ $row['to'] }}</td>
                            @else
                            <td class="py-1.5 text-ts-text font-medium" colspan="2">{{ $row['to'] }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <div id="reject-{{ $change->id }}" class="hidden mt-3 pt-3 border-t border-ts-border-soft">
                    <form method="POST" action="{{ route('approvals.reject', $change) }}" class="flex items-start gap-2">
                        @csrf
                        <input type="text" name="review_notes" placeholder="Reason (optional)…"
                               class="input-field text-sm flex-1">
                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-ts-error-light text-ts-error hover:brightness-95 transition shrink-0">
                            Confirm Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Recently reviewed ── --}}
    @if($reviewed->isNotEmpty())
    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        <div class="px-5 py-4 border-b border-ts-border-soft">
            <h2 class="font-display text-base font-normal text-ts-text">Recently Reviewed</h2>
        </div>
        <div class="divide-y divide-ts-border-soft">
            @foreach($reviewed as $change)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm text-ts-text">{{ $change->title() }}</p>
                    <p class="text-xs text-ts-text-subtle">
                        {{ $change->submitter?->name ?? 'Unknown' }} · reviewed by {{ $change->reviewer?->name ?? '—' }}
                        · {{ $change->reviewed_at?->diffForHumans() }}
                        @if($change->review_notes) · "{{ $change->review_notes }}" @endif
                    </p>
                </div>
                @if($change->status === 'approved')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Approved</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-ts-error-light text-ts-error">Rejected</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
