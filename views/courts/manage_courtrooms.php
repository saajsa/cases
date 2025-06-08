<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Minimalist Black & White UI with Status Colors */
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

.modern-table tbody tr:last-child td {
    border-bottom: none;
}

.court-name {
    font-weight: 600;
    color: #1a1a1a;
}

.court-number {
    font-weight: 600;
    color: #1a1a1a;
}

.judge-name {
    color: #2c2c2c;
    font-style: italic;
}

.date-range {
    font-size: 0.8rem;
    color: #666666;
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

.action-btn.btn-primary:hover {
    background: #000000;
    border-color: #000000;
}

.action-btn.btn-danger {
    background: #ffffff;
    border-color: #cc1a1a;
    color: #cc1a1a;
}

.action-btn.btn-danger:hover {
    background: #cc1a1a;
    color: #ffffff;
}

.action-btn.btn-default {
    background: #ffffff;
    border-color: #d1d1d1;
    color: #2c2c2c;
}

.action-btn.btn-default:hover {
    background: #f8f8f8;
    border-color: #999999;
}

/* Status Colors */
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

.status-active { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-inactive { 
    background: #f5f5f5; 
    color: #666666; 
    border-color: #666666; 
}

.empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #999999;
    background: #fafafa;
    border: 1px dashed #d1d1d1;
    border-radius: 2px;
    margin: 30px;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 20px;
    opacity: 0.6;
    color: #cccccc;
}

.empty-state h5 {
    font-weight: 600;
    color: #666666;
    margin-bottom: 8px;
}

.empty-state p {
    color: #999999;
    margin-bottom: 20px;
}

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
    padding: 12px 16px;
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

.close {
    border: none;
    background: none;
    font-size: 1.5rem;
    color: #666666;
    cursor: pointer;
}

.close:hover {
    color: #1a1a1a;
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
    
    .modern-table {
        font-size: 0.75rem;
    }
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 12px 8px;
    }
    
    .action-btn {
        padding: 8px 10px;
        margin-bottom: 8px;
        display: block;
        text-align: center;
        font-size: 0.7rem;
    }
    
    /* Stack table on mobile */
    .modern-table thead {
        display: none;
    }
    
    .modern-table tbody tr {
        display: block;
        border: 1px solid #e1e1e1;
        margin-bottom: 15px;
        border-radius: 2px;
        padding: 15px;
    }
    
    .modern-table tbody td {
        display: block;
        padding: 8px 0;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .modern-table tbody td:before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #666666;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modern-table tbody td:last-child {
        border-bottom: none;
        padding-top: 15px;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Minimalist Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-8">
                    <h1>Manage Court Rooms</h1>
                    <div class="subtitle">Configure court rooms, judges, and schedules</div>
                </div>
                <div class="col-md-4">
                    <div class="page-actions text-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#add_room_modal">
                            Add New Court Room
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php if (!empty($rooms)): ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Court</th>
                            <th>Court Number</th>
                            <th>Judge Name</th>
                            <th>Duration</th>
                            <th>Type & Bench</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rooms as $room): ?>
                            <tr>
                                <td data-label="Court">
                                    <div class="court-name"><?php echo htmlspecialchars($room['court_name']); ?></div>
                                </td>
                                <td data-label="Court Number">
                                    <div class="court-number">Court <?php echo htmlspecialchars($room['court_no']); ?></div>
                                </td>
                                <td data-label="Judge">
                                    <div class="judge-name">Hon'ble <?php echo htmlspecialchars($room['judge_name']); ?></div>
                                </td>
                                <td data-label="Duration">
                                    <div class="date-range">
                                        <?php if ($room['from_date']): ?>
                                            <?php echo date('d M Y', strtotime($room['from_date'])); ?>
                                        <?php endif; ?>
                                        <?php if ($room['to_date']): ?>
                                            <br>to <?php echo date('d M Y', strtotime($room['to_date'])); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td data-label="Type">
                                    <div>
                                        <?php if ($room['type']): ?>
                                            <div><?php echo htmlspecialchars($room['type']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($room['bench_type']): ?>
                                            <small style="color: #666666;"><?php echo htmlspecialchars($room['bench_type']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge <?php echo strtolower($room['status']) == 'active' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $room['status']; ?>
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <a href="<?php echo admin_url('cases/courts/edit_room/'.$room['id']); ?>" class="action-btn btn-primary">Edit</a>
                                    <a href="<?php echo admin_url('cases/courts/delete_room/'.$room['id']); ?>" class="action-btn btn-danger _delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-gavel"></i>
                    <h5>No Court Rooms Found</h5>
                    <p>Start by adding your first court room</p>
                    <button class="action-btn btn-primary" data-toggle="modal" data-target="#add_room_modal">
                        Add Court Room
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Court Room Modal -->
<div class="modal fade" id="add_room_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Court Room</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php echo form_open(admin_url('cases/courts/add_room')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Court <span style="color: #cc1a1a;">*</span></label>
                            <select name="court_id" id="court_id" class="form-control" required>
                                <option value="">Select Court</option>
                                <?php foreach($courts as $court): ?>
                                <option value="<?php echo $court['id']; ?>"><?php echo htmlspecialchars($court['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Court Number <span style="color: #cc1a1a;">*</span></label>
                            <input type="text" id="court_no" name="court_no" class="form-control" required 
                                   placeholder="e.g., 1, 2, 3">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Judge Name</label>
                            <input type="text" id="judge_name" name="judge_name" class="form-control" 
                                   placeholder="e.g., Justice Smith">
                        </div>
                        <div class="form-group">
                            <label class="form-label">From Date</label>
                            <input type="date" id="from_date" name="from_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <input type="text" id="type" name="type" class="form-control" 
                                   placeholder="e.g., Civil, Criminal, Commercial">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bench Type</label>
                            <input type="text" id="bench_type" name="bench_type" class="form-control" 
                                   placeholder="e.g., Single Bench, Division Bench">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="action-btn btn-primary">Add Court Room</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
// Clean JavaScript without jQuery dependencies
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('._delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this court room? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation
    const form = document.querySelector('#add_room_modal form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const courtField = document.getElementById('court_id');
            const courtNoField = document.getElementById('court_no');
            const statusField = document.getElementById('status');
            
            if (!courtField.value) {
                alert('Court selection is required');
                courtField.focus();
                e.preventDefault();
                return;
            }
            
            if (!courtNoField.value.trim()) {
                alert('Court number is required');
                courtNoField.focus();
                e.preventDefault();
                return;
            }
            
            if (!statusField.value) {
                alert('Status is required');
                statusField.focus();
                e.preventDefault();
                return;
            }
        });
    }
    
    // Date validation
    const fromDateField = document.getElementById('from_date');
    const toDateField = document.getElementById('to_date');
    
    if (fromDateField && toDateField) {
        toDateField.addEventListener('change', function() {
            if (fromDateField.value && toDateField.value) {
                if (new Date(toDateField.value) < new Date(fromDateField.value)) {
                    alert('To date cannot be earlier than from date');
                    toDateField.value = '';
                }
            }
        });
    }
    
    // Table row hover effects
    const tableRows = document.querySelectorAll('.modern-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#fafafa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>

<?php init_tail(); ?>