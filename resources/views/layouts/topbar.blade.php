<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light topbar mb-0 static-top" style="background: #ffffff; border-bottom: 1px solid #e4e4e7; box-shadow: none; height: 60px;">

    <!-- Sidebar Toggle (Mobile) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" style="color: #71717a;">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Page Title Area (Optional) -->
    <div class="d-none d-md-block">
        {{-- You can add breadcrumbs or page title here --}}
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        
        <!-- User Dropdown -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center py-2 px-3" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 0.375rem;">
                <div class="mr-3 text-right d-none d-lg-block">
                    <span class="d-block" style="font-weight: 500; font-size: 0.875rem; color: #18181b; line-height: 1.2;">{{ Auth::user()->name }}</span>
                    <small style="font-size: 0.75rem; color: #71717a;">{{ Auth::user()->roles->first()->name ?? 'User' }}</small>
                </div>
                <div style="width: 36px; height: 36px; background: #f4f4f5; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-weight: 500; color: #52525b; font-size: 0.875rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </a>
            
            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="userDropdown" style="border: 1px solid #e4e4e7; border-radius: 0.5rem; min-width: 180px; padding: 0.25rem;">
                <a class="dropdown-item d-flex align-items-center" href="#" data-toggle="modal" data-target="#profileModal" style="padding: 0.625rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; color: #3f3f46;">
                    <i class="fas fa-user fa-sm mr-2" style="color: #a1a1aa; width: 1rem;"></i>
                    Profile
                </a>

                <a class="dropdown-item d-flex align-items-center" href="#" data-toggle="modal" data-target="#changePasswordModal" style="padding: 0.625rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; color: #3f3f46;">
                    <i class="fas fa-key fa-sm mr-2" style="color: #a1a1aa; width: 1rem;"></i>
                    Change Password
                </a>
                
                <div style="border-top: 1px solid #e4e4e7; margin: 0.25rem 0;"></div>
                
                <a class="dropdown-item d-flex align-items-center" href="#" data-toggle="modal" data-target="#logoutModal" style="padding: 0.625rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; color: #dc2626;">
                    <i class="fas fa-sign-out-alt fa-sm mr-2" style="color: #dc2626; width: 1rem;"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->

<style>
    /* Topbar dropdown hover */
    .topbar .dropdown-item:hover {
        background: #f4f4f5;
    }
    
    /* User avatar hover */
    .topbar .nav-link:hover {
        background: #f4f4f5;
    }
</style>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 380px;">
        <div class="modal-content" style="border-radius: 0.75rem; border: 1px solid #e4e4e7; overflow: hidden;">
            
            <!-- Header dengan background gelap -->
            <div style="background: #18181b; padding: 1.5rem; text-align: center; position: relative;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="position: absolute; top: 0.75rem; right: 0.75rem; color: #a1a1aa; opacity: 1; font-size: 1.25rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
                
                <!-- Avatar besar -->
                <div style="width: 72px; height: 72px; background: #3f3f46; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #fafafa; font-size: 1.5rem; margin: 0 auto 0.75rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                
                <h6 style="color: #fafafa; font-weight: 600; font-size: 1rem; margin: 0 0 0.25rem;">
                    {{ Auth::user()->name }}
                </h6>
                <span style="font-size: 0.75rem; color: #a1a1aa;">
                    {{ Auth::user()->roles->first()->name ?? 'User' }}
                </span>
            </div>
            
            <!-- Body dengan maklumat -->
            <div style="padding: 1.25rem 1.5rem; background: #fff;">
                
                <!-- Nama Penuh -->
                <div style="margin-bottom: 1rem;">
                    <p style="font-size: 0.7rem; font-weight: 700; color: #3f3f46; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Full Name</p>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-user" style="color: #3f3f46; font-size: 0.8rem; width: 1rem;"></i>
                        <p style="font-size: 0.9rem; font-weight: 500; color: #18181b; margin: 0;">{{ Auth::user()->name }}</p>
                    </div>
                </div>
                
                <div style="border-top: 1px solid #e4e4e7; margin-bottom: 1rem;"></div>
                
                <!-- Email -->
                <div style="margin-bottom: 0.5rem;">
                    <p style="font-size: 0.7rem; font-weight: 700; color: #3f3f46; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Email Address</p>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-envelope" style="color: #3f3f46; font-size: 0.8rem; width: 1rem;"></i>
                        <p style="font-size: 0.9rem; font-weight: 500; color: #18181b; margin: 0;">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                
            </div>
            
            <!-- Footer -->
            <div style="padding: 0.75rem 1.5rem; background: #fafafa; border-top: 1px solid #f4f4f5; text-align: right;">
                <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal" style="font-size: 0.8rem;">
                    Close
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 380px;">
        <div class="modal-content" style="border-radius: 0.75rem; border: 1px solid #e4e4e7; overflow: hidden;">

            <!-- Header -->
            <div style="background: #18181b; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fas fa-key" style="color: #a1a1aa; font-size: 0.9rem;"></i>
                    <h6 style="color: #fafafa; font-weight: 600; font-size: 0.95rem; margin: 0;">Change Password</h6>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #a1a1aa; opacity: 1; font-size: 1.25rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <form id="changePasswordForm" method="POST" action="{{ route('change.password.update') }}">
                @csrf
                <div style="padding: 1.25rem 1.5rem; background: #fff;">

                    <!-- Current Password -->
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.75rem; font-weight: 600; color: #3f3f46; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.4rem;">Current Password</label>
                        <div style="position: relative;">
                            <input type="password" name="current_password" id="current_password"
                                class="form-control"
                                style="font-size: 0.875rem; border: 1px solid #e4e4e7; border-radius: 0.375rem; padding: 0.5rem 2.5rem 0.5rem 0.75rem;"
                                placeholder="Enter current password">
                            <span onclick="togglePassword('current_password', this)"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #a1a1aa;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <small class="text-danger d-none" id="err_current_password"></small>
                    </div>

                    <!-- New Password -->
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.75rem; font-weight: 600; color: #3f3f46; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.4rem;">New Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="new_password"
                                class="form-control"
                                style="font-size: 0.875rem; border: 1px solid #e4e4e7; border-radius: 0.375rem; padding: 0.5rem 2.5rem 0.5rem 0.75rem;"
                                placeholder="Min. 8 characters">
                            <span onclick="togglePassword('new_password', this)"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #a1a1aa;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <small class="text-danger d-none" id="err_password"></small>
                    </div>

                    <!-- Confirm Password -->
                    <div style="margin-bottom: 0.25rem;">
                        <label style="font-size: 0.75rem; font-weight: 600; color: #3f3f46; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.4rem;">Confirm New Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password_confirmation" id="confirm_password"
                                class="form-control"
                                style="font-size: 0.875rem; border: 1px solid #e4e4e7; border-radius: 0.375rem; padding: 0.5rem 2.5rem 0.5rem 0.75rem;"
                                placeholder="Re-enter new password">
                            <span onclick="togglePassword('confirm_password', this)"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #a1a1aa;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <small class="text-danger d-none" id="err_confirm"></small>
                    </div>

                </div>

                <!-- Footer -->
                <div style="padding: 0.75rem 1.5rem; background: #fafafa; border-top: 1px solid #f4f4f5; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-sm" data-dismiss="modal"
                        style="font-size: 0.8rem; border: 1px solid #e4e4e7; border-radius: 0.375rem; color: #3f3f46; background: #fff; padding: 0.4rem 0.9rem;">
                        Cancel
                    </button>
                    <button type="submit" id="savePwdBtn"
                        style="font-size: 0.8rem; background: #18181b; color: #fff; border: none; border-radius: 0.375rem; padding: 0.4rem 0.9rem;">
                        <span id="savePwdText">Update Password</span>
                        <span id="savePwdSpinner" class="d-none">
                            <i class="fas fa-spinner fa-spin"></i> Saving...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- iziToast -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

<script>
    // Show/hide password toggle
    function togglePassword(fieldId, icon) {
        const input = document.getElementById(fieldId);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.querySelector('i').className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
    }

    // AJAX form submit
    document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear previous errors
        ['current_password', 'password', 'confirm'].forEach(key => {
            const el = document.getElementById('err_' + key);
            if (el) { el.textContent = ''; el.classList.add('d-none'); }
        });

        // Client-side confirm check
        const newPwd = document.getElementById('new_password').value;
        const confirmPwd = document.getElementById('confirm_password').value;
        if (newPwd !== confirmPwd) {
            const el = document.getElementById('err_confirm');
            el.textContent = 'Passwords do not match.';
            el.classList.remove('d-none');
            return;
        }

        // Show spinner
        document.getElementById('savePwdText').classList.add('d-none');
        document.getElementById('savePwdSpinner').classList.remove('d-none');

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            // Hide spinner
            document.getElementById('savePwdText').classList.remove('d-none');
            document.getElementById('savePwdSpinner').classList.add('d-none');

            if (data.success) {
                // Close modal
                $('#changePasswordModal').modal('hide');
                // Reset form
                document.getElementById('changePasswordForm').reset();
                // iziToast success
                iziToast.success({
                    title: 'Success',
                    message: data.message,
                    position: 'topRight',
                    timeout: 4000,
                    icon: 'fas fa-check-circle',
                });
            } else {
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const el = document.getElementById('err_' + key);
                        if (el) {
                            el.textContent = data.errors[key][0];
                            el.classList.remove('d-none');
                        }
                    });
                }
                iziToast.error({
                    title: 'Error',
                    message: data.message || 'Please check your input.',
                    position: 'topRight',
                    timeout: 4000,
                });
            }
        })
        .catch(() => {
            document.getElementById('savePwdText').classList.remove('d-none');
            document.getElementById('savePwdSpinner').classList.add('d-none');
            iziToast.error({
                title: 'Error',
                message: 'Something went wrong. Please try again.',
                position: 'topRight',
                timeout: 4000,
            });
        });
    });

    // Reset form & errors when modal closed
    $('#changePasswordModal').on('hidden.bs.modal', function () {
        document.getElementById('changePasswordForm').reset();
        ['current_password', 'password', 'confirm'].forEach(key => {
            const el = document.getElementById('err_' + key);
            if (el) { el.textContent = ''; el.classList.add('d-none'); }
        });
    });
</script>