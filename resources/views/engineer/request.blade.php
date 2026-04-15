@extends('layouts.app')

@section('title', 'Request Management - PT Sumber Prima Mandiri')

@section('content')

{{-- VIEW: REQUEST LIST --}}
<div id="view-request-list" class="view-section active">
    <div class="mb-4">
        <h2 class="page-title">Request Management</h2>
        <p class="page-subtitle">View requests and add part listing</p>
    </div>
    <div class="content-card mt-0">
        <div class="d-flex justify-content-between mb-4">
            <div class="position-relative" style="width:300px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" class="form-control ps-5" placeholder="Search by ID or machine name...">
            </div>
            <select class="form-select" style="width:150px;">
                <option selected>All Status</option>
                <option>Waiting for Part</option>
                <option>Completed</option>
            </select>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Machine Name</th>
                        <th>Quantity</th>
                        <th>Due Date</th>
                        <th>Parts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-medium">REQ-2025-001</td>
                        <td>
                            <div class="fw-medium">Custom Gear Box Machine</div>
                            <small class="text-muted">Mesin gear box untuk packaging line</small>
                        </td>
                        <td>2 unit</td>
                        <td>15/2/2025</td>
                        <td>4 parts</td>
                        <td><span class="status-badge status-blue">Part Listing Completed</span></td>
                        <td><span class="btn-add-parts-link" onclick="navigate.toPartList('REQ-2025-001')">+ Add Parts</span></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">REQ-2025-002</td>
                        <td>
                            <div class="fw-medium">Conveyor Belt System</div>
                            <small class="text-muted">System conveyor dengan panjang 10 meter</small>
                        </td>
                        <td>1 unit</td>
                        <td>28/2/2025</td>
                        <td>0 parts</td>
                        <td><span class="status-badge status-yellow">Waiting for Part Listing</span></td>
                        <td><span class="btn-add-parts-link" onclick="navigate.toPartList('REQ-2025-002')">+ Add Parts</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- VIEW: PART LIST --}}
<div id="view-part-list" class="view-section">
    <div onclick="navigate.toRequestList()" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Request Management
    </div>
    <div class="mb-4">
        <h2 class="page-title">Create Part Listing</h2>
        <div class="text-muted">Request ID: <span class="fw-medium text-dark" id="pl-request-id"></span></div>
        <div class="text-muted">Machine: <span class="fw-medium text-dark" id="pl-machine-name"></span></div>
        <small class="text-muted">Define what will be produced - parts, materials, and dimensions</small>
    </div>
    <div class="content-card mt-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Part List (<span id="part-count">0</span> parts)</h5>
            <button class="btn btn-primary-custom" onclick="navigate.toAddPartForm()">
                <i class="bi bi-plus-lg me-2"></i>Add Part
            </button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle" id="parts-table">
                <thead>
                    <tr>
                        <th>Part Name</th>
                        <th>Material</th>
                        <th>Dimension Finish</th>
                        <th>Dimension Raw</th>
                        <th>Qty</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-success-custom px-4 py-2" onclick="submitToAdmin()">Submit to Admin</button>
        </div>
    </div>
</div>

{{-- VIEW: ADD PART --}}
<div id="view-add-part" class="view-section">
    <div onclick="navigate.toPartList()" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Part Listing
    </div>
    <div class="mb-4">
        <h2 class="page-title">Add New Part</h2>
        <div class="text-muted small">
            Request ID: <span class="fw-medium text-dark" id="ap-request-id"></span> •
            Machine: <span class="fw-medium text-dark" id="ap-machine-name"></span>
        </div>
    </div>
    <div class="alert alert-info-custom d-flex align-items-center gap-3">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            <div class="fw-medium">Design Team - Part Entry</div>
            <div class="small">Enter part specifications including material and dimensions.</div>
        </div>
    </div>
    <div class="content-card mt-0">
        <h5 class="fw-bold mb-4">Part Details</h5>
        <p class="text-muted small mb-4">Enter part specifications</p>
        <form id="addPartForm">
            <div class="mb-3">
                <label class="form-label">Part Name<span class="required-star">*</span></label>
                <input type="text" class="form-control" id="add-partName" required placeholder="e.g., Mesin CNC 2">
            </div>
            <div class="mb-3">
                <label class="form-label">Material<span class="required-star">*</span></label>
                <input type="text" class="form-control" id="add-material" required placeholder="e.g., BAJA">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Dimension Finish<span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="add-dimFinish" required placeholder="e.g., 200 x 10">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dimension Raw<span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="add-dimRaw" required placeholder="e.g., 210 x 290">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity<span class="required-star">*</span></label>
                <input type="number" class="form-control" id="add-quantity" required placeholder="e.g., 1">
            </div>
            <div class="mb-4">
                <label class="form-label">Notes (Optional)</label>
                <textarea class="form-control" id="add-notes" rows="3" placeholder="Additional notes..."></textarea>
            </div>
            <div class="d-flex justify-content-end gap-3">
                <button type="button" class="btn btn-outline-custom px-4" onclick="navigate.toPartList()">Cancel</button>
                <button type="submit" class="btn btn-primary-custom px-4">Add Part</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDIT PART --}}
<div class="modal fade" id="editPartModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-4">Edit Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="editPartForm">
                    <input type="hidden" id="edit-partId">
                    <div class="mb-3">
                        <label class="form-label">Part Name<span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="edit-partName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Material<span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="edit-material" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Dimension Finish<span class="required-star">*</span></label>
                            <input type="text" class="form-control" id="edit-dimFinish" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dimension Raw<span class="required-star">*</span></label>
                            <input type="text" class="form-control" id="edit-dimRaw" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity<span class="required-star">*</span></label>
                        <input type="number" class="form-control" id="edit-quantity" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="edit-notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-custom" onclick="saveEditedPart()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: VIEW PART --}}
<div class="modal fade" id="viewPartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Part Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="row g-4">
                    <div class="col-6">
                        <div class="text-muted small">Part Name</div>
                        <div class="fw-medium fs-5" id="view-partName"></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Material</div>
                        <div class="fw-medium fs-5" id="view-material"></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Dimension Finish</div>
                        <div class="fw-medium fs-5" id="view-dimFinish"></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Dimension Raw</div>
                        <div class="fw-medium fs-5" id="view-dimRaw"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Quantity</div>
                        <div class="fw-medium fs-5" id="view-quantity"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Notes</div>
                        <div class="fw-medium" id="view-notes"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-0 pb-3 pe-3">
                <button type="button" class="btn btn-outline-custom px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- TOAST --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="successToast" class="toast custom-toast align-items-center bg-white" role="alert" data-bs-delay="5000">
        <div class="d-flex p-2">
            <div class="toast-body d-flex align-items-start gap-3">
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                <div>
                    <h6 class="fw-bold mb-1">Submitted!</h6>
                    <p class="mb-0 text-muted small">Part listing submitted to Admin successfully!</p>
                </div>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const requestsData = {
        'REQ-2025-001': { machineName: 'Custom Gear Box Machine' },
        'REQ-2025-002': { machineName: 'Conveyor Belt System' }
    };

    let partsStorage = {
        'REQ-2025-001': [
            { id: 101, name: 'Gear Shaft', material: 'SCM 440', dimFinish: 'Ø50 x 200', dimRaw: 'Ø55 x 210', qty: 2, notes: 'Heat treatment required' },
            { id: 102, name: 'Bearing Housing', material: 'SS 304', dimFinish: '100 x 100 x 50', dimRaw: '110 x 110 x 55', qty: 4, notes: 'Precision machining' },
        ],
        'REQ-2025-002': []
    };

    let currentState = { requestId: null };

    const editModal    = new bootstrap.Modal(document.getElementById('editPartModal'));
    const viewModal    = new bootstrap.Modal(document.getElementById('viewPartModal'));
    const successToast = new bootstrap.Toast(document.getElementById('successToast'));

    const navigate = {
        toRequestList() {
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
            document.getElementById('view-request-list').classList.add('active');
            currentState.requestId = null;
        },
        toPartList(requestId) {
            if (requestId) currentState.requestId = requestId;
            const req = requestsData[currentState.requestId];
            document.getElementById('pl-request-id').textContent  = currentState.requestId;
            document.getElementById('pl-machine-name').textContent = req.machineName;
            renderPartsTable();
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
            document.getElementById('view-part-list').classList.add('active');
        },
        toAddPartForm() {
            const req = requestsData[currentState.requestId];
            document.getElementById('ap-request-id').textContent  = currentState.requestId;
            document.getElementById('ap-machine-name').textContent = req.machineName;
            document.getElementById('addPartForm').reset();
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
            document.getElementById('view-add-part').classList.add('active');
        }
    };

    function renderPartsTable() {
        const tbody = document.querySelector('#parts-table tbody');
        const parts = partsStorage[currentState.requestId] || [];
        document.getElementById('part-count').textContent = parts.length;
        if (!parts.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No parts added yet.</td></tr>';
            return;
        }
        tbody.innerHTML = parts.map(p => `
            <tr>
                <td class="fw-medium">${p.name}</td>
                <td>${p.material}</td>
                <td>${p.dimFinish}</td>
                <td>${p.dimRaw}</td>
                <td>${p.qty}</td>
                <td>${p.notes}</td>
                <td>
                    <button class="action-icon-btn edit-icon"   onclick="openEditModal(${p.id})"><i class="bi bi-pencil-fill"></i></button>
                    <button class="action-icon-btn view-icon"   onclick="openViewModal(${p.id})"><i class="bi bi-eye-fill"></i></button>
                    <button class="action-icon-btn delete-icon" onclick="deletePart(${p.id})"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>`).join('');
    }

    document.getElementById('addPartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        partsStorage[currentState.requestId].push({
            id: Date.now(),
            name:      document.getElementById('add-partName').value,
            material:  document.getElementById('add-material').value,
            dimFinish: document.getElementById('add-dimFinish').value,
            dimRaw:    document.getElementById('add-dimRaw').value,
            qty:       document.getElementById('add-quantity').value,
            notes:     document.getElementById('add-notes').value
        });
        navigate.toPartList();
    });

    function deletePart(partId) {
        if (confirm('Are you sure you want to delete this part?')) {
            partsStorage[currentState.requestId] = partsStorage[currentState.requestId].filter(p => p.id !== partId);
            renderPartsTable();
        }
    }

    function openViewModal(partId) {
        const p = partsStorage[currentState.requestId].find(p => p.id === partId);
        if (!p) return;
        document.getElementById('view-partName').textContent  = p.name;
        document.getElementById('view-material').textContent  = p.material;
        document.getElementById('view-dimFinish').textContent = p.dimFinish;
        document.getElementById('view-dimRaw').textContent    = p.dimRaw;
        document.getElementById('view-quantity').textContent  = p.qty;
        document.getElementById('view-notes').textContent     = p.notes || '-';
        viewModal.show();
    }

    function openEditModal(partId) {
        const p = partsStorage[currentState.requestId].find(p => p.id === partId);
        if (!p) return;
        document.getElementById('edit-partId').value    = p.id;
        document.getElementById('edit-partName').value  = p.name;
        document.getElementById('edit-material').value  = p.material;
        document.getElementById('edit-dimFinish').value = p.dimFinish;
        document.getElementById('edit-dimRaw').value    = p.dimRaw;
        document.getElementById('edit-quantity').value  = p.qty;
        document.getElementById('edit-notes').value     = p.notes;
        editModal.show();
    }

    function saveEditedPart() {
        const form = document.getElementById('editPartForm');
        if (!form.checkValidity()) { form.reportValidity(); return; }
        const id  = parseInt(document.getElementById('edit-partId').value);
        const idx = partsStorage[currentState.requestId].findIndex(p => p.id === id);
        if (idx !== -1) {
            partsStorage[currentState.requestId][idx] = {
                id,
                name:      document.getElementById('edit-partName').value,
                material:  document.getElementById('edit-material').value,
                dimFinish: document.getElementById('edit-dimFinish').value,
                dimRaw:    document.getElementById('edit-dimRaw').value,
                qty:       document.getElementById('edit-quantity').value,
                notes:     document.getElementById('edit-notes').value
            };
            editModal.hide();
            renderPartsTable();
        }
    }

    function submitToAdmin() { successToast.show(); }

    document.addEventListener('DOMContentLoaded', function() {
        const id = new URLSearchParams(window.location.search).get('id');
        id ? navigate.toPartList(id) : navigate.toRequestList();
    });
</script>
@endpush