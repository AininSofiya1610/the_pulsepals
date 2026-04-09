@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Project Management</h1>
            <p class="shad-page-description">Track and manage project delivery status</p>
        </div>
        <div class="d-flex align-items-center" style="gap: 0.5rem;">
            <form action="{{ route('projects.export') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="shad-btn shad-btn-primary" style="background: #18181B; border-color: #18181B;">
                    <i class="fas fa-file-excel mr-2"></i>
                    Generate Report
                </button>
            </form>
            <button class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#projectModal" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Add Project
            </button>
        </div>
    </div>

    {{-- Success/Error messages handled globally by iziToast --}}

    <!-- RAG Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Total Projects</p>
                        <p class="shad-stat-value">{{ $stats['total'] }}</p>
                    </div>
                    <div class="shad-stat-icon primary">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 4px solid #22c55e;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label" style="color: #22c55e !important;">On Track</p>
                        <p class="shad-stat-value" style="color: #22c55e;">{{ $stats['green'] }}</p>
                    </div>
                    <div style="width: 48px; height: 48px; background: rgba(34,197,94,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check" style="color: #22c55e;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 4px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label" style="color: #f59e0b !important;">At Risk</p>
                        <p class="shad-stat-value" style="color: #f59e0b;">{{ $stats['yellow'] }}</p>
                    </div>
                    <div style="width: 48px; height: 48px; background: rgba(245,158,11,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation" style="color: #f59e0b;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 4px solid #ef4444;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label" style="color: #ef4444 !important;">Delayed</p>
                        <p class="shad-stat-value" style="color: #ef4444;">{{ $stats['red'] }}</p>
                    </div>
                    <div style="width: 48px; height: 48px; background: rgba(239,68,68,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times" style="color: #ef4444;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="shad-card">
        <div class="shad-card-header">
            <h2 class="shad-card-title">All Projects</h2>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Description</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Deadline</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr data-project-id="{{ $project->id }}">
                            <td class="font-weight-bold">{{ $project->name }}</td>
                            <td>{{ Str::limit($project->description, 50) }}</td>
                            <td class="text-center" id="status-badge-{{ $project->id }}">
                                @if($project->status === 'green')
                                    <span class="shad-badge shad-badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> On Track
                                    </span>
                                @elseif($project->status === 'yellow')
                                    <span class="shad-badge shad-badge-warning">
                                        <i class="fas fa-exclamation-circle mr-1"></i> At Risk
                                    </span>
                                @else
                                    <span class="shad-badge shad-badge-danger">
                                        <i class="fas fa-times-circle mr-1"></i> Delayed
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($project->deadline)
                                    {{ $project->deadline->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="shad-btn shad-btn-ghost btn-sm" 
                                        onclick="openDetailModal({{ $project->id }})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="shad-btn shad-btn-ghost btn-sm text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                No projects yet. Click "Add Project" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content shad-modal">
            <div class="modal-header shad-modal-header">
                <h5 class="modal-title" id="modalTitle">Add Project</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="projectForm" method="POST" action="{{ route('projects.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="shad-label" for="name">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="shad-input" id="name" name="name" required placeholder="Enter project name">
                    </div>

                    <div class="mb-3">
                        <label class="shad-label" for="deadline">Deadline</label>
                        <input type="date" class="shad-input" id="deadline" name="deadline">
                    </div>

                    <div class="mb-3">
                        <label class="shad-label" for="description">Description</label>
                        <textarea class="shad-input" id="description" name="description" rows="3" placeholder="Brief description of the project"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top bg-gray-50 px-4 py-3">
                    <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary" id="submitBtn">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Project Detail Modal -->
<div class="modal fade" id="projectDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shad-modal">
            <div class="modal-header shad-modal-header">
                <div class="flex-grow-1">
                    <h5 class="modal-title" id="detailModalTitle">Project Details</h5>
                    <p class="text-muted mb-0 small" id="detailModalSubtitle"></p>
                </div>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            
            <div class="modal-body px-4 py-4">
                <!-- Project Info -->
                <div class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="font-weight-bold mb-2" id="detailProjectName"></h6>
                            <p class="text-muted mb-2" id="detailProjectDescription"></p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="mb-2">
                                <span class="text-muted small">Created:</span><br>
                                <strong id="detailCreatedDate">—</strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">Deadline:</span><br>
                                <strong id="detailDeadline">—</strong>
                            </div>
                            <div id="detailStatusBadge"></div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Overall Progress</span>
                        <span class="font-weight-bold" id="detailProgressText">0%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div id="detailProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- 4-Stage Tracker -->
                <div class="mb-3">
                    <h6 class="font-weight-bold mb-3">Process Stages</h6>
                    
                    <!-- Stage 1: Order -->
                    <div class="card mb-3" id="stage1Card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <div id="stage1Icon" class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e5e7eb;">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">📦 Order from Vendor</h6>
                                        <span id="stage1Status" class="badge badge-secondary">Pending</span>
                                    </div>
                                    <div id="stage1Details" class="text-muted small" style="display: none;">
                                        <p class="mb-1"><strong>Order Date:</strong> <span id="stage1Date"></span></p>
                                        <p class="mb-1"><strong>Vendor:</strong> <span id="stage1Vendor"></span></p>
                                        <p class="mb-0"><strong>PO Number:</strong> <span id="stage1PO"></span></p>
                                    </div>
                                    <div id="stage1Form" class="mt-2" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <input type="date" class="form-control form-control-sm" id="stage1DateInput" required>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <input type="text" class="form-control form-control-sm" id="stage1VendorInput" placeholder="Vendor Name">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <input type="text" class="form-control form-control-sm" id="stage1POInput" placeholder="PO Number">
                                            </div>
                                        </div>
                                        <button onclick="saveStage('order')" class="btn btn-sm btn-success mt-1">
                                            <i class="fas fa-check"></i> Mark as Complete
                                        </button>
                                        <button onclick="cancelStageForm(1)" class="btn btn-sm btn-secondary mt-1">Cancel</button>
                                    </div>
                                    <button id="stage1Btn" onclick="showStageForm(1)" class="btn btn-sm btn-outline-primary mt-2" style="display: none;">
                                        <i class="fas fa-plus"></i> Update Stage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 2: Delivery -->
                    <div class="card mb-3" id="stage2Card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <div id="stage2Icon" class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e5e7eb;">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">🚚 Delivery to Microlab</h6>
                                        <span id="stage2Status" class="badge badge-secondary">Pending</span>
                                    </div>
                                    <div id="stage2Details" class="text-muted small" style="display: none;">
                                        <p class="mb-1"><strong>Delivery Date:</strong> <span id="stage2Date"></span></p>
                                        <p class="mb-0"><strong>Received By:</strong> <span id="stage2Person"></span></p>
                                    </div>
                                    <div id="stage2Form" class="mt-2" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="date" class="form-control form-control-sm" id="stage2DateInput" required>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control form-control-sm" id="stage2PersonInput" placeholder="Received By">
                                            </div>
                                        </div>
                                        <button onclick="saveStage('delivery')" class="btn btn-sm btn-success mt-1">
                                            <i class="fas fa-check"></i> Mark as Complete
                                        </button>
                                        <button onclick="cancelStageForm(2)" class="btn btn-sm btn-secondary mt-1">Cancel</button>
                                    </div>
                                    <button id="stage2Btn" onclick="showStageForm(2)" class="btn btn-sm btn-outline-primary mt-2" style="display: none;">
                                        <i class="fas fa-plus"></i> Update Stage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 3: Installation -->
                    <div class="card mb-3" id="stage3Card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <div id="stage3Icon" class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e5e7eb;">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">🔧 Installation/Delivery to Customer</h6>
                                        <span id="stage3Status" class="badge badge-secondary">Pending</span>
                                    </div>
                                    <div id="stage3Details" class="text-muted small" style="display: none;">
                                        <p class="mb-1"><strong>Installation Date:</strong> <span id="stage3Date"></span></p>
                                        <p class="mb-0"><strong>Installed By:</strong> <span id="stage3Person"></span></p>
                                    </div>
                                    <div id="stage3Form" class="mt-2" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="date" class="form-control form-control-sm" id="stage3DateInput" required>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control form-control-sm" id="stage3PersonInput" placeholder="Installed By">
                                            </div>
                                        </div>
                                        <button onclick="saveStage('installation')" class="btn btn-sm btn-success mt-1">
                                            <i class="fas fa-check"></i> Mark as Complete
                                        </button>
                                        <button onclick="cancelStageForm(3)" class="btn btn-sm btn-secondary mt-1">Cancel</button>
                                    </div>
                                    <button id="stage3Btn" onclick="showStageForm(3)" class="btn btn-sm btn-outline-primary mt-2" style="display: none;">
                                        <i class="fas fa-plus"></i> Update Stage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 4: Closing -->
                    <div class="card mb-3" id="stage4Card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <div id="stage4Icon" class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e5e7eb;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">✅ Closing</h6>
                                        <span id="stage4Status" class="badge badge-secondary">Pending</span>
                                    </div>
                                    <div id="stage4Details" class="text-muted small" style="display: none;">
                                        <p class="mb-1"><strong>Closing Date:</strong> <span id="stage4Date"></span></p>
                                        <p class="mb-0"><strong>Notes:</strong> <span id="stage4Notes"></span></p>
                                    </div>
                                    <div id="stage4Form" class="mt-2" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12 mb-2">
                                                <input type="date" class="form-control form-control-sm" id="stage4DateInput" required>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <textarea class="form-control form-control-sm" id="stage4NotesInput" placeholder="Closing notes (optional)" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <button onclick="saveStage('closing')" class="btn btn-sm btn-success mt-1">
                                            <i class="fas fa-check"></i> Mark as Complete
                                        </button>
                                        <button onclick="cancelStageForm(4)" class="btn btn-sm btn-secondary mt-1">Cancel</button>
                                    </div>
                                    <button id="stage4Btn" onclick="showStageForm(4)" class="btn btn-sm btn-outline-primary mt-2" style="display: none;">
                                        <i class="fas fa-plus"></i> Update Stage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top bg-gray-50 px-4 py-3">
                <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add Project';
        document.getElementById('projectForm').action = '{{ route("projects.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('name').value = '';
        document.getElementById('deadline').value = '';
        document.getElementById('description').value = '';
        document.getElementById('submitBtn').textContent = 'Save Project';
    }

    function openEditModal(id, name, status, deadline, description) {
        document.getElementById('modalTitle').textContent = 'Edit Project';
        document.getElementById('projectForm').action = '/projects/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('name').value = name;
        document.getElementById('status').value = status;
        document.getElementById('deadline').value = deadline || '';
        document.getElementById('description').value = description;
        document.getElementById('submitBtn').textContent = 'Update Project';
        $('#projectModal').modal('show');
    }

    // Store current project data globally for detail modal
    let currentProjectData = null;

    // Open detail modal with project data
    function openDetailModal(projectId) {
        // Fetch project data from server
        fetch(`/projects/${projectId}`)
            .then(response => response.json())
            .then(project => {
                currentProjectData = project;
                populateDetailModal(project);
                $('#projectDetailModal').modal('show');
            })
            .catch(error => {
                console.error('Error fetching project:', error);
                iziToast.error({ title: 'Error', message: 'Failed to load project details' });
            });
    }

    // Populate detail modal with project data
    function populateDetailModal(project) {
        // Header info
        document.getElementById('detailModalTitle').textContent = 'Project: ' + project.name;
        document.getElementById('detailProjectName').textContent = project.name;
        document.getElementById('detailProjectDescription').textContent = project.description || 'No description';
        document.getElementById('detailCreatedDate').textContent = project.created_formatted || '—';
        document.getElementById('detailDeadline').textContent = project.deadline_formatted || '—';
        
        // Status badge
        let statusBadge = '';
        if (project.status === 'green') {
            statusBadge = '<span class="shad-badge shad-badge-success"><i class="fas fa-check-circle mr-1"></i> On Track</span>';
        } else if (project.status === 'yellow') {
            statusBadge = '<span class="shad-badge shad-badge-warning"><i class="fas fa-exclamation-circle mr-1"></i> At Risk</span>';
        } else {
            statusBadge = '<span class="shad-badge shad-badge-danger"><i class="fas fa-times-circle mr-1"></i> Delayed</span>';
        }
        document.getElementById('detailStatusBadge').innerHTML = statusBadge;
        
        // Progress
        const progress = project.progress || 0;
        document.getElementById('detailProgressText').textContent = progress + '%';
        document.getElementById('detailProgressBar').style.width = progress + '%';
        
        // Update stages
        updateStageUI(1, project.order_date, {vendor: project.vendor_name, po: project.po_number});
        updateStageUI(2, project.delivery_date, {person: project.received_by});
        updateStageUI(3, project.installation_date, {person: project.installed_by});
        updateStageUI(4, project.closing_date, {notes: project.closing_notes});
    }

    // Update stage UI based on completion status 
    function updateStageUI(stageNum, completionDate, extraData = {}) {
        const completed = !!completionDate;
        const stageIcon = document.getElementById(`stage${stageNum}Icon`);
        const stageStatus = document.getElementById(`stage${stageNum}Status`);
        const stageDetails = document.getElementById(`stage${stageNum}Details`);
        const stageBtn = document.getElementById(`stage${stageNum}Btn`);
        
        if (completed) {
            // Mark as complete
            stageIcon.style.background = '#22c55e';
            stageIcon.style.color = 'white';
            stageStatus.className = 'badge badge-success';
            stageStatus.textContent = 'Complete';
            stageDetails.style.display = 'block';
            stageBtn.style.display = 'none';
            
            // Fill in details
            if (stageNum === 1 && extraData) {
                document.getElementById('stage1Date').textContent = completionDate;
                document.getElementById('stage1Vendor').textContent = extraData.vendor || '—';
                document.getElementById('stage1PO').textContent = extraData.po || '—';
            } else if (stageNum === 2 && extraData) {
                document.getElementById('stage2Date').textContent = completionDate;
                document.getElementById('stage2Person').textContent = extraData.person || '—';
            } else if (stageNum === 3 && extraData) {
                document.getElementById('stage3Date').textContent = completionDate;
                document.getElementById('stage3Person').textContent = extraData.person || '—';
            } else if (stageNum === 4 && extraData) {
                document.getElementById('stage4Date').textContent = completionDate;
                document.getElementById('stage4Notes').textContent = extraData.notes || 'No notes';
            }
        } else {
            // Mark as pending
            stageIcon.style.background = '#e5e7eb';
            stageIcon.style.color = '#6b7280';
            stageStatus.className = 'badge badge-secondary';
            stageStatus.textContent = 'Pending';
            stageDetails.style.display = 'none';
            stageBtn.style.display = 'inline-block';
        }
    }

    // Show stage update form
    function showStageForm(stageNum) {
        const form = document.getElementById(`stage${stageNum}Form`);
        const btn = document.getElementById(`stage${stageNum}Btn`);
        
        form.style.display = 'block';
        btn.style.display = 'none';
        
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById(`stage${stageNum}DateInput`).value = today;
    }

    // Cancel stage form
    function cancelStageForm(stageNum) {
        const form = document.getElementById(`stage${stageNum}Form`);
        const btn = document.getElementById(`stage${stageNum}Btn`);
        
        form.style.display = 'none';
        btn.style.display = 'inline-block';
    }

    // Save stage via AJAX
    function saveStage(stageName) {
        if (!currentProjectData || !currentProjectData.id) {
            iziToast.error({ title: 'Error', message: 'Project data not loaded' });
            return;
        }
        
        let data = { stage: stageName };
        let stageNum;
        
        // Collect form data based on stage
        if (stageName === 'order') {
            stageNum = 1;
            data.date = document.getElementById('stage1DateInput').value;
            data.vendor_name = document.getElementById('stage1VendorInput').value;
            data.po_number = document.getElementById('stage1POInput').value;
        } else if (stageName === 'delivery') {
            stageNum = 2;
            data.date = document.getElementById('stage2DateInput').value;
            data.person = document.getElementById('stage2PersonInput').value;
        } else if (stageName === 'installation') {
            stageNum = 3;
            data.date = document.getElementById('stage3DateInput').value;
            data.person = document.getElementById('stage3PersonInput').value;
        } else if (stageName === 'closing') {
            stageNum = 4;
            data.date = document.getElementById('stage4DateInput').value;
            data.notes = document.getElementById('stage4NotesInput').value;
        }
        
        if (!data.date) {
            iziToast.warning({ title: 'Required', message: 'Please select a date' });
            return;
        }
        
        // Send AJAX request
        fetch(`/projects/${currentProjectData.id}/stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                iziToast.success({ title: 'Success', message: result.message });
                
                // Update UI
                let extraData = {};
                if (stageName === 'order') {
                    extraData = { vendor: data.vendor_name, po: data.po_number };
                } else if (stageName === 'delivery' || stageName === 'installation') {
                    extraData = { person: data.person };
                } else if (stageName === 'closing') {
                    extraData = { notes: data.notes };
                }
                
                updateStageUI(stageNum, data.date, extraData);
                
                // Update progress bar
                document.getElementById('detailProgressText').textContent = result.progress + '%';
                document.getElementById('detailProgressBar').style.width = result.progress + '%';
                
                // Update status badge
                let statusBadge = '';
                if (result.status === 'green') {
                    statusBadge = '<span class="shad-badge shad-badge-success"><i class="fas fa-check-circle mr-1"></i> On Track</span>';
                } else if (result.status === 'yellow') {
                    statusBadge = '<span class="shad-badge shad-badge-warning"><i class="fas fa-exclamation-circle mr-1"></i> At Risk</span>';
                } else {
                    statusBadge = '<span class="shad-badge shad-badge-danger"><i class="fas fa-times-circle mr-1"></i> Delayed</span>';
                }
                document.getElementById('detailStatusBadge').innerHTML = statusBadge;
                
                // Update table row status badge (so main page updates too!)
                const tableBadgeCell = document.getElementById(`status-badge-${currentProjectData.id}`);
                if (tableBadgeCell) {
                    tableBadgeCell.innerHTML = statusBadge;
                }
                
                // Hide form
                cancelStageForm(stageNum);
            } else {
                iziToast.error({ title: 'Error', message: result.message || 'Failed to update stage' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            iziToast.error({ title: 'Error', message: 'Failed to update stage' });
        });
    }

    // Open edit modal from detail modal
    function openEditFromDetail() {
        if (!currentProjectData) return;
        
        $('#projectDetailModal').modal('hide');
        
        setTimeout(() => {
            openEditModal(
                currentProjectData.id,
                currentProjectData.name,
                currentProjectData.status,
                currentProjectData.deadline,
                currentProjectData.description
            );
        }, 300);
    }
</script>
@endpush
@endsection
