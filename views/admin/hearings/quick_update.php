<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['forms', 'buttons', 'cards', 'status']);
echo cases_page_wrapper_start(
    'Quick Update Hearing',
    'Update hearing status and schedule next hearing',
    [
        [
            'text' => '← Back to Cases',
            'href' => admin_url('cases'),
            'class' => 'cases-btn'
        ],
        [
            'text' => 'Back to Hearings',
            'href' => admin_url('cases/hearings'),
            'class' => 'cases-btn cases-btn-default'
        ]
    ]
);
?>
            
<!-- Case Information -->
<div class="cases-card cases-info-card cases-mb-lg">
    <div class="cases-grid cases-grid-2">
        <div class="cases-card-section">
            <div class="cases-card-title cases-text-primary">
                <i class="fas fa-briefcase"></i> Case Information
            </div>
            <div class="cases-card-meta-grid">
                <div class="cases-card-meta-item">
                    <span class="cases-card-meta-label">Case Title:</span>
                    <span class="cases-card-meta-value"><?php echo htmlspecialchars($case['case_title']); ?></span>
                </div>
                <div class="cases-card-meta-item">
                    <span class="cases-card-meta-label">Case Number:</span>
                    <span class="cases-card-meta-value"><?php echo htmlspecialchars($case['case_number']); ?></span>
                </div>
            </div>
        </div>
        <div class="cases-card-section">
            <div class="cases-card-title cases-text-info">
                <i class="fas fa-calendar-alt"></i> Current Hearing
            </div>
            <div class="cases-card-meta-grid">
                <div class="cases-card-meta-item">
                    <span class="cases-card-meta-label">Hearing Date:</span>
                    <span class="cases-card-meta-value"><?php echo date('d M Y', strtotime($hearing['date'])); ?></span>
                </div>
                <div class="cases-card-meta-item">
                    <span class="cases-card-meta-label">Current Status:</span>
                    <span class="cases-status-badge cases-status-<?php echo strtolower($hearing['status']); ?>">
                        <?php echo $hearing['status']; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
            
<!-- Quick Update Form -->
<?php echo cases_section_start('Quick Update Form'); ?>
<form method="POST" action="<?php echo admin_url('cases/hearings/quick_update/' . $hearing['id']); ?>" id="quick-update-form">
    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
    
    <!-- Current Hearing Status -->
    <div class="cases-card cases-mb-lg">
        <div class="cases-card-header">
            <h3 class="cases-card-title">
                <i class="fas fa-sync-alt"></i> Update Current Hearing Status
            </h3>
        </div>
        <div class="cases-card-body">
            <div class="cases-form-group">
                <label class="cases-form-label cases-label-required">Status</label>
                <select name="status" id="status" class="cases-form-select" required>
                    <?php 
                    $statuses = ['Scheduled', 'Adjourned', 'Completed', 'Cancelled'];
                    foreach ($statuses as $status) {
                        $selected = ($hearing['status'] == $status) ? 'selected' : '';
                        echo '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                    }
                    ?>
                </select>
                <div class="cases-form-help">Update the current status of this hearing</div>
            </div>
            
            <div class="cases-form-group">
                <label class="cases-form-label">Outcome/Notes</label>
                <textarea name="description" id="description" class="cases-form-control cases-textarea" rows="3" 
                    placeholder="Enter the outcome or notes about this hearing"><?php echo htmlspecialchars($hearing['description']); ?></textarea>
                <div class="cases-form-help">Document what happened during this hearing</div>
            </div>
        </div>
    </div>
                
    <!-- Next Hearing Details -->
    <div class="cases-card cases-mb-lg">
        <div class="cases-card-header">
            <h3 class="cases-card-title">
                <i class="fas fa-calendar-plus"></i> Schedule Next Hearing
            </h3>
        </div>
        <div class="cases-card-body">
            <div class="cases-form-grid cases-form-grid-2">
                <div class="cases-form-group">
                    <label class="cases-form-label">Next Date</label>
                    <input type="date" name="next_date" id="next_date" class="cases-form-control" 
                        value="<?php echo !empty($hearing['next_date']) ? $hearing['next_date'] : 
                                (!empty($upcoming_hearing['date']) ? $upcoming_hearing['date'] : ''); ?>">
                    <div class="cases-form-help">Select the date for the next hearing</div>
                </div>
                <div class="cases-form-group">
                    <label class="cases-form-label">Next Time</label>
                    <input type="time" name="next_time" id="next_time" class="cases-form-control" 
                        value="<?php echo !empty($upcoming_hearing['time']) ? $upcoming_hearing['time'] : '10:00'; ?>">
                    <div class="cases-form-help">Set the scheduled time</div>
                </div>
            </div>
            
            <div class="cases-form-group">
                <label class="cases-form-label">Purpose of Next Hearing</label>
                <select name="upcoming_purpose" id="upcoming_purpose" class="cases-form-select">
                    <option value="">Select purpose or enter custom</option>
                    <option value="Arguments" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Arguments') ? 'selected' : ''; ?>>Arguments</option>
                    <option value="Evidence Submission" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Evidence Submission') ? 'selected' : ''; ?>>Evidence Submission</option>
                    <option value="Witness Examination" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Witness Examination') ? 'selected' : ''; ?>>Witness Examination</option>
                    <option value="Cross Examination" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Cross Examination') ? 'selected' : ''; ?>>Cross Examination</option>
                    <option value="Final Arguments" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Final Arguments') ? 'selected' : ''; ?>>Final Arguments</option>
                    <option value="Judgment" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Judgment') ? 'selected' : ''; ?>>Judgment</option>
                    <option value="Interim Application" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Interim Application') ? 'selected' : ''; ?>>Interim Application</option>
                    <option value="Status Report" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Status Report') ? 'selected' : ''; ?>>Status Report</option>
                    <option value="Settlement Discussion" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Settlement Discussion') ? 'selected' : ''; ?>>Settlement Discussion</option>
                    <option value="Compliance Review" <?php echo (isset($upcoming_hearing['hearing_purpose']) && $upcoming_hearing['hearing_purpose'] == 'Compliance Review') ? 'selected' : ''; ?>>Compliance Review</option>
                </select>
                <input type="text" name="custom_purpose" id="custom_purpose" class="cases-form-control" 
                    style="margin-top: 10px; display: none;" placeholder="Enter custom purpose">
                <div class="cases-form-help">This will be shown on the cause list for the next date</div>
            </div>
            
            <div class="cases-form-group">
                <label class="cases-form-checkbox">
                    <input type="checkbox" id="send_notification" name="send_notification" value="1">
                    <span class="cases-form-checkbox-label">
                        <i class="fas fa-bell"></i> Send notification reminder 24 hours before the hearing
                    </span>
                </label>
            </div>
        </div>
    </div>
              
    <!-- Form Actions -->
    <div class="cases-form-actions">
        <button type="submit" class="cases-btn cases-btn-primary cases-btn-lg" id="submit-btn">
            <i class="fas fa-check"></i> Update and Schedule Next
        </button>
        <a href="<?php echo admin_url('cases'); ?>" class="cases-btn cases-btn-default cases-btn-lg">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>
    
    <!-- Help Text -->
    <div class="cases-info-box cases-mt-md">
        <div class="cases-info-box-content">
            <i class="fas fa-info-circle cases-text-info"></i>
            <strong>Note:</strong> This will update the current hearing status and create a new hearing entry if you specify a next date.
        </div>
    </div>
</form>
<?php echo cases_section_end(); ?>
<?php echo cases_page_wrapper_end(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Purpose dropdown handler
    document.getElementById('upcoming_purpose').addEventListener('change', function() {
        const customInput = document.getElementById('custom_purpose');
        if (this.value === '') {
            customInput.style.display = 'block';
            customInput.required = true;
        } else {
            customInput.style.display = 'none';
            customInput.required = false;
            customInput.value = '';
        }
    });
    
    // Date validation
    document.getElementById('next_date').addEventListener('change', function() {
        const currentDate = new Date('<?php echo $hearing['date']; ?>');
        const nextDate = new Date(this.value);
        
        if (nextDate <= currentDate) {
            alert('Next hearing date must be after the current hearing date');
            this.value = '';
        }
    });
    
    // Form validation
    document.getElementById('quick-update-form').addEventListener('submit', function(e) {
        const nextDate = document.getElementById('next_date').value;
        const nextTime = document.getElementById('next_time').value;
        
        // Basic validation
        if (nextDate && !nextTime) {
            e.preventDefault();
            alert('Please select a time for the next hearing');
            document.getElementById('next_time').focus();
            return false;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;
    });
    
    // Set default time if empty
    const timeInput = document.getElementById('next_time');
    if (!timeInput.value) {
        timeInput.value = '10:00';
    }
});
</script>

<?php init_tail(); ?>