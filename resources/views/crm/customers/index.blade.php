@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Customers</h1>
            <p class="shad-page-description">Manage your CRM customer relationships</p>
        </div>
        <form action="{{ route('export.crm-customers') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="shad-btn shad-btn-outline">
                <i class="fas fa-file-excel mr-2"></i>
                Export to Excel
            </button>
        </form>
    </div>

    @if ($message = Session::get('success'))
        <div class="shad-alert shad-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <div>{{ $message }}</div>
        </div>
    @endif

    <!-- Customers Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Customers</h2>
                <p class="shad-card-description">{{ $customers->total() }} total customers</p>
            </div>
            <input type="text" class="shad-input" id="searchCustomer" placeholder="Search customers..." style="width: 250px;">
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th class="text-center">Deals</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $displayName = $customer->name ?: $customer->customerName ?: 'N/A';
                            @endphp
                            <tr class="customer-row" data-search="{{ strtolower($displayName . ' ' . ($customer->email ?? '') . ' ' . ($customer->company ?? '')) }}">
                                <td style="font-weight: 600; color: var(--slate-800);">{{ $displayName }}</td>
                                <td>{{ $customer->email ?: $customer->customerEmail ?: '-' }}</td>
                                <td>{{ $customer->phone ?: $customer->customerPhone ?: '-' }}</td>
                                <td>{{ $customer->company ?: '-' }}</td>
                                <td>
                                    @if($customer->status == 'active')
                                        <span class="shad-badge shad-badge-success">Active</span>
                                    @elseif($customer->status == 'inactive')
                                        <span class="shad-badge shad-badge-default">Inactive</span>
                                    @elseif($customer->status == 'pending')
                                        <span class="shad-badge shad-badge-warning">Pending</span>
                                    @else
                                        <span class="shad-badge shad-badge-default">{{ $customer->status ?: 'N/A' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="shad-badge shad-badge-info">{{ $customer->deals->count() }}</span>
                                </td>
                                <td>
                                    <div class="shad-actions justify-content-end">
                                        <a href="{{ route('crm.customers.show', $customer->id) }}" class="shad-btn shad-btn-ghost shad-btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="shad-empty">
                                        <div class="shad-empty-icon">
                                            <i class="fas fa-users fa-3x"></i>
                                        </div>
                                        <h3 class="shad-empty-title">No customers found</h3>
                                        <p class="shad-empty-description">Customers will appear here once leads are converted.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="shad-card-footer d-flex justify-content-between align-items-center">
            <small style="color: var(--slate-500);">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }}</small>
            <div class="shad-pagination">
                {{ $customers->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCustomer');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.customer-row').forEach(row => {
                const searchData = row.dataset.search || '';
                row.style.display = searchData.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
