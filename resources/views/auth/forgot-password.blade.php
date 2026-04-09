<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password - {{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

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

        .container {
            width: 100%;
            max-width: 400px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .card-logo {
            height: 48px;
            width: auto;
            display: block;
            margin: 0 auto;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 0.25rem;
        }

        .card-description {
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
            font-family: inherit;
            color: #18181b;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #a1a1aa;
            box-shadow: 0 0 0 2px rgba(161, 161, 170, 0.2);
        }

        .form-input::placeholder { color: #a1a1aa; }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: inherit;
            color: #fafafa;
            background: #18181b;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.15s ease;
            margin-top: 0.25rem;
        }

        .btn-primary:hover { background: #27272a; }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.25rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e4e4e7;
        }

        .card-footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .card-footer a {
            font-size: 0.875rem;
            color: #52525b;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .card-footer a:hover { color: #18181b; }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert ul { margin: 0; padding-left: 1.25rem; }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="card-logo">
                <h1 class="card-title">Forgot password?</h1>
                <p class="card-description">Enter your email and we'll send you a reset link.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input
                        type="email"
                        class="form-input"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required
                        autofocus
                    >
                </div>
                <button type="submit" class="btn-primary">Send reset link</button>
            </form>

            <div class="divider"></div>

            <div class="card-footer">
                <a href="{{ route('login') }}">← Back to login</a>
                <a href="{{ route('register') }}">Don't have an account? Sign up</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
