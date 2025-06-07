<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Consistent with your existing Cases module styling */
* {
    box-sizing: border-box;
}

body {
    background: #fafafa;
    color: #2c2c2c;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.page-header {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 40px;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.page-header h1 {
    margin: 0 0 8px 0;
    font-weight: 600;
    font-size: 2.2rem;
    color: #1a1a1a;
    letter-spacing: -0.02em;
}

.page-header .subtitle {
    font-size: 1rem;
    color: #666666;
    font-weight: 400;
    margin-bottom: 25px;
}

.page-actions .btn {
    margin-right: 12px;
    margin-bottom: 8px;
    border-radius: 1px;
    padding: 10px 20px;
    font-weight: 500;
    font-size: 0.875rem;
    border: 1px solid #d1d1d1;
    background: #ffffff;
    color: #2c2c2c;
    transition: all 0.15s ease;
    text-decoration: none;
}

.page-actions .btn:hover {
    background: #f8f8f8;
    border-color: #999999;
    color: #1a1a1a;
}

.page-actions .btn-primary {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.page-actions .btn-primary:hover {
    background: #000000;
    border-color: #000000;
}

.main-content {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    overflow: hidden;
}

.content-header {
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
    padding: 25px 30px;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: -0.01em;
}

.filters-section {
    padding: 25px 30px;
    border-bottom: 1px solid #e1e1e1;
    background: #f8f8f8;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.filter-control {
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.filter-control:focus {
    outline: none;
    border-color: #1a1a1a;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.table-section {
    padding: 30px;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.modern-table thead th {
    background: #f8f8f8;
    color: #1a1a1a;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 20px;
    border-bottom: 1px solid #e1e1e1;
    border-right: 1px solid #f0f0f0;
    text-align: left;
}

.modern-table thead th:last-child {
    border-right: none;
}

.modern-table tbody td {
    padding: 20px;
    border-bottom: 1px solid #f5f5f5;
    border-right: 1px solid #f8f8f8;
    vertical-align: middle;
    color: #2c2c2c;
    font-size: 0.875rem;
}

.modern-table tbody td:last-child {
    border-right: none;
}

.modern-table tbody tr:hover {
    background: #fafafa;
}

.action-btn {
    border-radius: 1px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-right: 6px;
    margin-bottom: 4px;
    transition: all 0.15s ease;
    text-decoration: none;
    border: 1px solid;
    display: inline-block;
}

.action-btn.btn-primary {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.action-btn.btn-success {
    background: #ffffff;
    border-color: #2d7d2d;
    color: #2d7d2d;
}

.action-btn.btn-danger {
    background: #ffffff;
    border-color: #cc1a1a;
    color: #cc1a1a;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 1px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid;
}

.status-pending { background: #fff8e6; color: #cc8c1a; border-color: #cc8c1a; }
.status-confirmed { background: #eff8ff; color: #1a6bcc; border-color: #1a6bcc; }
.status-completed { background: #f0f9f0; color: #2d7d2d; border-color: #2d7d2d; }
.status-cancelled { background: #fff0f0; color: #cc1a1a; border-color: #cc1a1a; }
.status-no-show { background: #f5f5f5; color: #666666; border-color: #666666; }

/* Modal Styles */
.modal-content {
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.modal-header {
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
    padding: 20px 30px;
}

.modal-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    background: #f8f8f8;
    border-top: 1px solid #e1e1e1;
    padding: 20px 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 2px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.form-control:focus {
    outline: none;
    border-color: #1a1a1a;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(250, 250, 250, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e1e1e1;
    border-top: 3px solid #1a1a1a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .page-header {
        padding: 25px 20px;
    }
    
    .page-header h1 {
        font-size: 1.8rem;
    }
    
    .page-actions .btn {
        width: 100%;
        margin-bottom: 8px;
        margin-right: 0;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .table-section {
        padding: 20px;
        overflow-x: auto;
    }
    
    .modern-table {
        min-width: 800px;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-8">
                    <h1>Appointment Management</h1>
                    <div class="subtitle">Schedule and manage client appointments</div>
                </div>
                <div class="col-md-4">
                    <div class="page-actions text-right">
                        <a href="<?php echo admin_url('cases/calendar'); ?>" class="btn">
                            Calendar View
                        </a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#appointmentModal">
                            Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Content Header -->
            <div class="content-header">
                <h3 class="section-title">All Appointments</h3>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Date From</label>
                        <input type="date" id="date_from" class="filter-control">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Date To</label>
                        <input type="date" id="date_to" class="filter-control">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Staff</label>
                        <select id="staff_filter" class="filter-control">
                            <option value="">All Staff</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?php echo $member['staffid']; ?>">
                                    <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select id="status_filter" class="filter-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary" id="apply-filters">Apply Filters</button>
                    <button class="btn" id="clear-filters">Clear</button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
                <table class="modern-table" id="appointments-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Book Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="appointmentForm">
                <div class="modal-header">
                    <h4 class="modal-title">Book New Appointment</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Client <span style="color: #cc1a1a;">*</span></label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['userid']; ?>">
                                        <?php echo htmlspecialchars($client['company'] ?: 'Individual Client'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" id="contact-group" style="display: none;">
                            <label class="form-label">Contact Person</label>
                            <select name="contact_id" id="contact_id" class="form-control">
                                <option value="">Select Contact</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Service <span style="color: #cc1a1a;">*</span></label>
                            <select name="service_id" id="service_id" class="form-control" required>
                                <option value="">Select Service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['id']; ?>" 
                                            data-price="<?php echo $service['price']; ?>"
                                            data-duration="<?php echo $service['duration_minutes']; ?>">
                                        <?php echo htmlspecialchars($service['name']); ?> 
                                        (<?php echo $service['duration_minutes']; ?> min - ₹<?php echo number_format($service['price'], 2); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Staff <span style="color: #cc1a1a;">*</span></label>
                            <select name="staff_id" id="staff_id" class="form-control" required>
                                <option value="">Select Staff</option>
                                <?php foreach ($staff as $member): ?>
                                    <option value="<?php echo $member['staffid']; ?>">
                                        <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Date <span style="color: #cc1a1a;">*</span></label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Time <span style="color: #cc1a1a;">*</span></label>
                            <select name="start_time" id="start_time" class="form-control" required>
                                <option value="">Select Date First</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Any special requirements or notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="action-btn btn-primary">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Appointment Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let appointmentsTable;
    const adminUrl = '<?php echo admin_url(); ?>'.replace(/\/$/, '') + '/';
    let csrfToken = '<?php echo $this->security->get_csrf_token_name(); ?>';
    let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    // Cache for contacts
    const contactsCache = new Map();
    
    // Initialize
    initializeDataTable();
    setupEventListeners();
    
    // Centralized API handler
    const API = {
        post: function(url, data) {
            if (!(data instanceof FormData)) {
                const formData = new FormData();
                for (let key in data) {
                    formData.append(key, data[key]);
                }
                data = formData;
            }
            data.append(csrfToken, csrfHash);
            
            return fetch(adminUrl + url, {
                method: 'POST',
                body: data
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).then(data => {
                // Update CSRF hash if provided
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }
                return data;
            });
        },
        
        get: function(url, params) {
            const queryString = params ? '?' + new URLSearchParams(params).toString() : '';
            return fetch(adminUrl + url + queryString)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update CSRF hash if provided
                    if (data.csrf_hash) {
                        csrfHash = data.csrf_hash;
                    }
                    return data;
                });
        }
    };
    
    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function showLoading() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.id = 'loading-overlay';
        overlay.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(overlay);
    }
    
    function hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ${escapeHtml(message)}
        `;
        
        const content = document.querySelector('.content');
        content.insertBefore(alertDiv, content.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    function validateAppointmentDate(dateInput) {
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showAlert('warning', 'Cannot select a date in the past');
            dateInput.value = today.toISOString().split('T')[0];
            return false;
        }
        return true;
    }
    
    // Initialize DataTable with proper server-side configuration
function initializeDataTable() {
    appointmentsTable = $('#appointments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: adminUrl + 'cases/appointments_list',
            type: 'GET',
            data: function(d) {
                // Add custom filters to the request
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
                d.staff_id = $('#staff_filter').val();
                d.status = $('#status_filter').val();
                
                return d;
            },
            error: function(xhr, error, code) {
                console.error('AJAX Error:', {xhr: xhr, error: error, code: code});
                
                let errorMessage = 'Failed to load appointments.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    if (xhr.status === 401) {
                        errorMessage = 'Session expired. Please refresh the page.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Access denied.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred.';
                    }
                }
                
                showAlert('danger', errorMessage);
            },
            dataSrc: function(json) {
                // Update CSRF token if provided
                if (json.csrf_hash) {
                    csrfHash = json.csrf_hash;
                }
                
                // Check for errors
                if (json.error) {
                    showAlert('danger', 'Error: ' + json.error);
                    return [];
                }
                
                // Return the data array
                return json.data || [];
            }
        },
        columns: [
            {
                data: null,
                name: 'appointment_date',
                render: function(data, type, row) {
                    if (!row.formatted_date || !row.formatted_time) {
                        return 'Invalid Date';
                    }
                    return '<div><strong>' + escapeHtml(row.formatted_date) + '</strong><br>' +
                           '<small>' + escapeHtml(row.formatted_time) + '</small></div>';
                }
            },
            {
                data: 'client_name',
                name: 'client_name',
                render: function(data) {
                    return escapeHtml(data || 'Unknown Client');
                }
            },
            {
                data: 'service_name',
                name: 'service_name',
                render: function(data, type, row) {
                    let service = escapeHtml(data || 'Unknown Service');
                    if (row.duration_minutes) {
                        service += '<br><small>' + escapeHtml(row.duration_minutes) + ' minutes</small>';
                    }
                    return service;
                }
            },
            {
                data: 'staff_full_name',
                name: 'staff_full_name',
                render: function(data) {
                    return escapeHtml(data || 'Unknown Staff');
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    const status = data || 'unknown';
                    const safeStatus = escapeHtml(status);
                    return '<span class="status-badge status-' + safeStatus + '">' + 
                           safeStatus.charAt(0).toUpperCase() + safeStatus.slice(1) + '</span>';
                }
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                render: function(data) {
                    const amount = parseFloat(data || 0);
                    return '₹' + amount.toFixed(2);
                }
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                render: function(data) {
                    const status = data || 'unpaid';
                    const statusMap = {
                        'unpaid': 'Unpaid',
                        'partial': 'Partial',
                        'paid': 'Paid',
                        'refunded': 'Refunded',
                        'cancelled': 'Cancelled'
                    };
                    const safeStatus = escapeHtml(status);
                    return '<span class="status-badge status-' + safeStatus + '">' + 
                           escapeHtml(statusMap[status] || 'Unknown') + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = '<div class="btn-group btn-group-sm" role="group">';
                    
                    if (row.status === 'pending' || row.status === 'confirmed') {
                        actions += '<button class="btn btn-success" onclick="appointmentManager.complete(' + row.id + ')" title="Complete"><i class="fa fa-check"></i></button>';
                        actions += '<button class="btn btn-primary" onclick="appointmentManager.edit(' + row.id + ')" title="Edit"><i class="fa fa-edit"></i></button>';
                        actions += '<button class="btn btn-danger" onclick="appointmentManager.cancel(' + row.id + ')" title="Cancel"><i class="fa fa-times"></i></button>';
                    } else if (row.status === 'completed') {
                        actions += '<button class="btn btn-info" onclick="appointmentManager.view(' + row.id + ')" title="View"><i class="fa fa-eye"></i></button>';
                        if (!row.consultation_id) {
                            actions += '<button class="btn btn-success" onclick="appointmentManager.convert(' + row.id + ')" title="Convert to Consultation"><i class="fa fa-exchange"></i></button>';
                        }
                    } else {
                        actions += '<button class="btn btn-info" onclick="appointmentManager.view(' + row.id + ')" title="View"><i class="fa fa-eye"></i></button>';
                    }
                    
                    actions += '</div>';
                    return actions;
                }
            }
        ],
        order: [[0, 'desc']], // Order by date column
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        responsive: true,
        language: {
            emptyTable: "No appointments found",
            loadingRecords: "Loading appointments...",
            processing: '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading appointments...</div>',
            zeroRecords: "No appointments match your search criteria",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            },
            info: "Showing _START_ to _END_ of _TOTAL_ appointments",
            infoEmpty: "Showing 0 to 0 of 0 appointments",
            infoFiltered: "(filtered from _MAX_ total appointments)",
            search: "Search:",
            lengthMenu: "Show _MENU_ appointments"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        drawCallback: function(settings) {
            const api = this.api();
            const info = api.page.info();
            console.log('DataTable drawn:', {
                page: info.page + 1,
                pages: info.pages,
                recordsTotal: info.recordsTotal,
                recordsDisplay: info.recordsDisplay
            });
        }
    });
}
    
    // Setup event listeners
    function setupEventListeners() {
        // Debounced reload for filters
        const debouncedReload = debounce(() => appointmentsTable.ajax.reload(), 300);
        
        // Filter inputs with debouncing
        $('#date_from, #date_to, #staff_filter, #status_filter').on('change', debouncedReload);
        
        // Filter buttons
        $('#apply-filters').on('click', function() {
            appointmentsTable.ajax.reload();
        });
        
        $('#clear-filters').on('click', function() {
            $('#date_from').val('');
            $('#date_to').val('');
            $('#staff_filter').val('').trigger('change');
            $('#status_filter').val('').trigger('change');
            appointmentsTable.ajax.reload();
        });
        
        // Client change handler
        $('#client_id').on('change', function() {
            const clientId = $(this).val();
            if (clientId) {
                loadContactsByClient(clientId);
            } else {
                $('#contact-group').hide();
                $('#contact_id').html('<option value="">Select Contact</option>');
            }
        });
        
        // Service/Staff/Date change handlers for time slots
        $('#service_id, #staff_id, #appointment_date').on('change', function() {
            if (this.id === 'appointment_date') {
                if (!validateAppointmentDate(this)) return;
            }
            loadAvailableTimeSlots();
        });
        
        // Form submission
        $('#appointmentForm').on('submit', function(e) {
            e.preventDefault();
            createAppointment();
        });
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#appointment_date').attr('min', today);
        
        // Initialize select2 if available
        if ($.fn.select2) {
            $('#client_id, #service_id, #staff_id').select2({
                placeholder: "Select an option",
                allowClear: true
            });
        }
    }
    
    // Load contacts by client
    function loadContactsByClient(clientId) {
        // Check cache first
        if (contactsCache.has(clientId)) {
            populateContactsDropdown(contactsCache.get(clientId));
            return;
        }
        
        // Show loading state
        $('#contact_id').html('<option value="">Loading contacts...</option>');
        
        API.get('cases/get_contacts_by_client/' + clientId)
            .then(data => {
                if (data.success && data.data) {
                    contactsCache.set(clientId, data.data);
                    populateContactsDropdown(data.data);
                } else {
                    showEmptyContactsDropdown();
                }
            })
            .catch(error => {
                console.error('Error loading contacts:', error);
                showEmptyContactsDropdown('Error loading contacts');
            });
    }
    
    function populateContactsDropdown(contacts) {
        const select = $('#contact_id');
        select.html('<option value="">Select Contact</option>');
        
        if (contacts && contacts.length > 0) {
            contacts.forEach(contact => {
                const contactName = contact.full_name || (contact.firstname + ' ' + contact.lastname).trim();
                select.append(`<option value="${contact.id}">${escapeHtml(contactName)}</option>`);
            });
            $('#contact-group').show();
        } else {
            showEmptyContactsDropdown('No contacts available');
        }
        
        // Reinitialize select2 if available
        if ($.fn.select2) {
            select.select2({
                placeholder: "Select Contact",
                allowClear: true
            });
        }
    }
    
    function showEmptyContactsDropdown(message = 'No contacts available') {
        $('#contact_id').html(`<option value="">${escapeHtml(message)}</option>`);
        $('#contact-group').show();
    }
    
    // Load available time slots
    function loadAvailableTimeSlots() {
        const serviceId = $('#service_id').val();
        const staffId = $('#staff_id').val();
        const date = $('#appointment_date').val();
        
        const timeSelect = $('#start_time');
        
        if (!serviceId || !staffId || !date) {
            timeSelect.html('<option value="">Select service, staff and date first</option>');
            return;
        }
        
        timeSelect.html('<option value="">Loading time slots...</option>');
        
        API.get('cases/get_available_slots', {
            service_id: serviceId,
            staff_id: staffId,
            date: date
        })
        .then(data => {
            timeSelect.html('<option value="">Select Time</option>');
            
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(slot => {
                    const availability = slot.available ? '' : ' (Unavailable)';
                    const disabled = slot.available ? '' : 'disabled';
                    timeSelect.append(`<option value="${slot.start_time}" ${disabled}>${escapeHtml(slot.formatted_time)}${availability}</option>`);
                });
            } else {
                timeSelect.html('<option value="">No slots available</option>');
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            timeSelect.html('<option value="">Error loading slots</option>');
        });
    }
    
    // Create appointment
    function createAppointment() {
        const form = $('#appointmentForm');
        
        // Validate form
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        const formData = new FormData(form[0]);
        
        showLoading();
        
        API.post('cases/create_appointment', formData)
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    $('#appointmentModal').modal('hide');
                    form[0].reset();
                    appointmentsTable.ajax.reload();
                    showAlert('success', 'Appointment created successfully');
                    
                    if (data.invoice_id) {
                        showAlert('info', 'Invoice generated automatically for this appointment');
                    }
                    
                    // Clear contacts cache for this client
                    const clientId = formData.get('client_id');
                    if (clientId) {
                        contactsCache.delete(clientId);
                    }
                } else {
                    showAlert('danger', data.message || 'Failed to create appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred. Please try again.');
            });
    }
    
    // Appointment Manager object for global functions
    window.appointmentManager = {
        complete: function(appointmentId) {
            if (!confirm('Mark this appointment as completed?')) return;
            
            showLoading();
            
            API.post('cases/complete_appointment/' + appointmentId, {
                create_consultation: '1'
            })
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    appointmentsTable.ajax.reload();
                    showAlert('success', 'Appointment completed successfully');
                    
                    if (data.consultation_id) {
                        showAlert('info', 'Consultation record created automatically');
                    }
                } else {
                    showAlert('danger', data.message || 'Failed to complete appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred');
            });
        },
        
        cancel: function(appointmentId) {
            const reason = prompt('Please enter cancellation reason (optional):');
            if (reason === null) return;
            
            showLoading();
            
            API.post('cases/cancel_appointment/' + appointmentId, {
                reason: reason
            })
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    appointmentsTable.ajax.reload();
                    showAlert('success', 'Appointment cancelled successfully');
                } else {
                    showAlert('danger', data.message || 'Failed to cancel appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred');
            });
        },
        
        edit: function(appointmentId) {
            showLoading();
            
            API.get('cases/get_appointment/' + appointmentId)
                .then(data => {
                    hideLoading();
                    
                    if (data.success && data.data) {
                        populateEditForm(data.data);
                        $('#editAppointmentModal').modal('show');
                    } else {
                        showAlert('danger', data.message || 'Failed to load appointment details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to load appointment details');
                });
        },
        
        view: function(appointmentId) {
            showLoading();
            
            API.get('cases/get_appointment/' + appointmentId)
                .then(data => {
                    hideLoading();
                    
                    if (data.success && data.data) {
                        displayAppointmentDetails(data.data);
                        $('#viewAppointmentModal').modal('show');
                    } else {
                        showAlert('danger', data.message || 'Failed to load appointment details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to load appointment details');
                });
        },
        
        convert: function(appointmentId) {
            if (!confirm('Convert this appointment to a consultation record?')) return;
            
            showLoading();
            
            API.post('cases/convert_to_consultation/' + appointmentId, {})
                .then(data => {
                    hideLoading();
                    
                    if (data.success) {
                        appointmentsTable.ajax.reload();
                        showAlert('success', 'Appointment converted to consultation successfully');
                        
                        if (data.consultation_id) {
                            showAlert('info', `Consultation ID: ${data.consultation_id}`);
                        }
                    } else {
                        showAlert('danger', data.message || 'Failed to convert appointment');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showAlert('danger', 'Network error occurred');
                });
        }
    };
    
    // Populate edit form
    function populateEditForm(appointment) {
        $('#edit_appointment_id').val(appointment.id);
        $('#edit_client_id').val(appointment.client_id).trigger('change');
        $('#edit_service_id').val(appointment.service_id).trigger('change');
        $('#edit_staff_id').val(appointment.staff_id).trigger('change');
        $('#edit_appointment_date').val(appointment.appointment_date);
        $('#edit_start_time').val(appointment.start_time);
        $('#edit_notes').val(appointment.notes);
        
        // Load contacts for the client
        if (appointment.client_id) {
            loadContactsByClient(appointment.client_id);
            setTimeout(() => {
                $('#edit_contact_id').val(appointment.contact_id).trigger('change');
            }, 500);
        }
    }
    
    // Display appointment details
    function displayAppointmentDetails(appointment) {
        const detailsHtml = `
            <div class="appointment-details">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Client:</strong> ${escapeHtml(appointment.client_name)}</p>
                        <p><strong>Contact:</strong> ${escapeHtml(appointment.contact_name || 'N/A')}</p>
                        <p><strong>Service:</strong> ${escapeHtml(appointment.service_name)}</p>
                        <p><strong>Staff:</strong> ${escapeHtml(appointment.staff_full_name)}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date:</strong> ${escapeHtml(appointment.formatted_date)}</p>
                        <p><strong>Time:</strong> ${escapeHtml(appointment.formatted_time)}</p>
                        <p><strong>Duration:</strong> ${escapeHtml(appointment.duration_minutes)} minutes</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${escapeHtml(appointment.status)}">${escapeHtml(appointment.status)}</span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Amount:</strong> ₹${parseFloat(appointment.total_amount || 0).toFixed(2)}</p>
                        <p><strong>Payment Status:</strong> <span class="status-badge status-${escapeHtml(appointment.payment_status)}">${escapeHtml(appointment.payment_status)}</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Created:</strong> ${escapeHtml(appointment.created_at)}</p>
                        <p><strong>Updated:</strong> ${escapeHtml(appointment.updated_at)}</p>
                    </div>
                </div>
                ${appointment.notes ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Notes:</strong></p>
                        <p>${escapeHtml(appointment.notes)}</p>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        
        $('#appointment-details-content').html(detailsHtml);
    }
    
    // Maintain backward compatibility
    window.completeAppointment = appointmentManager.complete;
    window.cancelAppointment = appointmentManager.cancel;
    window.editAppointment = appointmentManager.edit;
    window.viewAppointment = appointmentManager.view;
    window.convertToConsultation = appointmentManager.convert;
});
</script>

<?php init_tail(); ?>