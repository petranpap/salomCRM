@props(['fieldName', 'hours' => [], 'label' => 'Working Hours', 'limits' => null, 'readonly' => false])

<div>
    @if($label)
    <label class="section-label block mb-2">{{ $label }}</label>
    @endif
    @if($limits)
    <p class="text-[11px] text-espresso-muted mb-2">Hours must fall within the salon's opening hours (shown in the last column).</p>
    @endif
    <div x-data='weeklySchedule(@json($hours), @json($limits))' class="border border-border-warm dark:border-border-warm-dark rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-cream-soft/60 dark:bg-cream-dark-soft/40">
                    <th class="text-left px-4 py-2 section-label font-semibold">Day</th>
                    <th class="text-left px-4 py-2 section-label font-semibold">Open</th>
                    <th class="text-left px-4 py-2 section-label font-semibold">Start</th>
                    <th class="text-left px-4 py-2 section-label font-semibold">End</th>
                    <th class="text-left px-4 py-2 section-label font-semibold">Break <span class="normal-case font-normal">(optional)</span></th>
                    <template x-if="limits">
                        <th class="text-left px-4 py-2 section-label font-semibold">Salon Hours</th>
                    </template>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-warm/50 dark:divide-border-warm-dark/50">
                <template x-for="day in days" :key="day.key">
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-espresso dark:text-cream" x-text="day.label"></td>
                        <td class="px-4 py-2.5">
                            <input type="checkbox" :name="'{{ $fieldName }}[' + day.key + '][active]'" value="1"
                                   x-model="hours[day.key].active"
                                   @if($readonly) disabled @endif
                                   class="rounded border-border-warm text-rose-sand focus:ring-rose-sand disabled:opacity-60">
                        </td>
                        <td class="px-4 py-2.5">
                            <input type="text" :name="'{{ $fieldName }}[' + day.key + '][start]'"
                                   :value="hours[day.key].start" :disabled="{{ $readonly ? 'true' : '!hours[day.key].active' }}"
                                   class="input-field py-1.5 text-sm w-24 disabled:opacity-40 js-time-input" autocomplete="off">
                        </td>
                        <td class="px-4 py-2.5">
                            <input type="text" :name="'{{ $fieldName }}[' + day.key + '][end]'"
                                   :value="hours[day.key].end" :disabled="{{ $readonly ? 'true' : '!hours[day.key].active' }}"
                                   class="input-field py-1.5 text-sm w-24 disabled:opacity-40 js-time-input" autocomplete="off">
                        </td>
                        <td class="px-4 py-2.5">
                            {{-- x-show (not x-if): the break inputs must exist in the DOM from
                                 initial page load, hidden or not, so the global flatpickr
                                 initializer (which only scans .js-time-input once, on
                                 DOMContentLoaded) actually picks them up. An x-if would insert
                                 them fresh on click, after that scan already ran, leaving them
                                 as plain unstyled text inputs instead of time pickers. --}}
                            <button type="button" @click="toggleBreak(day.key)"
                                    x-show="!hours[day.key].hasBreak" x-cloak
                                    :disabled="{{ $readonly ? 'true' : '!hours[day.key].active' }}"
                                    class="text-xs font-semibold text-rose-sand hover:underline disabled:opacity-40 disabled:pointer-events-none whitespace-nowrap">
                                + Add break
                            </button>
                            <div x-show="hours[day.key].hasBreak" x-cloak class="flex items-center gap-1.5 whitespace-nowrap">
                                <input type="text" :name="'{{ $fieldName }}[' + day.key + '][start2]'"
                                       :value="hours[day.key].start2" :disabled="{{ $readonly ? 'true' : '!hours[day.key].active || !hours[day.key].hasBreak' }}"
                                       class="input-field py-1.5 text-sm w-24 disabled:opacity-40 js-time-input" autocomplete="off">
                                <span class="text-espresso-muted">–</span>
                                <input type="text" :name="'{{ $fieldName }}[' + day.key + '][end2]'"
                                       :value="hours[day.key].end2" :disabled="{{ $readonly ? 'true' : '!hours[day.key].active || !hours[day.key].hasBreak' }}"
                                       class="input-field py-1.5 text-sm w-24 disabled:opacity-40 js-time-input" autocomplete="off">
                                @unless($readonly)
                                <button type="button" @click="toggleBreak(day.key)"
                                        class="text-espresso-muted hover:text-rose-sand text-base leading-none px-1" title="Remove break">
                                    &times;
                                </button>
                                @endunless
                            </div>
                        </td>
                        <template x-if="limits">
                            <td class="px-4 py-2.5 text-xs text-espresso-muted" x-text="limitLabel(day.key)"></td>
                        </template>
                    </tr>
                </template>
            </tbody>
        </table>
        </div>
    </div>
</div>
