@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Dropdown Configuration</h1>
            <p class="shad-page-description">Manage system dropdown options for tickets</p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="shad-alert shad-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="shad-alert shad-alert-danger mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Main Card with Tabs -->
    <div class="shad-card">
        <div class="shad-card-header p-0">
            <ul class="nav nav-tabs border-0" id="settingsTab" role="tablist" style="padding: 0 1rem;">
                <li class="nav-item">
                    <a class="nav-link active shad-tab" id="priority-tab" data-toggle="tab" href="#priority" role="tab">
                        <i class="fas fa-flag mr-1"></i> Priorities
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link shad-tab" id="category-tab" data-toggle="tab" href="#category" role="tab">
                        <i class="fas fa-tag mr-1"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link shad-tab" id="ticket-type-tab" data-toggle="tab" href="#ticket-type" role="tab">
                        <i class="fas fa-clipboard-list mr-1"></i> Ticket Types
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link shad-tab" id="units-tab" data-toggle="tab" href="#units" role="tab">
                        <i class="fas fa-sitemap mr-1"></i> Units
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link shad-tab" id="lead-source-tab" data-toggle="tab" href="#lead-source" role="tab">
                        <i class="fas fa-funnel-dollar mr-1"></i> Lead Sources
                    </a>
                </li>

            </ul>
        </div>
        <div class="shad-card-body">
            <div class="tab-content" id="settingsTabContent">
                
                <!-- Priority Tab -->
                <div class="tab-pane fade show active" id="priority" role="tabpanel">
                    <form action="{{ route('settings.dropdown.store') }}" method="POST" class="d-flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" name="type" value="priority">
                        <input type="text" name="value" class="shad-input" placeholder="New priority name..." required style="max-width: 300px;">
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </form>

                    <div class="table-responsive">
                        <table class="shad-table">
                            <thead>
                                <tr>
                                    <th>Priority Name</th>
                                    <th class="text-right" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($priorities as $priority)
                                    <tr>
                                        <td>
                                            <form action="{{ route('settings.dropdown.update', $priority->id) }}" method="POST" id="update-priority-{{ $priority->id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="value" value="{{ $priority->value }}" class="shad-input" style="border: none; background: transparent; padding: 0; font-weight: 500;">
                                            </form>
                                        </td>
                                        <td class="text-right">
                                            <div class="shad-actions justify-content-end">
                                                <form action="{{ route('settings.dropdown.destroy', $priority->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" onclick="return confirm('Delete this priority?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center" style="color: var(--slate-500); padding: 2rem;">No priorities defined.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Category Tab -->
                <div class="tab-pane fade" id="category" role="tabpanel">
                    <form action="{{ route('settings.dropdown.store') }}" method="POST" class="d-flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" name="type" value="category">
                        <input type="text" name="value" class="shad-input" placeholder="New category name..." required style="max-width: 300px;">
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </form>

                    <div class="table-responsive">
                        <table class="shad-table">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th class="text-right" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <form action="{{ route('settings.dropdown.update', $category->id) }}" method="POST" id="update-category-{{ $category->id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="value" value="{{ $category->value }}" class="shad-input" style="border: none; background: transparent; padding: 0; font-weight: 500;">
                                            </form>
                                        </td>
                                        <td class="text-right">
                                            <div class="shad-actions justify-content-end">
                                                <form action="{{ route('settings.dropdown.destroy', $category->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" onclick="return confirm('Delete this category?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center" style="color: var(--slate-500); padding: 2rem;">No categories defined.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ticket Type Tab -->
                <div class="tab-pane fade" id="ticket-type" role="tabpanel">
                    <form action="{{ route('settings.dropdown.store') }}" method="POST" class="d-flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" name="type" value="ticket_type">
                        <input type="text" name="value" class="shad-input" placeholder="e.g. Preventive Maintenance" required style="max-width: 300px;">
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </form>

                    <div class="table-responsive">
                        <table class="shad-table">
                            <thead>
                                <tr>
                                    <th>Ticket Type</th>
                                    <th class="text-right" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketTypes as $type)
                                    <tr>
                                        <td>
                                            <form action="{{ route('settings.dropdown.update', $type->id) }}" method="POST" id="update-type-{{ $type->id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="value" value="{{ $type->value }}" class="shad-input" style="border: none; background: transparent; padding: 0; font-weight: 500;">
                                            </form>
                                        </td>
                                        <td class="text-right">
                                            <div class="shad-actions justify-content-end">
                                                <form action="{{ route('settings.dropdown.destroy', $type->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" onclick="return confirm('Delete this type?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center" style="color: var(--slate-500); padding: 2rem;">No ticket types defined.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Units Tab -->
                <div class="tab-pane fade" id="units" role="tabpanel">
                    <form action="{{ route('settings.units.store') }}" method="POST" class="d-flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="name" class="shad-input" placeholder="New unit name..." required style="max-width: 300px;">
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </form>

                    <div class="table-responsive">
                        <table class="shad-table">
                            <thead>
                                <tr>
                                    <th>Unit Name</th>
                                    <th class="text-right" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($units as $unit)
                                    <tr>
                                        <td>
                                            <form action="{{ route('settings.units.update', $unit->id) }}" method="POST" id="update-unit-{{ $unit->id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $unit->name }}" class="shad-input" style="border: none; background: transparent; padding: 0; font-weight: 500;">
                                            </form>
                                        </td>
                                        <td class="text-right">
                                            <div class="shad-actions justify-content-end">
                                                <form action="{{ route('settings.units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" onclick="return confirm('Delete this unit?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center" style="color: var(--slate-500); padding: 2rem;">No units defined.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Lead Sources Tab -->
<div class="tab-pane fade" id="lead-source" role="tabpanel">
    <form action="{{ route('settings.lead-sources.store') }}" method="POST" class="d-flex gap-2 mb-4">
        @csrf
        <input type="text" name="name" class="shad-input" placeholder="e.g. Website, Referral..." required style="max-width: 300px;">
        <button type="submit" class="shad-btn shad-btn-primary">
            <i class="fas fa-plus"></i> Add
        </button>
    </form>

    <div class="table-responsive">
        <table class="shad-table">
            <thead>
                <tr>
                    <th>Source Name</th>
                    <th class="text-right" style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leadSources as $source)
                    <tr>
                        <td>{{ $source->name }}</td>
                        <td class="text-right">
                            <form action="{{ route('settings.lead-sources.destroy', $source->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" onclick="return confirm('Delete this source?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center" style="color: var(--slate-500); padding: 2rem;">No lead sources defined.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
            </div>
        </div>
    </div>
</div>


<style>
    /* shadcn-inspired tabs */
    .shad-tab {
        color: var(--slate-500);
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.875rem 1rem;
        border: none;
        border-bottom: 2px solid transparent;
        background: none;
        transition: all 0.15s ease;
    }
    .shad-tab:hover {
        color: var(--slate-700);
        background: none;
    }
    .shad-tab.active {
        color: var(--slate-900);
        border-bottom-color: var(--slate-900);
        background: none;
    }
    .nav-tabs {
        border-bottom: 1px solid var(--border-color);
    }
</style>
@endsection
