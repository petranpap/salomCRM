@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto" x-data="{
        testModalOpen: {{ $errors->has('test_phone') ? 'true' : 'false' }},
        previewModalOpen: false,
        previewTemplate: '{{ old('receipt_template', $salon->receipt_template) }}',
        previewPrimary: '{{ old('receipt_primary_color', $salon->receiptPrimaryColor()) }}',
        previewSecondary: '{{ old('receipt_secondary_color', $salon->receiptSecondaryColor()) }}',
        previewLogoSrc: {{ $salon->logoUrl() ? "'" . $salon->logoUrl() . "'" : 'null' }},
        onLogoChange(event) {
            const file = event.target.files[0];
            this.previewLogoSrc = file ? URL.createObjectURL(file) : this.previewLogoSrc;
        },
    }">
    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Settings</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Salon Details</h1>
        <p class="text-xs text-ts-text-subtle mt-1">This information appears on printed receipts and reports.</p>
    </div>

    @unless($canEdit)
    <div class="mb-4 px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        View only — ask the owner to make changes here.
    </div>
    @endunless

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('salon-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Salon Name *</label>
                <input type="text" name="name" value="{{ old('name', $salon->name) }}" required @disabled(!$canEdit)
                       class="input-field disabled:opacity-60 @error('name') ring-2 ring-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $salon->phone) }}" @disabled(!$canEdit) class="input-field disabled:opacity-60">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $salon->email) }}" @disabled(!$canEdit) class="input-field disabled:opacity-60">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Address</label>
                <input type="text" name="address" value="{{ old('address', $salon->address) }}" @disabled(!$canEdit) class="input-field disabled:opacity-60">
            </div>

            <div class="pt-4 border-t border-ts-border-soft">
                <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Billing / VAT</p>
                <p class="text-xs text-ts-text-subtle mb-3">Leave blank if you're not VAT-registered — receipts will simply show a total with no VAT breakdown.</p>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">VAT Number</label>
                        <input type="text" name="vat_number" value="{{ old('vat_number', $salon->vat_number) }}"
                               placeholder="e.g. CY12345678X" @disabled(!$canEdit) class="input-field disabled:opacity-60">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">VAT Rate (%)</label>
                        <input type="number" name="vat_rate" value="{{ old('vat_rate', $salon->vat_rate) }}"
                               step="0.01" min="0" max="100" placeholder="e.g. 19" @disabled(!$canEdit)
                               class="input-field disabled:opacity-60 @error('vat_rate') ring-2 ring-red-400 @enderror">
                        @error('vat_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-ts-border-soft" x-data="{ provider: '{{ old('sms_driver', $salon->sms_driver) }}' }">
                <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">SMS Reminders</p>
                <p class="text-xs text-ts-text-subtle mb-3">
                    Pick your SMS provider and enter its account details — appointment
                    reminders for customers who've opted into SMS go out through this.
                    Every attempt is written to the SMS log regardless of provider,
                    including while "Not configured" or testing with placeholder details.
                </p>

                <div class="mb-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Provider</label>
                    <select name="sms_driver" x-model="provider" @disabled(!$canEdit) class="input-field disabled:opacity-60">
                        <option value="">Not configured (logged only, nothing sent)</option>
                        <option value="cyta">Cyta (Cyprus)</option>
                    </select>
                </div>

                <div x-show="provider === 'cyta'" x-cloak class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Cyta Username</label>
                        <input type="text" name="sms_credentials[username]"
                               value="{{ old('sms_credentials.username', $salon->sms_credentials['username'] ?? '') }}"
                               @disabled(!$canEdit)
                               class="input-field disabled:opacity-60 @error('sms_credentials.username') ring-2 ring-red-400 @enderror">
                        @error('sms_credentials.username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Cyta Secret Key</label>
                        <input type="password" name="sms_credentials[secret_key]"
                               value="{{ old('sms_credentials.secret_key', $salon->sms_credentials['secret_key'] ?? '') }}"
                               @disabled(!$canEdit)
                               class="input-field disabled:opacity-60 @error('sms_credentials.secret_key') ring-2 ring-red-400 @enderror">
                        @error('sms_credentials.secret_key')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Message Language</label>
                        @php $smsLang = old('sms_credentials.language', $salon->sms_credentials['language'] ?? 'en'); @endphp
                        <select name="sms_credentials[language]" @disabled(!$canEdit) class="input-field disabled:opacity-60">
                            <option value="en" {{ $smsLang === 'en' ? 'selected' : '' }}>English</option>
                            <option value="el" {{ $smsLang === 'el' ? 'selected' : '' }}>Greek</option>
                        </select>
                    </div>
                </div>

                @if($salon->sms_driver)
                <div class="mt-5 pt-4 border-t border-dashed border-ts-border-soft">
                    <button type="button" @click="testModalOpen = true"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-ts-border-soft text-ts-text hover:bg-ts-primary/5 transition">
                        Send a Test SMS
                    </button>
                    <p class="text-xs text-ts-text-subtle mt-2">
                        Sends through the provider and credentials currently saved for this salon.
                    </p>
                </div>
                @endif
            </div>

            <div class="pt-4 border-t border-ts-border-soft" x-data="{ removeLogo: false }">
                <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Receipt Branding</p>
                <p class="text-xs text-ts-text-subtle mb-3">Your logo, brand colors, and layout — shown on every printed receipt.</p>

                <div class="mb-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Logo</label>
                    <div class="flex items-center gap-4">
                        @if($salon->logoUrl())
                        <img src="{{ $salon->logoUrl() }}" alt="{{ $salon->name }} logo"
                             class="h-14 w-14 object-contain rounded-lg border border-ts-border-soft bg-white p-1"
                             :class="{ 'opacity-30': removeLogo }">
                        @endif
                        <div class="flex-1">
                            <input type="file" name="logo" accept="image/png,image/jpeg" @disabled(!$canEdit)
                                   x-on:change="removeLogo = false; onLogoChange($event)"
                                   class="block w-full text-sm text-ts-text-subtle file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-ts-primary/10 file:text-ts-primary disabled:opacity-60">
                            <p class="text-[11px] text-ts-text-subtle mt-1">PNG or JPG, up to 2MB.</p>
                            @error('logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            @if($salon->logoUrl() && $canEdit)
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

                <div class="grid grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Primary Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="receipt_primary_color" x-model="previewPrimary"
                                   @disabled(!$canEdit) class="h-10 w-12 rounded-lg border border-ts-border-soft p-0.5 disabled:opacity-60">
                            <span class="text-xs text-ts-text-subtle font-mono" x-text="previewPrimary"></span>
                        </div>
                        @error('receipt_primary_color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Secondary Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="receipt_secondary_color" x-model="previewSecondary"
                                   @disabled(!$canEdit) class="h-10 w-12 rounded-lg border border-ts-border-soft p-0.5 disabled:opacity-60">
                            <span class="text-xs text-ts-text-subtle font-mono" x-text="previewSecondary"></span>
                        </div>
                        @error('receipt_secondary_color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Receipt Template</label>
                    <select name="receipt_template" x-model="previewTemplate" @disabled(!$canEdit) class="input-field disabled:opacity-60">
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
                <p class="text-xs text-ts-text-subtle mt-2">See how your logo, colors, and each layout look — before saving.</p>
            </div>

            <div class="pt-4 border-t border-ts-border-soft">
                <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Opening Hours</p>
                <p class="text-xs text-ts-text-subtle mb-3">Uncheck a day to mark the salon closed on that day.</p>

                <x-weekly-schedule field-name="opening_hours" label=""
                    :hours="$salon->normalizedOpeningHours()" :readonly="!$canEdit" />
            </div>

            @if($canEdit)
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Save Changes
                </button>
            </div>
            @endif
        </form>
    </div>

    @if($salon->sms_driver)
    <div x-show="testModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
         @keydown.escape.window="testModalOpen = false">
        <div @click.outside="testModalOpen = false"
             class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6 w-full max-w-sm">
            <h2 class="font-display text-lg font-normal text-ts-text mb-1">Send a Test SMS</h2>
            <p class="text-xs text-ts-text-subtle mb-4">
                Sends through the provider and credentials currently saved for this salon.
            </p>

            <form action="{{ route('salon-settings.sms-test') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Phone Number</label>
                    <input type="text" name="test_phone" value="{{ old('test_phone') }}" placeholder="e.g. 99123456" required autofocus
                           class="input-field @error('test_phone') ring-2 ring-red-400 @enderror">
                    @error('test_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                        Send Test
                    </button>
                    <button type="button" @click="testModalOpen = false"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold text-ts-text-subtle hover:bg-ts-primary/5 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Receipt template preview — pure client-side mockup with dummy data, reflecting the
         form's current (not-yet-saved) logo/colors/template so you can compare before saving. --}}
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

            {{-- Mock receipt, scaled down --}}
            <div class="border border-ts-border-soft rounded-xl overflow-hidden text-[11px] leading-snug" style="font-family: ui-serif, serif; color: #2f2a25;">

                {{-- Classic --}}
                <div x-show="previewTemplate === 'classic'" x-cloak class="p-4 text-center" :style="{ borderBottom: '2px solid ' + previewPrimary }">
                    <img :src="previewLogoSrc" x-show="previewLogoSrc" class="h-8 mx-auto mb-1 object-contain">
                    <p class="font-semibold text-sm">{{ $salon->name }}</p>
                    <p class="text-[10px] text-ts-text-subtle" x-show="{{ $salon->address ? 'true' : 'false' }}">{{ $salon->address }}</p>
                </div>

                {{-- Modern --}}
                <div x-show="previewTemplate === 'modern'" x-cloak class="p-4 flex items-center gap-3" :style="{ background: previewPrimary }">
                    <img :src="previewLogoSrc" x-show="previewLogoSrc" class="h-7 object-contain">
                    <p class="font-semibold text-sm text-white">{{ $salon->name }}</p>
                </div>

                {{-- Minimal --}}
                <div x-show="previewTemplate === 'minimal'" x-cloak class="p-4 flex items-center gap-2" :style="{ borderBottom: '1px solid ' + previewPrimary }">
                    <img :src="previewLogoSrc" x-show="previewLogoSrc" class="h-5 object-contain">
                    <p class="font-normal">{{ $salon->name }}</p>
                </div>

                {{-- Shared body: same on all three, sample data only --}}
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
