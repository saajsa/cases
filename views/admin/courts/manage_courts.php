<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['forms', 'buttons', 'tables', 'status', 'modals']);
echo cases_page_wrapper_start(
    'Manage Courts',
    'Configure court establishments and hierarchies',
    [
        [
            'text' => 'Add New Court',
            'class' => 'cases-btn cases-btn-primary',
            'data' => ['toggle' => 'modal', 'target' => '#add_court_modal']
        ]
    ]
);
?>

<!-- Main Content -->
<?php echo cases_section_start(''); ?>

<?php if (!empty($courts)): ?>
    <div class="cases-table-wrapper">
        <table class="cases-table">
            <thead>
                <tr>
                    <th>Court Name</th>
                    <th>Hierarchy</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th class="cases-table-actions-col">Actions</th>
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
                            <?php echo cases_status_badge(
                                $court['status'], 
                                strtolower($court['status']) == 'active' ? 'active' : 'inactive'
                            ); ?>
                        </td>
                        <td>
                            <div class="cases-table-actions">
                                <?php echo cases_action_button('Edit', [
                                    'type' => 'primary',
                                    'href' => admin_url('cases/courts/edit_court/'.$court['id'])
                                ]); ?>
                                <?php echo cases_action_button('Delete', [
                                    'type' => 'danger',
                                    'href' => admin_url('cases/courts/delete_court/'.$court['id']),
                                    'onclick' => 'return confirm(\'Are you sure you want to delete this court? This action cannot be undone.\');'
                                ]); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <?php echo cases_empty_state(
        'No Courts Found',
        'Start by adding your first court establishment',
        [
            'icon' => 'fas fa-university',
            'action' => [
                'text' => 'Add Court',
                'class' => 'cases-btn cases-btn-primary',
                'data-toggle' => 'modal',
                'data-target' => '#add_court_modal'
            ]
        ]
    ); ?>
<?php endif; ?>

<?php echo cases_section_end(); ?>
<?php echo cases_page_wrapper_end(); ?>

<!-- Add Court Modal -->
<div class="modal fade" id="add_court_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="cases-modal-content">
            <div class="cases-modal-header">
                <h4 class="cases-modal-title">Add New Court</h4>
                <button type="button" class="cases-modal-close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php echo form_open(admin_url('cases/courts/add_court')); ?>
            <div class="cases-modal-body">
                <div class="cases-form-group">
                    <label class="cases-form-label cases-label-required">Court Name</label>
                    <input type="text" id="name" name="name" class="cases-form-control" required 
                           placeholder="e.g., High Court of Delhi">
                </div>
                <div class="cases-form-group">
                    <label class="cases-form-label">Hierarchy</label>
                    <input type="text" id="hierarchy" name="hierarchy" class="cases-form-control" 
                           placeholder="e.g., High Court, District Court, Sessions Court">
                </div>
                <div class="cases-form-group">
                    <label class="cases-form-label">Location</label>
                    <input type="text" id="location" name="location" class="cases-form-control" 
                           placeholder="e.g., New Delhi, Mumbai, Bangalore">
                </div>
                <div class="cases-form-group">
                    <label class="cases-form-label">Status</label>
                    <select name="status" id="status" class="cases-form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="cases-modal-footer">
                <?php echo cases_button('Cancel', [
                    'type' => 'default',
                    'data' => ['dismiss' => 'modal']
                ]); ?>
                <?php echo cases_button('Add Court', [
                    'type' => 'primary',
                    'button_type' => 'submit'
                ]); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
// Clean JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('a[href*="delete_court"]');
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
    const tableRows = document.querySelectorAll('.cases-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'var(--cases-bg-hover)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>

<?php init_tail(); ?>