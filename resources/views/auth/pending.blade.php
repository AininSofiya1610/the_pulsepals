<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Pending - {{ config('app.name', 'Laravel') }}</title>
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
        .container {
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
        .icon-wrap {
            width: 64px;
            height: 64px;
            background: #fef9c3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap span { font-size: 2rem; line-height: 1; }
        h1 { font-size: 1.25rem; font-weight: 600; color: #18181b; margin-bottom: 0.75rem; }
        p { font-size: 0.875rem; color: #71717a; line-height: 1.6; margin-bottom: 1.5rem; }
        .user-info {
            background: #f4f4f5;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.8125rem;
            color: #3f3f46;
        }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #fafafa;
            background: #18181b;
            border: none;
            border-radius: 0.375rem;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .btn:hover { background: #27272a; color: #fafafa; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon-wrap">
                <span>😢</span>
            </div>

            <h1>Account Pending</h1>

            <div class="user-info">
                Logged in as <strong>{{ auth()->user()->name }}</strong>
            </div>

            <p>
                Sorry, your account is pending role assignment.<br>
                Please contact an administrator.
            </p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn">
                    <i class="fas fa-sign-out-alt"></i> Back to Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>
