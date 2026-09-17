<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Studio Kassandra') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mark.svg') }}">
    @vite(['resources/css/app.css'])
    <style>
        .loader {
            --color-1: #7D523C;
            --color-2: #FCF9F6;
            --color-3: rgba(0, 0, 0, 0.1);
            --size: 1px;

            transform: translateZ(calc(1 * var(--size)));
        }

        .loader:after {
            content: '';
            display: inline-block;

            width: calc(48 * var(--size));
            height: calc(48 * var(--size));

            border-radius: 50%;
            box-sizing: border-box;

            background-color: var(--color-1);

            box-shadow:
                calc(2 * var(--size))
                calc(2 * var(--size))
                calc(2 * var(--size))
                calc(1 * var(--size))
                var(--color-3);

            animation: coin-flip 4s cubic-bezier(0, 0.2, 0.8, 1) infinite;

            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 120'%3E%3Cellipse cx='50' cy='42' rx='30' ry='36' fill='none' stroke='%23FCF9F6' stroke-width='8'/%3E%3Cline x1='50' y1='76' x2='50' y2='108' stroke='%23FCF9F6' stroke-width='10' stroke-linecap='round'/%3E%3C/svg%3E");

            background-size: 70% 85%;
            background-position: center;
            background-repeat: no-repeat;
        }

        @keyframes coin-flip {
            0%, 100% {
                animation-timing-function: cubic-bezier(0.5, 0, 1, 0.5);
            }
            0% {
                transform: rotateY(0deg);
            }
            50% {
                transform: rotateY(1800deg);
                animation-timing-function: cubic-bezier(0, 0.5, 0.5, 1);
            }
            100% {
                transform: rotateY(3600deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .loader:after {
                animation: none;
            }
        }
    </style>
</head>
<body class="bg-ts-bg min-h-screen flex flex-col items-center justify-center gap-5">
    <span class="loader" role="status" aria-live="polite" aria-label="Loading"></span>
    <p class="text-sm text-ts-text-subtle" style="font-family: 'DM Sans', sans-serif;">
        Welcome back — one moment…
    </p>

    <script>
        setTimeout(function () {
            window.location.href = @json($next);
        }, 3000);
    </script>
</body>
</html>
