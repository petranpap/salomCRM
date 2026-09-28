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
                <a href="#faq" class="hover:text-ts-text transition">FAQ</a>
                <a href="#contact" class="hover:text-ts-text transition">Contact</a>
            </nav>
            <div class="flex items-center gap-5">
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

    {{-- FAQ — bilingual (EN/EL), searchable client-side, no page reload --}}
    <section id="faq" class="max-w-3xl mx-auto px-6 sm:px-8 py-20 sm:py-28">
        <div class="max-w-2xl mb-10">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-primary mb-3">FAQ</p>
            <h2 class="font-display text-3xl sm:text-4xl text-ts-text" style="font-family: 'DM Serif Display', serif;">
                Questions, answered.
            </h2>
        </div>

        <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ts-text-subtle pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                </svg>
                <input type="text" id="faq-search" placeholder="Search questions…" aria-label="Search FAQ" autocomplete="off"
                       style="padding-left: 2.5rem;"
                       class="w-full pr-4 py-2.5 rounded-xl border border-ts-border-soft bg-white text-sm text-ts-text focus:outline-none focus:ring-2 focus:ring-ts-primary/30 focus:border-ts-primary transition">
            </div>
            <div class="flex items-center gap-1.5 shrink-0" role="group" aria-label="FAQ language">
                <button type="button" data-faq-lang="en" class="faq-lang-btn whitespace-nowrap px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide border border-ts-border-soft bg-white text-ts-text-muted transition">EN</button>
                <button type="button" data-faq-lang="el" class="faq-lang-btn whitespace-nowrap px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide border border-ts-border-soft bg-white text-ts-text-muted transition">ΕΛ</button>
            </div>
        </div>

        <div id="faq-list-en" class="faq-list space-y-3" lang="en">
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>What is Studio Kassandra?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    A salon management platform built specifically for Cyprus — appointments, staff schedules, payments, and VAT-correct receipts, all in one calm place.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>How do I get started?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    There's no public sign-up on purpose — every salon is set up personally, so it works correctly from day one. Reach out through the contact section below and we'll get you started.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Does it send appointment reminders?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Yes — automatic reminders by SMS (through Cyta) and email, sent about 24 hours before each appointment, with a calendar attachment so your client can add it to their own phone.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Are the receipts VAT-compliant?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Yes. VAT is calculated correctly every time, and receipts carry your salon's own logo and colors.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Can more than one staff member use it?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Yes — each staff member has their own login, calendar, and working hours, including split shifts for a lunch break.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Is my salon's data kept separate from other salons?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Yes. Every salon's data is fully isolated — staff only ever see their own salon's appointments, customers, and payments.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Do I need to install anything?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    No — it runs in any modern browser, on a phone, tablet, or desktop. Nothing to install.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>What if I need help after I'm set up?</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Reach out any time through the contact section below.
                </div>
            </details>
        </div>

        <div id="faq-list-el" class="faq-list space-y-3 hidden" lang="el">
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Τι είναι το Studio Kassandra;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Μια πλατφόρμα διαχείρισης κομμωτηρίου φτιαγμένη ειδικά για την Κύπρο — ραντεβού, προσωπικό, πληρωμές και αποδείξεις με σωστό ΦΠΑ, όλα σε ένα ήρεμο μέρος.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Πώς ξεκινάω;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Δεν υπάρχει δημόσια εγγραφή σκόπιμα — κάθε κομμωτήριο ρυθμίζεται προσωπικά, ώστε να λειτουργεί σωστά από την πρώτη μέρα. Επικοινωνήστε μαζί μας από την ενότητα επικοινωνίας πιο κάτω και θα σας ξεκινήσουμε.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Στέλνει υπενθυμίσεις ραντεβού;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Ναι — αυτόματες υπενθυμίσεις μέσω SMS (μέσω Cyta) και email, περίπου 24 ώρες πριν από κάθε ραντεβού, με συνημμένο αρχείο ημερολογίου ώστε ο πελάτης να το προσθέσει στο δικό του κινητό.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Οι αποδείξεις είναι σύμφωνες με τον ΦΠΑ;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Ναι. Ο ΦΠΑ υπολογίζεται σωστά κάθε φορά, και οι αποδείξεις φέρουν το δικό σας λογότυπο και χρώματα.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Μπορεί να το χρησιμοποιεί περισσότερο από ένα μέλος προσωπικού;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Ναι — κάθε μέλος προσωπικού έχει τη δική του σύνδεση, ημερολόγιο και ωράριο εργασίας, με δυνατότητα διαλείμματος μεσημεριανού.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Τα δεδομένα του κομμωτηρίου μου είναι ξεχωριστά από άλλα κομμωτήρια;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Ναι. Τα δεδομένα κάθε κομμωτηρίου είναι πλήρως απομονωμένα — το προσωπικό βλέπει μόνο τα δικά του ραντεβού, πελάτες και πληρωμές.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Χρειάζεται να εγκαταστήσω κάτι;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Όχι — λειτουργεί σε οποιονδήποτε σύγχρονο browser, σε κινητό, tablet ή υπολογιστή. Τίποτα για εγκατάσταση.
                </div>
            </details>
            <details class="faq-item group bg-ts-surface border border-ts-border-soft rounded-card-lg overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none [&::-webkit-details-marker]:hidden text-sm sm:text-base font-semibold text-ts-text">
                    <span>Τι γίνεται αν χρειαστώ βοήθεια αφού έχω ξεκινήσει;</span>
                    <svg class="w-4 h-4 shrink-0 text-ts-text-subtle transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-5 pb-4 text-sm text-ts-text-muted leading-relaxed">
                    Επικοινωνήστε μαζί μας όποτε χρειαστεί από την ενότητα επικοινωνίας πιο κάτω.
                </div>
            </details>
        </div>

        <p id="faq-empty" class="hidden text-center text-sm text-ts-text-subtle py-10">
            No matching questions — try a different search.
        </p>
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

    <script>
        (function () {
            var searchInput = document.getElementById('faq-search');
            var langButtons = document.querySelectorAll('.faq-lang-btn');
            var lists = { en: document.getElementById('faq-list-en'), el: document.getElementById('faq-list-el') };
            var emptyMessage = document.getElementById('faq-empty');
            var currentLang = 'en';

            function setLang(lang) {
                currentLang = lang;
                lists.en.classList.toggle('hidden', lang !== 'en');
                lists.el.classList.toggle('hidden', lang !== 'el');
                langButtons.forEach(function (button) {
                    var active = button.dataset.faqLang === lang;
                    button.classList.toggle('bg-ts-primary', active);
                    button.classList.toggle('border-ts-primary', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('bg-white', !active);
                    button.classList.toggle('text-ts-text-muted', !active);
                });
                filter();
            }

            function filter() {
                var query = searchInput.value.trim().toLowerCase();
                var items = lists[currentLang].querySelectorAll('.faq-item');
                var visibleCount = 0;

                items.forEach(function (item) {
                    var match = query === '' || item.textContent.toLowerCase().indexOf(query) !== -1;
                    item.classList.toggle('hidden', !match);
                    if (match) visibleCount++;
                });

                emptyMessage.classList.toggle('hidden', visibleCount > 0);
            }

            langButtons.forEach(function (button) {
                button.addEventListener('click', function () { setLang(button.dataset.faqLang); });
            });
            searchInput.addEventListener('input', filter);

            setLang('en');
        })();
    </script>

</body>
</html>
