<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Working Hours') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            @if($isApprover)
                {{ __('Your weekly schedule, shown on the appointments calendar.') }}
            @else
                {{ __("Your weekly schedule. Changes need your owner's approval before they take effect.") }}
            @endif
        </p>
    </header>

    @if($pendingWorkingHoursChange)
        <div class="mt-4 px-4 py-3 rounded-md bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            {{ __('You have a change awaiting approval, submitted :time.', ['time' => $pendingWorkingHoursChange->created_at->diffForHumans()]) }}
        </div>
        <div class="mt-4">
            <x-weekly-schedule field-name="preview" label="Requested hours"
                :hours="$pendingWorkingHoursChange->payload['working_hours'] ?? []" :readonly="true" />
        </div>
    @else
        <form method="POST" action="{{ route('profile.working-hours.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <x-weekly-schedule field-name="working_hours" label=""
                :hours="$staffProfile->normalizedWorkingHours()" />

            <div class="flex items-center gap-4">
                <x-primary-button>{{ $isApprover ? __('Save') : __('Request Change') }}</x-primary-button>
            </div>
        </form>
    @endif
</section>
