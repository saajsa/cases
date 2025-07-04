<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['forms', 'buttons', 'tables', 'status', 'modals']);
echo cases_page_wrapper_start(
    'Manage Court Rooms',
    'Configure court rooms, judges, and schedules',
    [
        [
            'text' => 'Add New Court Room',
            'class' => 'cases-btn cases-btn-primary',
            'data' => ['toggle' => 'modal', 'target' => '#add_room_modal']
        ]
    ]
);
?>

<!-- Main Content -->
<?php echo cases_section_start(''); ?>

<?php if (!empty($rooms)): ?>
    <div class="cases-table-wrapper">
        <table class="cases-table">
            <thead>
                <tr>
                    <th>Court</th>
                    <th>Court Number</th>
                    <th>Judge Name</th>
                    <th>Duration</th>
                    <th>Type & Bench</th>
                    <th>Status</th>
                    <th class="cases-table-actions-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rooms as $room): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--cases-primary);"><?php echo htmlspecialchars($room['court_name']); ?></div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--cases-primary);">Court <?php echo htmlspecialchars($room['court_no']); ?></div>
                        </td>
                        <td>
                            <div style="font-style: italic;">Hon'ble <?php echo htmlspecialchars($room['judge_name']); ?></div>
                        </td>
                        <td>
                            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                                <?php if ($room['from_date']): ?>
                                    <?php echo date('d M Y', strtotime($room['from_date'])); ?>
                                <?php endif; ?>
                                <?php if ($room['to_date']): ?>
                                    <br>to <?php echo date('d M Y', strtotime($room['to_date'])); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div>
                                <?php if ($room['type']): ?>
                                    <div><?php echo htmlspecialchars($room['type']); ?></div>
                                <?php endif; ?>
                                <?php if ($room['bench_type']): ?>
                                    <small style="color: var(--cases-text-light);"><?php echo htmlspecialchars($room['bench_type']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php echo cases_status_badge(
                                $room['status'], 
                                strtolower($room['status']) == 'active' ? 'active' : 'inactive'
                            ); ?>
                        </td>
                        <td>
                            <div class="cases-table-actions">
                                <?php echo cases_action_button('Edit', [
                                    'type' => 'primary',
                                    'href' => admin_url('cases/courts/edit_room/'.$room['id'])
                                ]); ?>
                                <?php echo cases_action_button('Delete', [
                                    'type' => 'danger',
                                    'href' => admin_url('cases/courts/delete_room/'.$room['id']),
                                    'onclick' => 'return confirm(\'Are you sure you want to delete this court room? This action cannot be undone.\');'
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
        'No Court Rooms Found',
        'Start by adding your first court room',
        [
            'icon' => 'fas fa-gavel',
            'action' => [
                'text' => 'Add Court Room',
                'class' => 'cases-btn cases-btn-primary',
                'data-toggle' => 'modal',
                'data-target' => '#add_room_modal'
            ]
        ]
    ); ?>
<?php endif; ?>

<?php echo cases_section_end(); ?>
<?php echo cases_page_wrapper_end(); ?>

<!-- Add Court Room Modal -->
<div class="modal fade" id="add_room_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="cases-modal-content">
            <div class="cases-modal-header">
                <h4 class="cases-modal-title">Add New Court Room</h4>
                <button type="button" class="cases-modal-close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php echo form_open(admin_url('cases/courts/add_room')); ?>
            <div class="cases-modal-body">
                <div class="cases-grid cases-grid-2">
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Court</label>
                        <select name="court_id" id="court_id" class="cases-form-control" required>
                            <option value="">Select Court</option>
                            <?php foreach($courts as $court): ?>
                            <option value="<?php echo $court['id']; ?>"><?php echo htmlspecialchars($court['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Court Number</label>
                        <input type="text" id="court_no" name="court_no" class="cases-form-control" required 
                               placeholder="e.g., 1, 2, 3">
                    </div>
                </div>
                
                <div class="cases-grid cases-grid-2">
                    <div class="cases-form-group">
                        <label class="cases-form-label">Judge Name</label>
                        <input type="text" id="judge_name" name="judge_name" class="cases-form-control" 
                               placeholder="e.g., Justice Smith">
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="cases-form-control">
                    </div>
                </div>
                
                <div class="cases-grid cases-grid-2">
                    <div class="cases-form-group">
                        <label class="cases-form-label">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="cases-form-control">
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label">Type</label>
                        <input type="text" id="type" name="type" class="cases-form-control" 
                               placeholder="e.g., Civil, Criminal, Commercial">
                    </div>
                </div>
                
                <div class="cases-grid cases-grid-2">
                    <div class="cases-form-group">
                        <label class="cases-form-label">Bench Type</label>
                        <input type="text" id="bench_type" name="bench_type" class="cases-form-control" 
                               placeholder="e.g., Single Bench, Division Bench">
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label">Status</label>
                        <select name="status" id="status" class="cases-form-control">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="cases-modal-footer">
                <?php echo cases_button('Cancel', [
                    'type' => 'default',
                    'data' => ['dismiss' => 'modal']
                ]); ?>
                <?php echo cases_button('Add Court Room', [
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
    const deleteButtons = document.querySelectorAll('a[href*="delete_room"]');
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