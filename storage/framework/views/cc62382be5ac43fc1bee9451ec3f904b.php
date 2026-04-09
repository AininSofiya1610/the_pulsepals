<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: #18181b;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center py-3" href="<?php echo e(route('dashboard')); ?>" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="sidebar-brand-icon">
            <i class="fas fa-cube" style="color: #a1a1aa;"></i>
        </div>
        <div class="sidebar-brand-text mx-2" style="font-weight: 600; font-size: 1rem; letter-spacing: -0.025em;">Microlab</div>
    </a>

    <!-- Dashboard -->
    <li class="nav-item <?php echo e(Request::routeIs('dashboard') ? 'active' : ''); ?>" style="margin-top: 0.5rem;">
        <a class="nav-link shad-nav-link" href="<?php echo e(route('dashboard')); ?>">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- ============================================ -->
    <!-- Section: Snapshot -->
    <!-- ============================================ -->
    <div class="shad-sidebar-heading">Snapshot</div>

    <li class="nav-item <?php echo e(Request::routeIs('performance.*', 'projects.*') ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e(Request::routeIs('performance.*', 'projects.*') ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseSnapshot">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Performance</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseSnapshot" class="collapse <?php echo e(Request::routeIs('performance.*', 'projects.*') ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('performance.itsm')); ?>">
                    <i class="fas fa-server"></i>
                    <span>Project Snapshot</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('projects.index')); ?>">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects (RAG)</span>
                </a>
            </div>
        </div>
    </li>

    <!-- ============================================ -->
    <!-- Section: Finance (Admin only) -->
    <!-- ============================================ -->
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-finance')): ?>
    <div class="shad-sidebar-heading">Finance</div>

    <!-- Vendor -->
    <li class="nav-item <?php echo e(Request::routeIs('vendor.*') ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e(Request::routeIs('vendor.*') ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseVendor">
            <i class="fas fa-fw fa-building"></i>
            <span>Vendor</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseVendor" class="collapse <?php echo e(Request::routeIs('vendor.*') ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('vendor.create')); ?>">
                    <i class="fas fa-list"></i>
                    <span>Vendor List</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('vendor.finance.index')); ?>">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Transactions</span>
                </a>
            </div>
        </div>
    </li>

    <!-- Customer (Finance) -->
    <?php
        $isCustomerFinanceActive = Request::routeIs('finance.customers.*', 'customers.finance', 'customers.finance.*');
    ?>
    <li class="nav-item <?php echo e($isCustomerFinanceActive ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e($isCustomerFinanceActive ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseCustomer">
            <i class="fas fa-fw fa-users"></i>
            <span>Customer</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseCustomer" class="collapse <?php echo e($isCustomerFinanceActive ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('customers.create')); ?>">
                    <i class="fas fa-list"></i>
                    <span>Customer List</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('customers.finance')); ?>">
                    <i class="fas fa-receipt"></i>
                    <span>Transactions</span>
                </a>
            </div>
        </div>
    </li>

    <!-- Company -->
    <li class="nav-item <?php echo e(Request::routeIs('finance.company.*', 'settings.revenue-budgets.*') ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e(Request::routeIs('finance.company.*', 'settings.revenue-budgets.*') ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseCompany">
            <i class="fas fa-fw fa-landmark"></i>
            <span>Company</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseCompany" class="collapse <?php echo e(Request::routeIs('finance.company.*', 'settings.revenue-budgets.*') ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('finance.company.index')); ?>">
                    <i class="fas fa-wallet"></i>
                    <span>Balance & Net Pay</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('settings.revenue-budgets.index')); ?>">
                    <i class="fas fa-coins"></i>
                    <span>Revenue Budgets</span>
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?> 

    <!-- ============================================ -->
    <!-- Section: Mini CRM -->
    <!-- ============================================ -->
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-crm')): ?>
    <div class="shad-sidebar-heading">Mini CRM</div>

    <li class="nav-item <?php echo e(Request::routeIs('leads.*', 'crm.*', 'deals.*', 'tasks.*') ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e(Request::routeIs('leads.*', 'crm.*', 'deals.*', 'tasks.*') ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseCRM">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>CRM</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseCRM" class="collapse <?php echo e(Request::routeIs('leads.*', 'crm.*', 'deals.*', 'tasks.*') ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('leads.index')); ?>">
                    <i class="fas fa-user-plus"></i>
                    <span>Leads</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('crm.customers.index')); ?>">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('deals.index')); ?>">
                    <i class="fas fa-project-diagram"></i>
                    <span>Sales Pipeline</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('tasks.index')); ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Tasks</span>
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?> 

    <!-- ============================================ -->
    <!-- Section: Ticket -->
    <!-- ============================================ -->
    <div class="shad-sidebar-heading">TICKET</div>

    <li class="nav-item <?php echo e(Request::routeIs('tickets.*') ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e(Request::routeIs('tickets.*') ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseTicketing">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Ticketing</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseTicketing" class="collapse <?php echo e(Request::routeIs('tickets.*') ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('tickets.index')); ?>">
                    <i class="fas fa-list"></i>
                    <span>All Tickets</span>
                </a>
            </div>
        </div>
    </li>

    <!-- ============================================ -->
    <!-- Section: Maintenance -->
    <!-- ============================================ -->
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['access-settings', 'view users', 'manage roles', 'manage permissions', 'view technicians', 'view units'])): ?>
    <div class="shad-sidebar-heading">Maintenance</div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-settings')): ?>
    
    <li class="nav-item <?php echo e((Request::routeIs('settings.*') && !Request::routeIs('settings.revenue-budgets.*') && !Request::routeIs('settings.roles*')) ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e((Request::routeIs('settings.*') && !Request::routeIs('settings.revenue-budgets.*') && !Request::routeIs('settings.roles*')) ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseSettings">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseSettings" class="collapse <?php echo e((Request::routeIs('settings.*') && !Request::routeIs('settings.revenue-budgets.*') && !Request::routeIs('settings.roles*')) ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <a class="shad-submenu-item" href="<?php echo e(route('settings.dropdown')); ?>">
                    <i class="fas fa-sliders-h"></i>
                    <span>Dropdown</span>
                </a>
                <a class="shad-submenu-item" href="<?php echo e(route('settings.users')); ?>">
                    <i class="fas fa-user-tag"></i>
                    <span>User Roles</span>
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view users', 'manage roles', 'manage permissions', 'view technicians', 'view units'])): ?>
    <li class="nav-item <?php echo e(Request::routeIs('users.*', 'settings.roles*', 'settings.users*', 'units.*') ? 'active-parent' : ''); ?>">
        <a class="nav-link shad-nav-link <?php echo e(Request::routeIs('users.*', 'settings.roles*', 'settings.users*', 'units.*') ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse" data-target="#collapseUsers">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Users</span>
            <i class="fas fa-chevron-down shad-chevron ml-auto"></i>
        </a>
        <div id="collapseUsers" class="collapse <?php echo e(Request::routeIs('users.*', 'settings.roles*', 'settings.users*', 'units.*') ? 'show' : ''); ?>" data-parent="#accordionSidebar">
            <div class="shad-submenu">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view users')): ?>
                <a class="shad-submenu-item" href="<?php echo e(route('users.index')); ?>">
                    <i class="fas fa-users"></i>
                    <span>All Users</span>
                </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage roles')): ?>
                <a class="shad-submenu-item" href="<?php echo e(route('settings.roles')); ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Roles</span>
                </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view technicians')): ?>
                <a class="shad-submenu-item" href="<?php echo e(route('users.technicians')); ?>">
                    <i class="fas fa-user-cog"></i>
                    <span>Technicians</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Sidebar Toggle -->
    <div class="text-center d-none d-md-inline mt-auto pt-3 mb-3">
        <button class="rounded-circle border-0" id="sidebarToggle" style="width: 28px; height: 28px; background: rgba(255,255,255,0.1); color: #a1a1aa;"></button>
    </div>

</ul>

<!-- shadcn-inspired Sidebar Styles -->
<style>
    #accordionSidebar {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .shad-nav-link[data-toggle="collapse"]::after {
        display: none !important;
    }

    .shad-sidebar-heading {
        font-size: 0.6875rem;
        font-weight: 500;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1rem 0.5rem 1rem;
        margin-top: 0.25rem;
    }

    .shad-nav-link {
        display: flex;
        align-items: center;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 400;
        color: #a1a1aa !important;
        border-radius: 0.375rem;
        margin: 0.125rem 0.5rem;
        transition: all 0.15s ease;
    }

    .shad-nav-link i:first-child {
        width: 1.25rem;
        font-size: 0.875rem;
        margin-right: 0.75rem;
    }

    /* FIX: Override SB Admin 2's span { display: block } inside .sidebar .nav-item .nav-link */
    #accordionSidebar .shad-nav-link span {
        display: inline !important;
        font-size: 0.875rem !important;
        flex: 1;
        writing-mode: horizontal-tb !important;
    }

    .shad-nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fafafa !important;
    }

    .nav-item.active .shad-nav-link,
    .nav-item.active-parent .shad-nav-link {
        background: rgba(255, 255, 255, 0.1);
        color: #fafafa !important;
        border-left: 3px solid #3b82f6;
        margin-left: calc(0.5rem - 3px);
    }

    .nav-item.active .shad-nav-link i:first-child,
    .nav-item.active-parent .shad-nav-link i:first-child {
        color: #3b82f6;
    }

    .shad-chevron {
        font-size: 0.625rem;
        transition: transform 0.2s ease;
        color: #71717a;
    }

    .shad-nav-link:not(.collapsed) .shad-chevron {
        /**transform: rotate(180deg);*/
    }

    /* ============================================
       SUBMENU — full fix for SB Admin 2 conflicts
    ============================================ */
    #accordionSidebar .shad-submenu {
        display: block !important;
        padding: 0.25rem 0 0.25rem 0.5rem;
        margin-left: 1rem;
        border-left: 1px solid rgba(255, 255, 255, 0.1);
        writing-mode: horizontal-tb !important;
        text-orientation: mixed !important;
    }

    #accordionSidebar .shad-submenu-item {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        color: #a1a1aa;
        text-decoration: none;
        border-radius: 0.375rem;
        margin: 0.125rem 0.5rem 0.125rem 0;
        transition: all 0.15s ease;
        writing-mode: horizontal-tb !important;
        text-orientation: mixed !important;
        transform: none !important;
        white-space: nowrap;
        width: auto;
        /* Override SB Admin 2 collapse-item styles */
        background-color: transparent;
    }

    #accordionSidebar .shad-submenu-item i {
        display: inline-flex !important;
        align-items: center;
        width: 1rem;
        font-size: 0.75rem;
        margin-right: 0.625rem;
        color: #71717a;
        writing-mode: horizontal-tb !important;
        transform: none !important;
        flex-shrink: 0;
    }

    /* FIX: SB Admin 2 sets span { display: block } globally inside .sidebar — override it */
    #accordionSidebar .shad-submenu-item span {
        display: inline !important;
        font-size: 0.8125rem !important;
        writing-mode: horizontal-tb !important;
        text-orientation: mixed !important;
        transform: none !important;
        white-space: nowrap;
        flex: 1;
    }

    #accordionSidebar .shad-submenu-item:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #fafafa;
    }

    #accordionSidebar .shad-submenu-item:hover i {
        color: #a1a1aa;
    }

    .sidebar.bg-gradient-primary {
        background: #18181b !important;
    }

    /* Toggled (collapsed) sidebar */
    .sidebar.toggled .shad-sidebar-heading,
    .sidebar.toggled .shad-nav-link span,
    .sidebar.toggled .shad-chevron {
        display: none !important;
    }

    .sidebar.toggled .shad-nav-link {
        justify-content: center;
        padding: 0.75rem;
        margin: 0.125rem 0.25rem;
    }

    .sidebar.toggled .shad-nav-link i:first-child {
        margin-right: 0;
    }

    .sidebar.toggled .shad-submenu {
        display: none !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;

    const submenuItems = document.querySelectorAll('.shad-submenu-item');
    submenuItems.forEach(function(item) {
        const href = item.getAttribute('href');
        if (!href) return;

        const isExactMatch = (currentPath === href);
        const isChildMatch = currentPath.startsWith(href + '/');

        if (isExactMatch || isChildMatch) {
            const parentCollapse = item.closest('.collapse');
            if (!parentCollapse) return;

            $(parentCollapse).collapse('show');

            // Buang active-parent dari SEMUA nav-item dahulu, kemudian set hanya pada parent yang betul
            document.querySelectorAll('#accordionSidebar .nav-item').forEach(function(navItem) {
                navItem.classList.remove('active-parent');
            });

            const parentNavItem = parentCollapse.closest('.nav-item');
            if (parentNavItem) {
                parentNavItem.classList.add('active-parent');
            }
        }
    });

    const parentMenuLinks = document.querySelectorAll('.shad-nav-link[data-toggle="collapse"]');
    parentMenuLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            document.querySelectorAll('#accordionSidebar .nav-item').forEach(function(item) {
                item.classList.remove('active-parent');
            });
            const parentNavItem = link.closest('.nav-item');
            if (parentNavItem) {
                parentNavItem.classList.add('active-parent');
            }
        });
    });
});
</script>
<?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>