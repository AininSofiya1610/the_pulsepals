<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password - {{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --background: #09090b;
            --foreground: #fafafa;
            --card: #09090b;
            --primary: #fafafa;
            --primary-foreground: #18181b;
            --muted-foreground: #a1a1aa;
            --border: #27272a;
            --input: #27272a;
            --ring: #d4d4d8;
            --radius: 0.5rem;
        }

        html, body { height: 100%; }

        body {
            background-color: var(--background);
            color: var(--foreground);
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.018) 1px, transparent 1px);
            background-size: 64px 64px;
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 360px;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .brand-icon {
            width: 28px;
            height: 28px;
            background: var(--foreground);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon i { font-size: 13px; color: var(--background); }

        .brand-name {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--foreground);
            letter-spacing: -0.02em;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 4px 6px -1px rgba(0,0,0,0.4),
                0 20px 40px -10px rgba(0,0,0,0.6);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            letter-spacing: -0.025em;
            color: var(--foreground);
            margin-bottom: 0.375rem;
        }

        .card-description {
            font-size: 0.8125rem;
            color: var(--muted-foreground);
            margin-bottom: 1.5rem;
        }

        .form-group { margin-bottom: 1rem; }

        label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--foreground);
            margin-bottom: 0.375rem;
        }

        .input-wrapper { position: relative; }

        .input {
            width: 100%;
            height: 36px;
            padding: 0 2.25rem 0 0.75rem;
            background: transparent;
            border: 1px solid var(--input);
            border-radius: calc(var(--radius) - 2px);
            color: var(--foreground);
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .input.no-icon { padding-right: 0.75rem; }

        .input::placeholder { color: var(--muted-foreground); }

        .input:focus {
            border-color: var(--ring);
            box-shadow: 0 0 0 3px rgba(212, 212, 216, 0.12);
        }

        .input.is-invalid { border-color: #ef4444; }
        .input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }

        .toggle-password {
            position: absolute;
            right: 0.625rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted-foreground);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 0.75rem;
            transition: color 0.15s;
            line-height: 1;
        }

        .toggle-password:hover { color: var(--foreground); }

        .field-error {
            font-size: 0.75rem;
            color: #f87171;
            margin-top: 0.375rem;
        }

        .hint {
            font-size: 0.75rem;
            color: var(--muted-foreground);
            margin-top: 0.375rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 36px;
            padding: 0 1rem;
            background: var(--primary);
            color: var(--primary-foreground);
            border: none;
            border-radius: calc(var(--radius) - 2px);
            font-size: 0.875rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: opacity 0.15s;
            letter-spacing: -0.005em;
            margin-top: 1.25rem;
        }

        .btn:hover { opacity: 0.88; }
        .btn:active { opacity: 0.75; }

        .alert {
            padding: 0.75rem;
            border-radius: calc(var(--radius) - 2px);
            font-size: 0.8125rem;
            margin-bottom: 1rem;
            border: 1px solid;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .alert-danger ul { padding-left: 1rem; margin: 0; }

        .separator { height: 1px; background: var(--border); margin: 1.25rem 0; }

        .card-footer { display: flex; justify-content: center; }

        .card-footer a {
            font-size: 0.8125rem;
            color: var(--muted-foreground);
            text-decoration: none;
            transition: color 0.15s;
        }

        .card-footer a:hover { color: var(--foreground); }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- Brand -->
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-cube"></i></div>
            <span class="brand-name">Microlab</span>
        </div>

        <!-- Card -->
        <div class="card">
            <h1 class="card-title">Reset password</h1>
            <p class="card-description">Enter your new password below.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                {{-- Required hidden fields --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input
                        class="input no-icon @error('email') is-invalid @enderror"
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="form-group">
                    <label for="password">New password</label>
                    <div class="input-wrapper">
                        <input
                            class="input @error('password') is-invalid @enderror"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="toggle-password" onclick="toggleVisibility('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="hint">Minimum 8 characters.</p>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="input-wrapper">
                        <input
                            class="input @error('password_confirmation') is-invalid @enderror"
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="toggle-password" onclick="toggleVisibility('password_confirmation', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn">Reset password</button>
            </form>

            <div class="separator"></div>

            <div class="card-footer">
                <a href="{{ route('login') }}">← Back to login</a>
            </div>
        </div>

    </div>

    <script>
        function toggleVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
