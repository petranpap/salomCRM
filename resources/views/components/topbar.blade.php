<header class="h-16 bg-ts-surface border-b border-ts-border-soft flex items-center gap-4 px-6 sticky top-0 z-10 print:hidden">

    {{-- Mobile hamburger --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 rounded-lg text-ts-text-subtle hover:bg-ts-surface-mid transition"
            aria-label="Toggle sidebar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Salon name (desktop) --}}
    <div class="hidden lg:block">
        <span class="font-display text-base font-medium text-ts-text leading-tight">
            {{ auth()->user()->salon?->name ?? config('app.name') }}
        </span>
    </div>

    {{-- Search --}}
    <div class="flex-1 max-w-sm">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ts-text-subtle pointer-events-none"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="search" placeholder="Search appointments…"
                   class="w-full pl-9 pr-4 py-2 text-sm bg-ts-surface-low border border-ts-border-soft rounded-xl
                          text-ts-text placeholder:text-ts-text-subtle
                          focus:outline-none focus:ring-2 focus:ring-ts-primary/20 focus:border-ts-primary transition"/>
        </div>
    </div>

    <div class="flex-1"></div>

    {{-- Dark mode toggle --}}
    <button @click="darkMode = !darkMode"
            class="p-2 rounded-lg text-ts-text-subtle hover:bg-ts-surface-mid transition"
            :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
        </svg>
        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07-6.07l-.71.71M6.34 17.66l-.71.71m12.02 0l-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
        </svg>
    </button>

    {{-- User menu --}}
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" @click.outside="open = false"
                class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-ts-surface-low border border-ts-border-soft
                       text-sm font-medium text-ts-text hover:border-ts-primary transition">
            <span class="w-7 h-7 rounded-full bg-ts-primary flex items-center justify-center text-white font-semibold text-xs">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
            <span class="hidden sm:block">{{ auth()->user()->name }}</span>
            <svg class="w-3.5 h-3.5 text-ts-text-subtle" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="absolute right-0 mt-2 w-44 bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk-md py-1 z-20">
            <a href="{{ route('profile.edit') }}"
               class="block px-4 py-2.5 text-sm text-ts-text hover:bg-ts-surface-low transition">
                Profile
            </a>
            <div class="border-t border-ts-border-soft my-1"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2.5 text-sm text-ts-error hover:bg-ts-error-light transition">
                    Log out
                </button>
            </form>
        </div>
    </div>
</header>
