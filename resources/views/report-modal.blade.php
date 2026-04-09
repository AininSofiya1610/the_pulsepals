<!-- Report Generation Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg, 8px); border: 1px solid var(--border-color, #e2e8f0);">
            <!-- Modal Header -->
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color, #e2e8f0); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title" id="reportModalLabel" style="font-weight: 600; font-size: 1.125rem; color: var(--slate-800, #1e293b);">
                        <i class="fas fa-calendar-alt mr-2" style="color: var(--slate-400, #94a3b8);"></i>
                        Generate Dashboard Report
                    </h5>
                    <p style="font-size: 0.875rem; color: var(--slate-500, #64748b); margin: 0.25rem 0 0 0;">
                        Select date range for financial report
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="padding: 1.5rem;">
                <form action="{{ route('dashboard.export') }}" method="POST" id="reportForm">
                    @csrf
                    
                    <!-- Start Date -->
                    <div class="form-group mb-4">
                        <label for="start_date" class="form-label" style="font-weight: 500; color: var(--slate-700); margin-bottom: 0.5rem;">
                            Start Date
                        </label>
                        <input 
                            type="date" 
                            class="form-control @error('start_date') is-invalid @enderror" 
                            id="start_date" 
                            name="start_date" 
                            value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                            required
                            style="padding: 0.625rem; border: 1px solid var(--border-color); border-radius: var(--radius);"
                        >
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="form-group mb-4">
                        <label for="end_date" class="form-label" style="font-weight: 500; color: var(--slate-700); margin-bottom: 0.5rem;">
                            End Date
                        </label>
                        <input 
                            type="date" 
                            class="form-control @error('end_date') is-invalid @enderror" 
                            id="end_date" 
                            name="end_date" 
                            value="{{ old('end_date', now()->format('Y-m-d')) }}"
                            required
                            style="padding: 0.625rem; border: 1px solid var(--border-color); border-radius: var(--radius);"
                        >
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Report Contents Info -->
                    <div class="alert" style="background: var(--slate-50); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1rem; margin-bottom: 0;">
                        <h6 style="font-weight: 600; color: var(--slate-700); margin-bottom: 0.5rem; font-size: 0.875rem;">
                            <i class="fas fa-info-circle mr-2" style="color: var(--slate-400);"></i>
                            Report Will Include:
                        </h6>
                        <ul style="margin: 0; padding-left: 1.5rem; color: var(--slate-600); font-size: 0.8125rem;">
                            <li>Cash Balance Summary (Total, MBB, RHB)</li>
                            <li>Overdue Payments (To Pay)</li>
                            <li>Overdue Collections (To Collect)</li>
                            <li>List of Overdue Vendors</li>
                            <li>List of Overdue Customers</li>
                            <li>Projected Cash Balance Calculation</li>
                        </ul>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 1rem 1.5rem;">
                <button type="button" class="shad-btn shad-btn-ghost" data-bs-dismiss="modal" onclick="closeReportModal()">
                    Cancel
                </button>
                <button type="submit" form="reportForm" class="shad-btn shad-btn-primary">
                    <i class="fas fa-file-excel mr-2"></i>
                    Generate Excel Report
                </button>
            </div>
        </div>
    </div>
</div>

