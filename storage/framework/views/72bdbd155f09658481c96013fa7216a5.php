<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome (CDN for reliable icon loading) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- SB Admin 2 CSS (Base) -->
    <link href="<?php echo e(asset('vendor/css/sb-admin-2.min.css')); ?>" rel="stylesheet">

    <!-- shadcn-inspired Design System -->
    <link href="<?php echo e(asset('css/shadcn.css')); ?>" rel="stylesheet">

    <!-- iziToast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">

    <!-- Tailwind CSS (Load LAST to override) -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>

    <!-- Force Tailwind priority -->
    <style>
        /* Override SB Admin background */
        .bg-background { background-color: hsl(var(--background)) !important; }
        .text-foreground { color: hsl(var(--foreground)) !important; }
        .text-muted-foreground { color: hsl(var(--muted-foreground)) !important; }
        .bg-card { background-color: hsl(var(--card)) !important; }
        .text-card-foreground { color: hsl(var(--card-foreground)) !important; }
        .bg-primary { background-color: hsl(var(--primary)) !important; }
        .text-primary { color: hsl(var(--primary)) !important; }
        .text-primary-foreground { color: hsl(var(--primary-foreground)) !important; }
        .border { border-color: hsl(var(--border)) !important; }
        .rounded-lg { border-radius: var(--radius) !important; }
        .rounded-md { border-radius: calc(var(--radius) - 2px) !important; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important; }

        /* Remove SB Admin's main padding */
        #content main.p-4 { padding: 0 !important; }

        /* Ensure Inter font */
        body { font-family: 'Inter', sans-serif !important; }

        /* ============================================ */
        /* CRITICAL FIX: Sidebar submenu horizontal     */
        /* Overrides sb-admin-2.min.css sidebar styles  */
        /* ============================================ */
        #accordionSidebar .shad-submenu {
            writing-mode: horizontal-tb !important;
            display: block !important;
        }
        #accordionSidebar .shad-submenu-item {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            writing-mode: horizontal-tb !important;
            text-orientation: mixed !important;
            transform: none !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            width: auto !important;
            height: auto !important;
            min-height: unset !important;
        }
        #accordionSidebar .shad-submenu-item i,
        #accordionSidebar .shad-submenu-item span,
        #accordionSidebar .shad-submenu-item * {
            writing-mode: horizontal-tb !important;
            text-orientation: mixed !important;
            transform: none !important;
        }
        /* Override SB Admin collapse nav-item height/rotation */
        #accordionSidebar .collapse .nav-item,
        #accordionSidebar .collapsing .nav-item {
            height: auto !important;
        }

        /* ============================================ */
        /* ROOT FIX: Override sb-admin-2 sidebar mobile */
        /* styles that stack items vertically           */
        /* ============================================ */

        /* SB Admin sets span { display: block } and text-align: center
           on .sidebar .nav-item .nav-link — this causes vertical stacking */
        #accordionSidebar .shad-submenu-item {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            text-align: left !important;
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            writing-mode: horizontal-tb !important;
            transform: none !important;
            white-space: nowrap !important;
            height: auto !important;
            min-height: unset !important;
            font-size: 0.8125rem !important;
        }

        /* SB Admin sets span { display: block; font-size: .65rem }
           inside .nav-link — override it for our submenu text */
        #accordionSidebar .shad-submenu-item span,
        #accordionSidebar .shad-submenu-item i {
            display: inline-flex !important;
            font-size: unset !important;
            text-align: left !important;
            writing-mode: horizontal-tb !important;
            transform: none !important;
        }

        #accordionSidebar .shad-submenu-item i {
            font-size: 0.75rem !important;
            width: 1rem !important;
            margin-right: 0.625rem !important;
            flex-shrink: 0 !important;
        }

        #accordionSidebar .shad-submenu {
            display: block !important;
            writing-mode: horizontal-tb !important;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    
    <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            
            <?php echo $__env->make('layouts.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php if(isset($header)): ?>
                <header class="bg-white shadow py-3 px-4">
                    <?php echo e($header); ?>

                </header>
            <?php endif; ?>

            
            <main>
                <?php echo $__env->yieldContent('content'); ?>
            </main>

        </div>
    </div>

</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 1px solid #e4e4e7;">
                <h5 class="modal-title" id="logoutModalLabel" style="font-weight: 600; color: #18181b;">Ready to Leave?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #52525b;">
                Select "Logout" below if you are ready to end your current session.
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e4e4e7;">
                <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="shad-btn shad-btn-primary" style="background: #dc2626; border-color: #dc2626;">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('vendor/jquery-easing/jquery.easing.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/sb-admin-2.min.js')); ?>"></script>

<?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>

<!-- sb-admin-2 sidebar override handled via CSS above -->

<!-- iziToast JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

<!-- ============================================ -->
<!-- Global iziToast Notification System -->
<!-- ============================================ -->
<script>
    // Configure iziToast defaults
    iziToast.settings({
        timeout: 4000,
        position: 'topRight',
        transitionIn: 'fadeInDown',
        transitionOut: 'fadeOutUp',
        progressBar: true,
        progressBarColor: 'rgba(255, 255, 255, 0.6)',
        balloon: false,
        close: true,
        pauseOnHover: true
    });

    // Global notification helper function for AJAX responses
    window.notify = {
        success: function(message, title = 'Success') {
            iziToast.success({
                title: title,
                message: message,
                icon: 'fas fa-check-circle',
                iconColor: '#fff',
                backgroundColor: '#22c55e',
                titleColor: '#fff',
                messageColor: '#fff'
            });
        },
        error: function(message, title = 'Error') {
            iziToast.error({
                title: title,
                message: message,
                icon: 'fas fa-times-circle',
                iconColor: '#fff',
                backgroundColor: '#ef4444',
                titleColor: '#fff',
                messageColor: '#fff',
                timeout: 6000
            });
        },
        warning: function(message, title = 'Warning') {
            iziToast.warning({
                title: title,
                message: message,
                icon: 'fas fa-exclamation-triangle',
                iconColor: '#fff',
                backgroundColor: '#f59e0b',
                titleColor: '#fff',
                messageColor: '#fff'
            });
        },
        info: function(message, title = 'Info') {
            iziToast.info({
                title: title,
                message: message,
                icon: 'fas fa-info-circle',
                iconColor: '#fff',
                backgroundColor: '#3b82f6',
                titleColor: '#fff',
                messageColor: '#fff'
            });
        },
        confirm: function(message, onConfirm, onCancel = null) {
            iziToast.question({
                timeout: false,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                backgroundColor: '#18181b',
                title: 'Confirmation',
                message: message,
                position: 'center',
                buttons: [
                    ['<button><b>Yes</b></button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        if (typeof onConfirm === 'function') onConfirm();
                    }, true],
                    ['<button>Cancel</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        if (typeof onCancel === 'function') onCancel();
                    }]
                ]
            });
        }
    };

    // Handle Laravel session flash messages on page load
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('success')): ?>
            notify.success("<?php echo e(session('success')); ?>");
        <?php endif; ?>

        <?php if(session('error')): ?>
            notify.error("<?php echo e(session('error')); ?>");
        <?php endif; ?>

        <?php if(session('warning')): ?>
            notify.warning("<?php echo e(session('warning')); ?>");
        <?php endif; ?>

        <?php if(session('info')): ?>
            notify.info("<?php echo e(session('info')); ?>");
        <?php endif; ?>

        <?php if(session('message')): ?>
            notify.info("<?php echo e(session('message')); ?>");
        <?php endif; ?>
    });
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/layouts/app.blade.php ENDPATH**/ ?>