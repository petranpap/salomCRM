<x-guest-layout>

<style>
    /* ── App name ── */
    .login-title {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        letter-spacing: -0.025em;
        line-height: 1.2;
        color: var(--accent);
        margin-bottom: 32px;
        text-align: center;
    }

    /* ── Role tabs ── */
    .role-tabs {
        width: 100%;
        display: flex;
        gap: 0;
        background: var(--bg-soft);
        border-radius: 10px;
        padding: 4px;
        margin-bottom: 32px;
    }
    .role-tab {
        flex: 1;
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        border-radius: 8px;
        padding: 8px 4px;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all var(--transition);
    }
    .role-tab.active-tab {
        background: var(--bg-card);
        box-shadow: var(--shadow-card);
        color: var(--accent);
    }

    /* ── Field label ── */
    .crm-label {
        display: block;
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.10em;
        text-transform: uppercase;
        color: var(--text-subtle);
        margin-left: 4px;
        margin-bottom: 6px;
    }

    /* ── Input ── */
    .crm-input {
        width: 100%;
        height: 52px;
        padding: 0 16px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--bg);
        font-family: 'DM Sans', sans-serif;
        font-size: 15px;
        color: var(--text);
        outline: none;
        transition: border-color var(--transition), box-shadow var(--transition);
        display: block;
    }
    .crm-input::placeholder { color: var(--text-subtle); }
    .crm-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(181,131,106,0.15);
    }
    .crm-input.error {
        border-color: #B05252;
        box-shadow: 0 0 0 3px rgba(176,82,82,0.12);
    }

    /* ── Password wrapper ── */
    .pw-wrap { position: relative; }
    .pw-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        transition: color var(--transition);
    }
    .pw-toggle:hover { color: var(--accent); }
    .pw-toggle .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 300;
        font-size: 20px;
    }

    /* ── Error message ── */
    .field-error {
        margin-top: 6px;
        margin-left: 4px;
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        color: #B05252;
    }

    /* ── Remember me label ── */
    .remember-label {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        color: var(--text-muted);
        cursor: pointer;
        transition: color var(--transition);
    }
    .remember-label:hover { color: var(--accent); }

    /* ── Submit button ── */
    .crm-btn-primary {
        width: 100%;
        height: 52px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: var(--radius);
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        cursor: pointer;
        box-shadow: var(--shadow-btn);
        transition: background-color var(--transition), transform var(--transition);
    }
    .crm-btn-primary:hover  { background-color: var(--accent-hover); }
    .crm-btn-primary:active { transform: scale(0.98); }

    /* ── Links ── */
    .crm-link {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        color: var(--text-muted);
        text-decoration: none;
        transition: color var(--transition);
    }
    .crm-link:hover { color: var(--accent); text-decoration: underline; }
    .crm-link-accent {
        color: var(--accent);
        font-weight: 600;
        margin-left: 4px;
        text-decoration: none;
        transition: opacity var(--transition);
    }
    .crm-link-accent:hover { text-decoration: underline; }

    /* ── Form divider ── */
    .form-footer {
        width: 100%;
        text-align: center;
        margin-top: 48px;
        padding-top: 24px;
        border-top: 1px solid var(--border-soft);
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        color: var(--text-muted);
    }
</style>

    {{-- App name --}}
    <h1 class="login-title">{{ config('app.name', 'Studio Kassandra') }}</h1>

    {{-- Session status (e.g. password reset link sent) --}}
    @if (session('status'))
        <div style="width:100%; margin-bottom:16px; padding:12px 16px; background:var(--accent-light); border:1px solid var(--border); border-radius:var(--radius); font-family:'DM Sans',sans-serif; font-size:13px; color:var(--accent);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Login form --}}
    <form method="POST" action="{{ route('login') }}" style="width:100%; display:flex; flex-direction:column; gap:16px;">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="crm-label">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your email"
                class="crm-input {{ $errors->has('email') ? 'error' : '' }}"
            />
            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="crm-label">Password</label>
            <div class="pw-wrap">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="crm-input {{ $errors->has('password') ? 'error' : '' }}"
                    style="padding-right: 48px;"
                />
                <button type="button" class="pw-toggle" aria-label="Toggle password visibility"
                        onclick="const f=document.getElementById('password'); f.type=f.type==='password'?'text':'password'; this.querySelector('.material-symbols-outlined').textContent=f.type==='password'?'visibility':'visibility_off';">
                    <span class="material-symbols-outlined">visibility</span>
                </button>
            </div>
            @error('password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div style="display:flex; align-items:center; gap:8px; padding:4px 0 8px;">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                style="width:18px; height:18px; border-color:var(--border); accent-color:var(--accent); border-radius:4px; cursor:pointer;"
            />
            <label for="remember_me" class="remember-label">Keep me signed in</label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="crm-btn-primary">Sign In</button>

        {{-- Forgot password --}}
        @if (Route::has('password.request'))
            <div style="text-align:center; padding-top:8px;">
                <a href="{{ route('password.request') }}" class="crm-link">Forgot password?</a>
            </div>
        @endif

    </form>

    {{-- Support footer --}}
    <div class="form-footer">
        Need technical assistance?
        <a href="#" class="crm-link-accent">Contact Support</a>
    </div>


</x-guest-layout>
