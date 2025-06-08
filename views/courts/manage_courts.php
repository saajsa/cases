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

.action-btn.btn-success {
    background: #ffffff;
    border-color: #2d7d2d;
    color: #2d7d2d;
}

.action-btn.btn-success:hover {
    background: #2d7d2d;
    color: #ffffff;
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
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 15px 12px;
        font-size: 0.8rem;
    }
    
    .action-btn {
        padding: 8px 12px;
        margin-bottom: 8px;
        display: block;
        text-align: center;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Minimalist Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-8">
                    <h1>Manage Courts</h1>
                    <div class="subtitle">Configure court establishments and hierarchies</div>
                </div>
                <div class="col-md-4">
                    <div class="page-actions text-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#add_court_modal">
                            Add New Court
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php if (!empty($courts)): ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Court Name</th>
                            <th>Hierarchy</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($courts as $court): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($court['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($court['hierarchy']); ?></td>
                                <td><?php echo htmlspecialchars($court['location']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($court['status']) == 'active' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $court['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('cases/courts/edit_court/'.$court['id']); ?>" class="action-btn btn-primary">Edit</a>
                                    <a href="<?php echo admin_url('cases/courts/delete_court/'.$court['id']); ?>" class="action-btn btn-danger _delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-university"></i>
                    <h5>No Courts Found</h5>
                    <p>Start by adding your first court establishment</p>
                    <button class="action-btn btn-primary" data-toggle="modal" data-target="#add_court_modal">
                        Add Court
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Court Modal -->
<div class="modal fade" id="add_court_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Court</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php echo form_open(admin_url('cases/courts/add_court')); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Court Name <span style="color: #cc1a1a;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           placeholder="e.g., High Court of Delhi">
                </div>
                <div class="form-group">
                    <label class="form-label">Hierarchy</label>
                    <input type="text" id="hierarchy" name="hierarchy" class="form-control" 
                           placeholder="e.g., High Court, District Court, Sessions Court">
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           placeholder="e.g., New Delhi, Mumbai, Bangalore">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="action-btn btn-primary">Add Court</button>
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
            if (!confirm('Are you sure you want to delete this court? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation
    const form = document.querySelector('#add_court_modal form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameField = document.getElementById('name');
            const statusField = document.getElementById('status');
            
            if (!nameField.value.trim()) {
                alert('Court name is required');
                nameField.focus();
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