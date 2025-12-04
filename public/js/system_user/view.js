// System Users - JS Script
// Handles: Fetch, Search, Modals, Deactivate/Reactivate, Table Rendering

// ========== VARIABLE DECLARATIONS ==========
let currentUserId = null;
let systemUsers = [];
let filteredUsers = [];
let deleteModalInstance = null;
let reactivateModalInstance = null;
let currentFilter = 'all';
let selectedUsers = new Set();
let searchDebounceTimer = null;

// ========== MAIN FUNCTIONS ==========

/* ----------------------- Fetch Users ----------------------- */
function loadSystemUsers() {
    console.log("Loading system users...");
    showLoading();
    hideError();

    fetch('/api/system-users', {
        method: 'GET',
        headers: { 
            'Accept': 'application/json', 
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        console.log('Response Status:', response.status);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data);
        systemUsers = Array.isArray(data) ? data : (data.data || []);
        filteredUsers = [...systemUsers];
        renderUsersTable(filteredUsers);
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showError('Failed to load system users: ' + error.message);
        showEmpty(true);
    })
    .finally(() => hideLoading());
}

/* ----------------------- Render Table ----------------------- */
function renderUsersTable(users) {
    const tbody = document.getElementById('usersTableBody');
    const tableContainer = document.getElementById('usersTableContainer');
    const actionBar = document.getElementById('actionBar');
    const emptyState = document.getElementById('emptyState');

    tbody.innerHTML = '';
    selectedUsers.clear();
    document.getElementById('selectAll').checked = false;
    updateBulkActionState();

    if (!users || users.length === 0) {
        tableContainer.classList.add('d-none');
        actionBar.classList.add('d-none');
        emptyState.classList.remove('d-none');
        return;
    }

    tableContainer.classList.remove('d-none');
    actionBar.classList.remove('d-none');
    emptyState.classList.add('d-none');

    // Update statistics
    updateStatistics(systemUsers);

    users.forEach(user => {
        const tr = document.createElement('tr');
        const userType = user.user?.user_type?.type || 'Unknown';
        const isActive = !!(user.is_active && user.user?.is_active);
        const avatarText = (user.fname?.charAt(0).toUpperCase() || '') + (user.lname?.charAt(0).toUpperCase() || 'U');
        const statusClass = isActive ? 'user-status-active' : 'user-status-inactive';

        tr.className = statusClass;
        
        tr.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input user-checkbox" data-user-id="${user.id}">
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 text-white fw-bold shadow">
                        ${avatarText}
                    </div>
                    <div>
                        <div class="fw-semibold text-dark">${escapeHtml((user.fname || '') + ' ' + (user.lname || ''))}</div>
                        <small class="text-muted">${escapeHtml(user.custom_id || 'N/A')}</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="text-dark">${escapeHtml(user.email || 'N/A')}</div>
                <small class="text-muted">${escapeHtml(user.mobile || 'N/A')}</small>
            </td>
            <td>
                <span class="badge ${getUserTypeBadgeClass(userType)} shadow-sm">${escapeHtml(userType)}</span>
            </td>
            <td>
                <span class="badge ${isActive ? 'bg-success' : 'bg-danger'} shadow-sm">
                    <i class="fas ${isActive ? 'fa-check-circle' : 'fa-times-circle'} me-1"></i>
                    ${isActive ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>
                <small class="text-muted">${formatDate(user.updated_at)}</small>
            </td>
            <td>
                <div class="action-buttons d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewUserDetails(${user.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editUser(${user.id})" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm ${isActive ? 'btn-outline-danger' : 'btn-outline-success'}" 
                            onclick="toggleUserStatus(${user.id}, ${isActive})" 
                            title="${isActive ? 'Deactivate User' : 'Reactivate User'}">
                        <i class="fas ${isActive ? 'fa-user-slash' : 'fa-user-check'}"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Add event listeners for checkboxes
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const userId = this.dataset.userId;
            if (this.checked) {
                selectedUsers.add(userId);
            } else {
                selectedUsers.delete(userId);
                document.getElementById('selectAll').checked = false;
            }
            updateBulkActionState();
        });
    });

    initializeTooltips();
}

/* ----------------------- Search Functions ----------------------- */
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function () {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => applySearch(this.value), 300);
    });
}

function applySearch(term) {
    term = (term || '').toString().trim().toLowerCase();
    filteredUsers = term ? systemUsers.filter(user =>
        (user.fname || '').toLowerCase().includes(term) ||
        (user.lname || '').toLowerCase().includes(term) ||
        (user.email || '').toLowerCase().includes(term) ||
        (user.user?.email || '').toLowerCase().includes(term) ||
        (user.custom_id || '').toLowerCase().includes(term) ||
        (user.mobile || '').toString().includes(term) ||
        (user.nic || '').toLowerCase().includes(term) ||
        (user.user?.name || '').toLowerCase().includes(term)
    ) : [...systemUsers];
    renderUsersTable(filteredUsers);
}

/* ----------------------- User Actions ----------------------- */
function viewUserDetails(userId) {
    fetch(`/api/system-users/${userId}`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch user details');
            return response.json();
        })
        .then(data => {
            const user = data.data;
            const modalContent = document.getElementById('userDetailsContent');
            const isActive = !!(user.is_active && user.user?.is_active);
            
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto text-white fw-bold mb-2" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            ${(user.fname?.charAt(0).toUpperCase() || '') + (user.lname?.charAt(0).toUpperCase() || 'U')}
                        </div>
                        <h5 class="mb-1">${escapeHtml(user.fname || '')} ${escapeHtml(user.lname || '')}</h5>
                        <span class="badge ${isActive ? 'bg-success' : 'bg-danger'}">${isActive ? 'Active' : 'Inactive'}</span>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-6 mb-2"><strong>Custom ID:</strong> ${escapeHtml(user.custom_id || 'N/A')}</div>
                            <div class="col-6 mb-2"><strong>User Type:</strong> <span class="badge ${getUserTypeBadgeClass(user.user?.user_type?.type)}">${escapeHtml(user.user?.user_type?.type || 'Unknown')}</span></div>
                            <div class="col-6 mb-2"><strong>Email:</strong> ${escapeHtml(user.email || 'N/A')}</div>
                            <div class="col-6 mb-2"><strong>Mobile:</strong> ${escapeHtml(user.mobile || 'N/A')}</div>
                            <div class="col-6 mb-2"><strong>NIC:</strong> ${escapeHtml(user.nic || 'N/A')}</div>
                            <div class="col-6 mb-2"><strong>Birthday:</strong> ${formatDate(user.bday)}</div>
                            <div class="col-6 mb-2"><strong>Gender:</strong> ${escapeHtml(user.gender || 'N/A')}</div>
                            <div class="col-12 mb-2"><strong>Address:</strong> ${escapeHtml([user.address1, user.address2, user.address3].filter(Boolean).join(', ') || 'N/A')}</div>
                            <div class="col-6 mb-2"><strong>Created:</strong> ${formatDate(user.created_at)}</div>
                            <div class="col-6 mb-2"><strong>Updated:</strong> ${formatDate(user.updated_at)}</div>
                        </div>
                    </div>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching user details:', error);
            showError('Failed to load user details');
        });
}

function editUser(userId) {
    window.location.href = `/system-users/${encodeURIComponent(userId)}/edit`;
}

function toggleUserStatus(userId, isActive) {
    currentUserId = userId;
    if (isActive) {
        deleteModalInstance.show();
    } else {
        reactivateModalInstance.show();
    }
}

function deactivateUser(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(`/api/system-users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to deactivate user');
        return response.json();
    })
    .then(data => {
        deleteModalInstance.hide();
        showSuccess('User deactivated successfully');
        loadSystemUsers(); // Reload the table
    })
    .catch(error => {
        console.error('Error deactivating user:', error);
        showError('Failed to deactivate user');
    });
}

function reactivateUser(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(`/api/system-users/${userId}/reactivate`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to reactivate user');
        return response.json();
    })
    .then(data => {
        reactivateModalInstance.hide();
        showSuccess('User reactivated successfully');
        loadSystemUsers(); // Reload the table
    })
    .catch(error => {
        console.error('Error reactivating user:', error);
        showError('Failed to reactivate user');
    });
}

// ========== UTILITY FUNCTIONS ==========
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getUserTypeBadgeClass(userType) {
    if (!userType) return 'bg-secondary';
    switch (userType.toLowerCase()) {
        case 'admin': return 'bg-danger';
        case 'user': return 'bg-primary';
        case 'teacher': return 'bg-info';
        case 'student': return 'bg-success';
        default: return 'bg-secondary';
    }
}

function showLoading() { 
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.remove('d-none'); 
}

function hideLoading() { 
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.add('d-none'); 
}

function showError(msg) {
    const err = document.getElementById('errorMessage');
    if (err) {
        document.getElementById('errorText').textContent = msg;
        err.classList.remove('d-none');
    }
}

function hideError() { 
    const err = document.getElementById('errorMessage');
    if (err) err.classList.add('d-none'); 
}

function showEmpty(show = true) {
    const emptyState = document.getElementById('emptyState');
    if (emptyState) emptyState.classList.toggle('d-none', !show);
}

function formatDate(dateString) {
    try {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (e) {
        return dateString || 'N/A';
    }
}

function showSuccess(message) {
    // You can use Toast or Alert here
    alert(message); // Replace with your preferred notification system
}

function exportTo(format) {
    showSuccess(`Export to ${format.toUpperCase()} feature would be implemented here`);
}

function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.forEach(el => { 
        try { 
            if (el._tooltip) el._tooltip.dispose(); 
        } catch { } 
        new bootstrap.Tooltip(el); 
    });
}

// ========== ENHANCED FEATURES FUNCTIONS ==========

/* ----------------------- Statistics Functions ----------------------- */
function updateStatistics(users) {
    const totalUsers = users.length;
    const activeUsers = users.filter(user => user.is_active && user.user?.is_active).length;
    const inactiveUsers = totalUsers - activeUsers;
    const adminUsers = users.filter(user => user.user?.user_type?.type?.toLowerCase() === 'admin').length;

    document.getElementById('totalUsers').textContent = totalUsers;
    document.getElementById('activeUsers').textContent = activeUsers;
    document.getElementById('inactiveUsers').textContent = inactiveUsers;
    document.getElementById('adminUsers').textContent = adminUsers;
    
    // Update user count text
    document.getElementById('userCount').textContent = `Showing ${filteredUsers.length} of ${totalUsers} users`;
}

/* ----------------------- Filter Functions ----------------------- */
function initializeFilters() {
    // Filter dropdown options
    document.querySelectorAll('.filter-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            currentFilter = this.dataset.filter;
            applyFilter(currentFilter);
            
            // Update dropdown button text
            const dropdownBtn = document.querySelector('.dropdown-toggle');
            const filterText = this.textContent;
            dropdownBtn.innerHTML = `<i class="fas fa-filter me-2"></i>${filterText}`;
        });
    });

    // Bulk actions
    document.getElementById('bulkActions').addEventListener('change', function() {
        const action = this.value;
        if (action && selectedUsers.size > 0) {
            handleBulkAction(action);
        }
        this.value = '';
    });

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            const userId = checkbox.dataset.userId;
            if (this.checked) {
                selectedUsers.add(userId);
            } else {
                selectedUsers.delete(userId);
            }
        });
        updateBulkActionState();
    });

    // Refresh button
    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadSystemUsers();
    });
}

function applyFilter(filter) {
    currentFilter = filter;
    
    switch(filter) {
        case 'active':
            filteredUsers = systemUsers.filter(user => user.is_active && user.user?.is_active);
            break;
        case 'inactive':
            filteredUsers = systemUsers.filter(user => !user.is_active || !user.user?.is_active);
            break;
        case 'admin':
            filteredUsers = systemUsers.filter(user => 
                user.user?.user_type?.type?.toLowerCase() === 'admin'
            );
            break;
        case 'user':
            filteredUsers = systemUsers.filter(user => 
                user.user?.user_type?.type?.toLowerCase() !== 'admin'
            );
            break;
        default:
            filteredUsers = [...systemUsers];
    }
    
    renderUsersTable(filteredUsers);
}

/* ----------------------- Bulk Actions ----------------------- */
function handleBulkAction(action) {
    const userIds = Array.from(selectedUsers);
    
    if (action === 'activate') {
        if (confirm(`Are you sure you want to activate ${userIds.length} users?`)) {
            activateMultipleUsers(userIds);
        }
    } else if (action === 'deactivate') {
        if (confirm(`Are you sure you want to deactivate ${userIds.length} users?`)) {
            deactivateMultipleUsers(userIds);
        }
    }
}

function activateMultipleUsers(userIds) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const promises = userIds.map(userId => 
        fetch(`/api/system-users/${userId}/reactivate`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
    );

    Promise.all(promises)
        .then(() => {
            showSuccess(`${userIds.length} users activated successfully`);
            selectedUsers.clear();
            document.getElementById('selectAll').checked = false;
            loadSystemUsers();
        })
        .catch(error => {
            console.error('Error activating users:', error);
            showError('Failed to activate users');
        });
}

function deactivateMultipleUsers(userIds) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const promises = userIds.map(userId => 
        fetch(`/api/system-users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
    );

    Promise.all(promises)
        .then(() => {
            showSuccess(`${userIds.length} users deactivated successfully`);
            selectedUsers.clear();
            document.getElementById('selectAll').checked = false;
            loadSystemUsers();
        })
        .catch(error => {
            console.error('Error deactivating users:', error);
            showError('Failed to deactivate users');
        });
}

function updateBulkActionState() {
    const bulkSelect = document.getElementById('bulkActions');
    bulkSelect.disabled = selectedUsers.size === 0;
}

// ========== EVENT LISTENER (අන්තිමට) ==========
document.addEventListener('DOMContentLoaded', function () {
    console.log("System Users - DOM Content Loaded");
    
    // Initialize modals
    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    reactivateModalInstance = new bootstrap.Modal(document.getElementById('reactivateModal'));

    // Event listeners
    document.getElementById('clearSearchBtn').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        applySearch('');
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (currentUserId) deactivateUser(currentUserId);
    });

    document.getElementById('confirmReactivateBtn').addEventListener('click', function () {
        if (currentUserId) reactivateUser(currentUserId);
    });

    // Initialize enhanced features
    initializeFilters();
    
    // Load users
    loadSystemUsers();
    initializeSearch();
});