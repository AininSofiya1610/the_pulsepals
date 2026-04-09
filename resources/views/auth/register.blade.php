<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fafafa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .register-container {
            width: 100%;
            max-width: 420px;
        }
        .register-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.5);
        }
        .register-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .register-logo {
            
            width: 140px;       /* laras saiz ikut keperluan */
            height: auto;
        
            margin: 0 auto;
            display: block;
            margin-bottom: 3rem; 
        }
        .register-logo i {
            color: #fafafa;
            font-size: 1.5rem;
        }
        .register-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 0.25rem;
        }
        .register-subtitle {
            font-size: 0.875rem;
            color: #71717a;
        }
        .form-group { margin-bottom: 1rem; }
        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #3f3f46;
            margin-bottom: 0.375rem;
        }
        .form-input {
            display: block;
            width: 100%;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            color: #18181b;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #a1a1aa;
            box-shadow: 0 0 0 2px rgba(161, 161, 170, 0.2);
        }
        .form-input::placeholder { color: #a1a1aa; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .btn-primary {
            display: block;
            width: 100%;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #fafafa;
            background: #18181b;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.15s ease;
            margin-top: 0.5rem;
        }
        .btn-primary:hover { background: #27272a; }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .alert ul { margin: 0; padding-left: 1.25rem; }
        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #71717a;
        }
        .footer-text a {
            color: #18181b;
            font-weight: 500;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">

                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="register-logo">

                <h1 class="register-title">Create an account</h1>
                <p class="register-subtitle">Get started with Microlab</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" class="form-input" id="name" name="name" 
                           value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-input" id="email" name="email" 
                           value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-input" id="password" name="password" 
                               placeholder="••••••••" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm</label>
                        <input type="password" class="form-input" id="password_confirmation" 
                               name="password_confirmation" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Create account</button>
            </form>
        </div>

        <p class="footer-text">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</body>
</html>
