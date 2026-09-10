<aside class="w-60 shrink-0 flex flex-col ts-sidebar
              fixed inset-y-0 left-0 z-30 transform transition-transform duration-200
              lg:static lg:translate-x-0 print:hidden"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Brand --}}
    <div class="h-16 flex flex-col justify-center px-5 shrink-0 border-b border-ts-border-soft">
        <a href="{{ route('dashboard') }}" class="font-display text-lg leading-tight tracking-tight text-ts-primary">
            {{ config('app.name', 'Studio Kassandra') }}
        </a>
        <span class="text-[10px] font-semibold uppercase tracking-widest text-ts-text-subtle mt-0.5">
            @if(auth()->user()?->isSuperAdmin()) Platform Admin
            @elseif(auth()->user()?->isOwner()) Owner Portal
            @else Staff Portal
            @endif
        </span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        {{-- Main --}}
        <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Main</p>

        @php
            $navMain = [
                ['route' => 'dashboard',          'label' => 'Dashboard',     'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6'],
                ['route' => 'appointments.index', 'label' => 'Appointments',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route' => 'customers.index',    'label' => 'Clients',       'icon' => 'M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8z'],
            ];
        @endphp

        @foreach($navMain as $link)
            @php
                $prefix = Str::before($link['route'], '.index');
                $active = request()->routeIs($link['route']) || ($prefix !== $link['route'] && request()->routeIs($prefix . '.*'));
            @endphp
            <a href="{{ route($link['route']) }}" @click="sidebarOpen = false"
               class="ts-nav-link {{ $active ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach

        {{-- Staff management (owner + super_admin only) --}}
        @if(auth()->user()?->hasRole('owner', 'super_admin'))
        <a href="{{ route('staff.index') }}" @click="sidebarOpen = false"
           class="ts-nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Staff
        </a>

        @php
            $pendingApprovalsCount = \App\Models\PendingChange::where('status', 'pending')->count();
        @endphp
        <a href="{{ route('approvals.index') }}" @click="sidebarOpen = false"
           class="ts-nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }} flex items-center justify-between">
            <span class="flex items-center gap-2.5">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Approvals
            </span>
            @if($pendingApprovalsCount > 0)
            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold bg-ts-primary text-white">
                {{ $pendingApprovalsCount }}
            </span>
            @endif
        </a>
        @endif

        {{-- Operations --}}
        <p class="px-3 pt-4 mb-2 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Operations</p>

        @php
            $navOps = [
                ['route' => 'services.index',            'label' => 'Services',           'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['route' => 'service-categories.index',  'label' => 'Categories',         'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z'],
                ['route' => 'products.index',            'label' => 'Products',           'icon' => 'M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10'],
                ['route' => 'todos.index',               'label' => 'To-Dos',             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-6 9l2 2 4-4'],
            ];
        @endphp

        @foreach($navOps as $link)
            @php
                $prefix = Str::before($link['route'], '.index');
                $active = request()->routeIs($link['route']) || ($prefix !== $link['route'] && request()->routeIs($prefix . '.*'));
            @endphp
            <a href="{{ route($link['route']) }}" @click="sidebarOpen = false"
               class="ts-nav-link {{ $active ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach

        {{-- Finance (owner + super_admin only) --}}
        @if(auth()->user()?->hasRole('owner', 'super_admin'))
        <p class="px-3 pt-4 mb-2 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>

        @php
            $navFinance = [
                ['route' => 'payments.index',         'label' => 'Payments',         'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['route' => 'z-report.index',         'label' => 'Z Report',         'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route' => 'client-insights.index',  'label' => 'Client Insights',  'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2'],
            ];
        @endphp

        @foreach($navFinance as $link)
            @php
                $active = request()->routeIs($link['route']);
            @endphp
            <a href="{{ route($link['route']) }}" @click="sidebarOpen = false"
               class="ts-nav-link {{ $active ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach
        @endif

        {{-- Salon profile / VAT settings (any salon-scoped role) --}}
        @if(auth()->user()?->hasRole(...\App\Models\User::SALON_ROLES))
        <a href="{{ route('salon-settings.edit') }}" @click="sidebarOpen = false"
           class="ts-nav-link {{ request()->routeIs('salon-settings.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Salon Settings
        </a>
        @endif

        {{-- Platform (super_admin only) --}}
        @if(auth()->user()?->isSuperAdmin())
        <p class="px-3 pt-4 mb-2 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Platform</p>
        <a href="{{ route('salons.index') }}" @click="sidebarOpen = false"
           class="ts-nav-link {{ request()->routeIs('salons.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Salons
        </a>
        <a href="{{ route('platform-admins.index') }}" @click="sidebarOpen = false"
           class="ts-nav-link {{ request()->routeIs('platform-admins.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 005 15a1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.6a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            Super Admins
        </a>
        @endif

    </nav>

    {{-- User profile footer --}}
    <div class="px-4 py-4 shrink-0 border-t border-ts-border-soft">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 group">
            <span class="w-9 h-9 rounded-full bg-ts-primary flex items-center justify-center text-white text-sm font-bold shrink-0 group-hover:bg-ts-primary-dim transition">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-ts-text truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-ts-text-subtle capitalize leading-tight mt-0.5">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
            <svg class="w-4 h-4 text-ts-text-subtle shrink-0 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</aside>
