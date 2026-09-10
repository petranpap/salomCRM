<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Salon CRM') }}</title>

    {{-- Fonts: DM Serif Display + Material Symbols (app.css already loads DM Sans + JetBrains Mono) --}}
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── DESIGN.md tokens ── */
        :root {
            --accent:           #B5836A;
            --accent-light:     #F5EDE8;
            --accent-hover:     #8C5E4A;
            --bg:               #FDFAF7;
            --bg-soft:          #F5F0EB;
            --bg-card:          #FFFFFF;
            --text:             #2C2218;
            --text-muted:       #7A6E65;
            --text-subtle:      #ADA49B;
            --border:           #E8E0D8;
            --border-soft:      #F0EBE4;
            --shadow-modal:     0 10px 40px -10px rgba(44,34,24,0.12);
            --shadow-card:      0 2px 8px rgba(44,34,24,0.07);
            --shadow-btn:       0 3px 8px rgba(181,131,106,0.25);
            --radius:           10px;
            --radius-lg:        16px;
            --transition:       0.2s ease;
        }

        html[data-theme="dark"] {
            --bg:           #1A1512;
            --bg-soft:      #221D19;
            --bg-card:      #2A2420;
            --text:         #F5EFE9;
            --text-muted:   #ADA49B;
            --border:       #3A322C;
            --border-soft:  #2E2822;
            --accent:       #D4A088;
            --accent-light: #2E201A;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            overflow: hidden;
            background-color: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Hero panel ── */
        .guest-hero {
            display: none;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 768px) {
            .guest-hero { display: flex; width: 50%; }
        }
        .guest-hero img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .guest-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.08), transparent);
        }

        /* ── Right panel ── */
        .guest-panel {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: var(--bg);
        }
        @media (min-width: 768px) {
            .guest-panel {
                width: 50%;
                padding: 40px;
            }
        }

        /* ── Card ── */
        .guest-card {
            width: 100%;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 48px;
            box-shadow: var(--shadow-modal);
        }

        /* ── Decorative corner icon ── */
        .guest-deco {
            position: fixed;
            bottom: 0;
            right: 0;
            padding: 40px;
            opacity: 0.08;
            pointer-events: none;
        }
        .guest-deco .material-symbols-outlined {
            font-size: 120px;
            color: var(--accent);
        }
    </style>
</head>
<body>

    {{-- Left: Hero image --}}
    <section class="guest-hero">
        <img
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBAEv6w-TM1cY3oAXc48PHIoAIiagc4yyJfcRC96yaZ8UvgKgpWLaJ_K8R7zSBdZT9X8sRrU4lX1UJQuqEcpgcNchVSozKbex262720JGz2gnAv65p9PUAek0uqToGWvfAVqQcf_PrjCwD9IFpI2ctL85zJWxpdw_TpcuMGQBAiIOihquS3uGvuGLl5VHvY2TrE5Jno0Aew7HSW4wVOmlicnxtgld4YzO1ITr3jZ-rVchEJ0EWHqNhiNrA07wY6v83mM70XbPfhzoN6"
            alt="Sophisticated Salon Interior"
            onerror="this.style.background='var(--bg-soft)'; this.style.opacity='0.4';"
        />
        <div class="guest-hero-overlay"></div>
    </section>

    {{-- Right: Login panel --}}
    <main class="guest-panel">
        <div class="guest-card">
            {{ $slot }}
        </div>

        <div class="guest-deco">
            <span class="material-symbols-outlined">spa</span>
        </div>
    </main>

    <script>
        // Parallax on hero image
        const heroImg = document.querySelector('.guest-hero img');
        if (heroImg) {
            document.addEventListener('mousemove', (e) => {
                const x = (e.clientX - window.innerWidth / 2) / 100;
                const y = (e.clientY - window.innerHeight / 2) / 100;
                heroImg.style.transform = `scale(1.05) translate(${x}px, ${y}px)`;
            });
        }
    </script>

</body>
</html>
