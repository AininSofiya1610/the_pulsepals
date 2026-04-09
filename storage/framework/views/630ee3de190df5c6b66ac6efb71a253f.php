<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?php echo e(config('app.name', 'Laravel')); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="<?php echo e(asset('vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet" type="text/css">

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
        .login-container { width: 100%; max-width: 400px; }
        .login-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.5);
        }
        .login-header { text-align: center; margin-bottom: 1.5rem; }
        .login-logo-img {
    width: 140px;       /* laras saiz ikut keperluan */
    height: auto;
   
    margin: 0 auto;
    display: block;
    margin-bottom: 3rem; 
}
        }
        .login-logo i { color: #fafafa; font-size: 1.5rem; }
        .login-title { font-size: 1.25rem; font-weight: 600; color: #18181b; margin-bottom: 0.25rem; }
        .login-subtitle { font-size: 0.875rem; color: #71717a; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.8125rem; font-weight: 500; color: #3f3f46; margin-bottom: 0.375rem; }

        /* Input + eye wrapper */
        .input-wrapper {
            position: relative;
        }
        .input-wrapper input {
            display: block;
            width: 100%;
            padding: 0.625rem 2.75rem 0.625rem 0.75rem;
            font-size: 0.875rem;
            color: #18181b;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .input-wrapper input:focus {
            outline: none;
            border-color: #a1a1aa;
            box-shadow: 0 0 0 2px rgba(161,161,170,0.2);
        }
        .input-wrapper input::placeholder { color: #a1a1aa; }

        /* Eye button */
        .eye-btn {
            position: absolute;
            top: 0; bottom: 0;
            right: 0;
            width: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            cursor: pointer;
            color: #a1a1aa;
            padding: 0;
        }
        .eye-btn:hover { color: #52525b; }
        .eye-btn svg { width: 18px; height: 18px; pointer-events: none; }

        /* Email field (no eye) */
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
            box-shadow: 0 0 0 2px rgba(161,161,170,0.2);
        }
        .form-input::placeholder { color: #a1a1aa; }

        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; }
        .checkbox-group input { width: 1rem; height: 1rem; accent-color: #18181b; }
        .checkbox-group label { font-size: 0.8125rem; color: #52525b; }

        .btn-primary {
            display: block; width: 100%;
            padding: 0.625rem 1rem;
            font-size: 0.875rem; font-weight: 500;
            color: #fafafa; background: #18181b;
            border: none; border-radius: 0.375rem;
            cursor: pointer; transition: background 0.15s ease;
        }
        .btn-primary:hover { background: #27272a; }

        .divider { display: flex; align-items: center; margin: 1.25rem 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e4e4e7; }
        .divider span { padding: 0 0.75rem; font-size: 0.75rem; color: #a1a1aa; }

        .links { text-align: center; }
        .links a { font-size: 0.875rem; color: #52525b; text-decoration: none; transition: color 0.15s ease; }
        .links a:hover { color: #18181b; }

        .alert { padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .alert-success { background: #dcfce7; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-danger { background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
        .alert ul { margin: 0; padding-left: 1.25rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="login-logo-img">
                
                <h1 class="login-title">Welcome back</h1>
                <p class="login-subtitle">Sign in to your Microlab account</p>
            </div>

            <?php if(session('status')): ?>
                <div class="alert alert-success"><?php echo e(session('status')); ?></div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-input" id="email" name="email"
                           value="<?php echo e(old('email')); ?>" placeholder="you@example.com" required autofocus>
                </div>

                <!-- Password with eye toggle -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="eye-btn" onclick="togglePassword()" aria-label="Toggle password">
                            <!-- Eye OPEN (shown by default) -->
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                                       -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <!-- Eye CLOSED (hidden by default) -->
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                                       a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                                       M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59
                                       m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn-primary">Sign in</button>
            </form>

            <div class="divider"><span>or</span></div>

            <div class="links">
                <?php if(app('router')->has('password.request')): ?>
                    <a href="<?php echo e(route('password.request')); ?>">Forgot your password?</a>
                <?php endif; ?>
            </div>
        </div>

        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: #71717a;">
            Don't have an account?
            <a href="<?php echo e(route('register')); ?>" style="color: #18181b; font-weight: 500;">Sign up</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            const input     = document.getElementById('password');
            const eyeOpen   = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            const isHidden  = input.type === 'password';

            input.type           = isHidden ? 'text' : 'password';
            eyeOpen.style.display   = isHidden ? 'none'  : 'block';
            eyeClosed.style.display = isHidden ? 'block' : 'none';
        }
    </script>
</body>
</html>


<?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/auth/login.blade.php ENDPATH**/ ?>