@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto" x-data="{
        previewModalOpen: false,
        previewTemplate: '{{ old('receipt_template', $salon->receipt_template) }}',
        previewPrimary: '{{ old('receipt_primary_color', $salon->receiptPrimaryColor()) }}',
        previewSecondary: '{{ old('receipt_secondary_color', $salon->receiptSecondaryColor()) }}',
        previewLogoSrc: {{ $salon->logoUrl() ? "'" . $salon->logoUrl() . "'" : 'null' }},
        removeLogo: false,
        onLogoChange(event) {
            const file = event.target.files[0];
            this.previewLogoSrc = file ? URL.createObjectURL(file) : this.previewLogoSrc;
        },
    }">
    @include('onboarding._progress')

    <div class="mb-6">
        <h1 class="font-display text-2xl font-normal text-ts-text">Make it look like you</h1>
        <p class="text-xs text-ts-text-subtle mt-1">
            Your logo, brand colors, and receipt layout — shown on every printed receipt. Skip
            this and receipts still work fine with the default look; you can add branding
            anytime in Settings.
        </p>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('onboarding.store', ['step' => 'branding']) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Logo</label>
                <div class="flex items-center gap-4">
                    @if($salon->logoUrl())
                    <img src="{{ $salon->logoUrl() }}" alt="{{ $salon->name }} logo"
                         class="h-14 w-14 object-contain rounded-lg border border-ts-border-soft bg-white p-1"
                         :class="{ 'opacity-30': removeLogo }">
                    @endif
                    <div class="flex-1">
                        <input type="file" name="logo" accept="image/png,image/jpeg"
                               x-on:change="removeLogo = false; onLogoChange($event)"
                               class="block w-full text-sm text-ts-text-subtle file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-ts-primary/10 file:text-ts-primary">
                        <p class="text-[11px] text-ts-text-subtle mt-1">PNG or JPG, up to 2MB.</p>
                        @error('logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @if($salon->logoUrl())
                        <label class="flex items-center gap-1.5 mt-1.5 text-xs text-ts-text-subtle">
                            <input type="checkbox" name="remove_logo" value="1" x-model="removeLogo"
                                   x-on:change="previewLogoSrc = removeLogo ? null : {{ $salon->logoUrl() ? "'" . $salon->logoUrl() . "'" : 'null' }}"
                                   class="rounded border-ts-border-soft text-rose-sand focus:ring-rose-sand">
                            Remove current logo
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Primary Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="receipt_primary_color" x-model="previewPrimary"
                               class="h-10 w-12 rounded-lg border border-ts-border-soft p-0.5">
                        <span class="text-xs text-ts-text-subtle font-mono" x-text="previewPrimary"></span>
                    </div>
                    @error('receipt_primary_color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Secondary Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="receipt_secondary_color" x-model="previewSecondary"
                               class="h-10 w-12 rounded-lg border border-ts-border-soft p-0.5">
                        <span class="text-xs text-ts-text-subtle font-mono" x-text="previewSecondary"></span>
                    </div>
                    @error('receipt_secondary_color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Receipt Template</label>
                <select name="receipt_template" x-model="previewTemplate" class="input-field">
                    @foreach(['classic' => 'Classic — clean, understated', 'modern' => 'Modern — bold color banner', 'minimal' => 'Minimal — mostly black & white'] as $val => $labelText)
                    <option value="{{ $val }}">{{ $labelText }}</option>
                    @endforeach
                </select>
                @error('receipt_template')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="button" @click="previewModalOpen = true"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-ts-border-soft text-ts-text hover:bg-ts-primary/5 transition">
                Preview Templates
            </button>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Finish Setup
                </button>
                <a href="{{ route('onboarding.show', ['step' => 'sms']) }}" class="text-sm text-ts-text-subtle hover:text-ts-text">
                    Back
                </a>
            </div>
        </form>
    </div>

    {{-- Receipt template preview — pure client-side mockup with dummy data, same as Settings. --}}
    <div x-show="previewModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-8"
         @keydown.escape.window="previewModalOpen = false">
        <div @click.outside="previewModalOpen = false"
             class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6 w-full max-w-sm max-h-full overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-lg font-normal text-ts-text">Receipt Preview</h2>
                <button type="button" @click="previewModalOpen = false" class="text-ts-text-subtle hover:text-ts-text text-xl leading-none">&times;</button>
            </div>

            <div class="flex gap-2 mb-4">
                <template x-for="opt in [{v:'classic',l:'Classic'},{v:'modern',l:'Modern'},{v:'minimal',l:'Minimal'}]" :key="opt.v">
                    <button type="button" @click="previewTemplate = opt.v"
                            :class="previewTemplate === opt.v ? 'bg-ts-primary text-white' : 'border border-ts-border-soft text-ts-text-subtle'"
                            class="flex-1 px-2 py-1.5 rounded-lg text-xs font-semibold transition" x-text="opt.l"></button>
                </template>
            </div>

            <div class="border border-ts-border-soft rounded-xl overflow-hidden text-[11px] leading-snug" style="font-family: ui-serif, serif; color: #2f2a25;">

                <div x-show="previewTemplate === 'classic'" x-cloak class="p-4 text-center" :style="{ borderBottom: '2px solid ' + previewPrimary }">
                    <img :src="previewLogoSrc" x-show="previewLogoSrc" class="h-8 mx-auto mb-1 object-contain">
                    <p class="font-semibold text-sm">{{ $salon->name }}</p>
                    <p class="text-[10px] text-ts-text-subtle" x-show="{{ $salon->address ? 'true' : 'false' }}">{{ $salon->address }}</p>
                </div>

                <div x-show="previewTemplate === 'modern'" x-cloak class="p-4 flex items-center gap-3" :style="{ background: previewPrimary }">
                    <img :src="previewLogoSrc" x-show="previewLogoSrc" class="h-7 object-contain">
                    <p class="font-semibold text-sm text-white">{{ $salon->name }}</p>
                </div>

                <div x-show="previewTemplate === 'minimal'" x-cloak class="p-4 flex items-center gap-2" :style="{ borderBottom: '1px solid ' + previewPrimary }">
                    <img :src="previewLogoSrc" x-show="previewLogoSrc" class="h-5 object-contain">
                    <p class="font-normal">{{ $salon->name }}</p>
                </div>

                <div class="px-4 pb-4 pt-3 space-y-2.5">
                    <div class="flex justify-between text-ts-text-subtle text-[10px] uppercase tracking-wide">
                        <span>Sample Client</span>
                        <span>{{ now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-ts-border-soft pt-2">
                        <span>Haircut &amp; Style</span>
                        <span>€35.00</span>
                    </div>
                    <div class="flex justify-between text-ts-text-subtle text-[10px]" x-show="{{ $salon->hasVat() ? 'true' : 'false' }}">
                        <span>VAT ({{ $salon->hasVat() ? rtrim(rtrim(number_format($salon->vat_rate, 2), '0'), '.') : '' }}%)</span>
                        <span>{{ $salon->hasVat() ? '€' . number_format(35 * $salon->vat_rate / (100 + $salon->vat_rate), 2) : '' }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-sm pt-2 border-t" :style="{ borderColor: previewSecondary, color: previewTemplate === 'minimal' ? previewSecondary : previewPrimary }">
                        <span>Total</span>
                        <span>€35.00</span>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-ts-text-subtle mt-3">
                Sample data shown above — save your changes for this to apply to real receipts.
            </p>
        </div>
    </div>
</div>
@endsection
