<div class="mb-6">
    <div class="flex items-center justify-between mb-2">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">
            Salon Setup — Step {{ $stepIndex }} of {{ $totalSteps }}
        </p>
        <form action="{{ route('onboarding.skip') }}" method="POST">
            @csrf
            <button type="submit" class="text-[11px] font-semibold text-ts-text-subtle hover:text-ts-text underline">
                Skip setup
            </button>
        </form>
    </div>
    <div class="h-1.5 w-full rounded-full bg-ts-border-soft overflow-hidden">
        <div class="h-full rounded-full bg-ts-primary transition-all" style="width: {{ ($stepIndex / $totalSteps) * 100 }}%"></div>
    </div>
</div>
