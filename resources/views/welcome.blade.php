<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Studio Kassandra — Salon management, done right.</title>
    <meta name="description" content="Appointments, staff, payments, and VAT-correct receipts — one calm place to run your salon, built specifically for Cyprus.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mark.svg') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-ts-bg text-ts-text antialiased" style="font-family: 'DM Sans', sans-serif;">

    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-ts-bg/90 backdrop-blur border-b border-ts-border-soft">
        <div class="max-w-6xl mx-auto px-6 sm:px-8 h-16 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="w-5 h-6 shrink-0">
                <span class="font-display text-lg text-ts-text" style="font-family: 'DM Serif Display', serif;">
                    Studio Kassandra
                </span>
            </a>
            <nav class="hidden sm:flex items-center gap-8 text-sm text-ts-text-muted">
                <a href="#features" class="hover:text-ts-text transition">Features</a>
                <a href="#why" class="hover:text-ts-text transition">Why Kassandra</a>
                <a href="#contact" class="hover:text-ts-text transition">Contact</a>
            </nav>
            <div class="flex items-center gap-5">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-ts-text hover:text-ts-primary transition">
                    Log In
                </a>
                <a href="#contact"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    Request a Demo
                </a>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="max-w-6xl mx-auto px-6 sm:px-8 pt-16 sm:pt-24 pb-16 sm:pb-20">
        <div class="max-w-3xl">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-primary mb-4">
                Built for Cyprus salons
            </p>
            <h1 class="font-display text-[2.5rem] leading-[1.1] sm:text-6xl sm:leading-[1.05] text-ts-text"
                style="font-family: 'DM Serif Display', serif;">
                Salon management,<br>done right.
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-ts-text-muted max-w-xl leading-relaxed">
                Appointments, staff, payments, and VAT-correct receipts — one calm place to run
                your salon, built specifically for Cyprus.
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-4">
                <a href="#contact"
                   class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk-md">
                    Request a Demo
                </a>
                <a href="#features"
                   class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold text-ts-text hover:bg-ts-surface-mid transition">
                    See what it does →
                </a>
            </div>
        </div>
    </section>

    {{-- Trust strip --}}
    <section class="border-y border-ts-border-soft bg-ts-surface">
        <div class="max-w-6xl mx-auto px-6 sm:px-8 py-6 flex flex-wrap gap-x-10 gap-y-3 text-sm text-ts-text-muted">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-ts-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                VAT-correct receipts, out of the box
            </span>
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-ts-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                SMS reminders through Cyta
            </span>
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-ts-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Personally set up, not another tool nobody configures right
            </span>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="max-w-6xl mx-auto px-6 sm:px-8 py-20 sm:py-28">
        <div class="max-w-2xl mb-14">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-primary mb-3">What it does</p>
            <h2 class="font-display text-3xl sm:text-4xl text-ts-text" style="font-family: 'DM Serif Display', serif;">
                Everything a salon actually needs — nothing it doesn't.
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 gap-6">
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-7 shadow-silk">
                <h3 class="font-display text-xl text-ts-text mb-2" style="font-family: 'DM Serif Display', serif;">Appointments that respect real hours</h3>
                <p class="text-ts-text-muted leading-relaxed">
                    Close for lunch without a fight — split shifts (e.g. 09:00–13:30, then
                    16:00–18:30) are built in, for the salon and for every stylist. No customer
                    ever gets double-booked.
                </p>
            </div>
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-7 shadow-silk">
                <h3 class="font-display text-xl text-ts-text mb-2" style="font-family: 'DM Serif Display', serif;">Receipts that look like your salon</h3>
                <p class="text-ts-text-muted leading-relaxed">
                    Your logo, your colors, your choice of layout — with the VAT breakdown Cyprus
                    actually requires, calculated correctly every time.
                </p>
            </div>
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-7 shadow-silk">
                <h3 class="font-display text-xl text-ts-text mb-2" style="font-family: 'DM Serif Display', serif;">Fewer no-shows</h3>
                <p class="text-ts-text-muted leading-relaxed">
                    Automatic SMS reminders through Cyta's own network — every attempt logged, so
                    you always know what was actually sent.
                </p>
            </div>
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-7 shadow-silk">
                <h3 class="font-display text-xl text-ts-text mb-2" style="font-family: 'DM Serif Display', serif;">Staff, without the headaches</h3>
                <p class="text-ts-text-muted leading-relaxed">
                    Working hours, calendar colors, and the ability to step someone back from
                    scheduling without erasing a single appointment they ever had.
                </p>
            </div>
        </div>
    </section>

    {{-- Why Kassandra — the signature section --}}
    <section id="why" class="bg-ts-secondary-bg/40 border-y border-ts-border-soft">
        <div class="max-w-2xl mx-auto px-6 sm:px-8 py-20 sm:py-28 text-center">
            <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="w-9 h-auto mx-auto mb-7">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-primary mb-6">Why a mirror</p>
            <p class="font-display text-2xl sm:text-3xl leading-snug text-ts-text" style="font-family: 'DM Serif Display', serif;">
                A hand mirror, drawn plainly. It's a real object in every salon in the world — but
                it's also the mark's whole argument: in Greek myth, Kassandra saw the truth
                clearly. A mirror doesn't flatter or hide anything; it just shows you what's
                actually there. That's the same job this software does for a salon's day —
                appointments, revenue, who's booked when — reflected back plainly, nothing
                dressed up.
            </p>
        </div>
    </section>

    {{-- Contact / CTA --}}
    <section id="contact" class="max-w-6xl mx-auto px-6 sm:px-8 py-20 sm:py-28 text-center">
        <h2 class="font-display text-3xl sm:text-4xl text-ts-text mb-4" style="font-family: 'DM Serif Display', serif;">
            Let's set up your salon.
        </h2>
        <p class="text-ts-text-muted max-w-xl mx-auto mb-8 leading-relaxed">
            There's no self-serve sign-up on purpose — every salon is set up personally, so it
            actually works from day one. Reach out and we'll get you started.
        </p>
        <a href="mailto:peterpapagiannis@yahoo.com?subject=Studio%20Kassandra%20—%20Demo%20Request"
           class="inline-flex items-center px-7 py-4 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk-md">
            Request a Demo
        </a>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-ts-border-soft">
        <div class="max-w-6xl mx-auto px-6 sm:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ts-text-subtle">
            <span>© {{ date('Y') }} Studio Kassandra. Made in Cyprus.</span>
            <span>Salon management, done right.</span>
        </div>
    </footer>

</body>
</html>
