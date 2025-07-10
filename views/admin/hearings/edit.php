<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['forms', 'buttons', 'cards', 'status']);
?>

<style>
/* Enhanced Edit Hearing Styles */
.hearing-edit-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.hearing-edit-header {
    background: var(--cases-bg-primary);
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-border-radius);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hearing-edit-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--cases-text-primary);
    margin-bottom: 8px;
}

.hearing-edit-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: var(--cases-text-light);
    margin-bottom: 20px;
}

.hearing-edit-breadcrumb a {
    color: var(--cases-primary);
    text-decoration: none;
}

.hearing-edit-breadcrumb a:hover {
    text-decoration: underline;
}

.hearing-edit-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.context-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.context-card {
    background: var(--cases-bg-primary);
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-border-radius);
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.context-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--cases-border);
}

.context-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--cases-text-primary);
}

.context-card-icon {
    color: var(--cases-primary);
    font-size: 1.2rem;
}

.context-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.context-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.context-info-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--cases-text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.context-info-value {
    font-size: 0.95rem;
    color: var(--cases-text-primary);
    font-weight: 500;
}

.edit-form-container {
    background: var(--cases-bg-primary);
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-border-radius);
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.edit-form-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--cases-border);
}

.edit-form-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--cases-text-primary);
}

.current-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-scheduled {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #bbdefb;
}

.status-completed {
    background: #e8f5e8;
    color: #388e3c;
    border: 1px solid #c8e6c9;
}

.status-adjourned {
    background: #fff3e0;
    color: #f57c00;
    border: 1px solid #ffcc02;
}

.status-cancelled {
    background: #ffebee;
    color: #d32f2f;
    border: 1px solid #ffcdd2;
}

.form-section {
    margin-bottom: 25px;
}

.form-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--cases-text-primary);
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--cases-border-light);
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid var(--cases-border);
}

.quick-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.quick-action-btn {
    padding: 8px 16px;
    border: 1px solid var(--cases-border);
    background: var(--cases-bg-primary);
    color: var(--cases-text-primary);
    border-radius: var(--cases-border-radius);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.quick-action-btn:hover {
    background: var(--cases-bg-secondary);
    border-color: var(--cases-primary);
}

.quick-action-btn.completed {
    background: var(--cases-success);
    color: white;
    border-color: var(--cases-success);
}

.quick-action-btn.adjourned {
    background: var(--cases-warning);
    color: white;
    border-color: var(--cases-warning);
}

.hearing-history {
    margin-top: 30px;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-left: 3px solid var(--cases-border);
    margin-bottom: 15px;
    background: var(--cases-bg-secondary);
    border-radius: 0 var(--cases-border-radius) var(--cases-border-radius) 0;
}

.history-item.current {
    border-left-color: var(--cases-primary);
    background: var(--cases-primary-bg);
}

.history-date {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--cases-text-primary);
    min-width: 100px;
}

.history-status {
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.history-details {
    flex: 1;
    font-size: 0.9rem;
    color: var(--cases-text-light);
}

.history-purpose {
    font-weight: 500;
    color: var(--cases-text-primary);
    margin-bottom: 4px;
}

.history-description {
    color: var(--cases-text-light);
    margin-bottom: 4px;
}

.history-next {
    color: var(--cases-primary);
    font-size: 0.8rem;
    font-weight: 500;
}

.history-actions {
    display: flex;
    gap: 8px;
}

.current-indicator {
    font-size: 0.7rem;
    color: var(--cases-primary);
    font-weight: 600;
    background: var(--cases-primary-bg);
    padding: 2px 6px;
    border-radius: 8px;
    margin-left: 8px;
}

/* Documents Section */
.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-border-radius);
    background: var(--cases-bg-secondary);
    transition: all 0.2s ease;
}

.document-item:hover {
    border-color: var(--cases-primary);
    background: var(--cases-bg-primary);
}

.document-icon {
    font-size: 1.5rem;
    color: var(--cases-primary);
    width: 40px;
    text-align: center;
}

.document-details {
    flex: 1;
}

.document-name {
    font-weight: 500;
    color: var(--cases-text-primary);
    margin-bottom: 4px;
}

.document-meta {
    display: flex;
    gap: 10px;
    font-size: 0.8rem;
    color: var(--cases-text-light);
}

.document-type {
    background: var(--cases-bg-tertiary);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
}

.document-actions {
    display: flex;
    gap: 8px;
}

.cases-btn-sm {
    padding: 6px 12px;
    font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .context-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .context-info-grid {
        grid-template-columns: 1fr;
    }
    
    .hearing-edit-actions {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .quick-actions {
        flex-wrap: wrap;
    }
}
</style>

<div class="hearing-edit-container">
    <!-- Header Section -->
    <div class="hearing-edit-header">
        <div class="hearing-edit-breadcrumb">
            <a href="<?php echo admin_url('cases'); ?>">Cases</a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?php echo admin_url('cases/details?id=' . $case['id']); ?>"><?php echo htmlspecialchars($case['case_title']); ?></a>
            <i class="fas fa-chevron-right"></i>
            <span>Edit Hearing</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 class="hearing-edit-title">Edit Hearing</h1>
                <p style="color: var(--cases-text-light); margin: 0;">
                    <?php echo date('d M Y', strtotime($hearing['date'])); ?> at <?php echo date('h:i A', strtotime($hearing['time'])); ?>
                </p>
            </div>
            
            <div class="hearing-edit-actions">
                <a href="<?php echo admin_url('cases/details?id=' . $case['id']); ?>" class="cases-btn cases-btn-default">
                    <i class="fas fa-arrow-left"></i> Back to Case
                </a>
                <a href="<?php echo admin_url('cases/hearings'); ?>" class="cases-btn cases-btn-default">
                    <i class="fas fa-list"></i> All Hearings
                </a>
                <a href="<?php echo admin_url('cases/hearings/quick_update/' . $hearing['id']); ?>" class="cases-btn cases-btn-success">
                    <i class="fas fa-bolt"></i> Quick Update
                </a>
            </div>
        </div>
    </div>

    <!-- Context Cards -->
    <div class="context-grid">
        <!-- Case Information Card -->
        <div class="context-card">
            <div class="context-card-header">
                <i class="fas fa-briefcase context-card-icon"></i>
                <h3 class="context-card-title">Case Information</h3>
            </div>
            <div class="context-info-grid">
                <div class="context-info-item">
                    <div class="context-info-label">Case Title</div>
                    <div class="context-info-value"><?php echo htmlspecialchars($case['case_title']); ?></div>
                </div>
                <div class="context-info-item">
                    <div class="context-info-label">Case Number</div>
                    <div class="context-info-value"><?php echo htmlspecialchars($case['case_number']); ?></div>
                </div>
                <div class="context-info-item">
                    <div class="context-info-label">Client</div>
                    <div class="context-info-value"><?php echo htmlspecialchars($case['client_name'] ?? 'N/A'); ?></div>
                </div>
                <div class="context-info-item">
                    <div class="context-info-label">Court</div>
                    <div class="context-info-value"><?php echo htmlspecialchars($case['court_display'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <!-- Current Hearing Status Card -->
        <div class="context-card">
            <div class="context-card-header">
                <i class="fas fa-gavel context-card-icon"></i>
                <h3 class="context-card-title">Current Hearing Status</h3>
            </div>
            <div class="context-info-grid">
                <div class="context-info-item">
                    <div class="context-info-label">Status</div>
                    <div class="context-info-value">
                        <?php 
                        $status_class = 'status-scheduled';
                        switch(strtolower($hearing['status'])) {
                            case 'completed': $status_class = 'status-completed'; break;
                            case 'adjourned': $status_class = 'status-adjourned'; break;
                            case 'cancelled': $status_class = 'status-cancelled'; break;
                        }
                        ?>
                        <span class="current-status-badge <?php echo $status_class; ?>">
                            <i class="fas fa-circle" style="font-size: 0.6rem;"></i>
                            <?php echo htmlspecialchars($hearing['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="context-info-item">
                    <div class="context-info-label">Purpose</div>
                    <div class="context-info-value"><?php echo htmlspecialchars($hearing['hearing_purpose'] ?? 'Not specified'); ?></div>
                </div>
                <div class="context-info-item">
                    <div class="context-info-label">Date & Time</div>
                    <div class="context-info-value">
                        <?php echo date('d M Y', strtotime($hearing['date'])); ?><br>
                        <small><?php echo date('h:i A', strtotime($hearing['time'])); ?></small>
                    </div>
                </div>
                <div class="context-info-item">
                    <div class="context-info-label">Next Date</div>
                    <div class="context-info-value">
                        <?php echo !empty($hearing['next_date']) ? date('d M Y', strtotime($hearing['next_date'])) : 'Not scheduled'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="edit-form-container">
        <div class="edit-form-header">
            <h2 class="edit-form-title">Edit Hearing Details</h2>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button type="button" class="quick-action-btn" onclick="quickStatusUpdate('Scheduled')">
                <i class="fas fa-clock"></i> Mark as Scheduled
            </button>
            <button type="button" class="quick-action-btn completed" onclick="quickStatusUpdate('Completed')">
                <i class="fas fa-check"></i> Mark as Completed
            </button>
            <button type="button" class="quick-action-btn adjourned" onclick="quickStatusUpdate('Adjourned')">
                <i class="fas fa-calendar-times"></i> Mark as Adjourned
            </button>
        </div>

        <form id="editHearingForm" method="POST" action="<?php echo admin_url('cases/hearings/edit/' . $hearing['id']); ?>">
            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
            <input type="hidden" name="case_id" value="<?php echo $hearing['case_id']; ?>">
            
            <!-- Basic Information Section -->
            <div class="form-section">
                <h3 class="form-section-title">Basic Information</h3>
                
                <div class="cases-form-grid cases-form-grid-2">
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Hearing Date</label>
                        <input type="date" name="date" id="date" class="cases-form-control" 
                               value="<?php echo $hearing['date']; ?>" required>
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Hearing Time</label>
                        <input type="time" name="time" id="time" class="cases-form-control" 
                               value="<?php echo $hearing['time']; ?>" required>
                    </div>
                </div>

                <div class="cases-form-group">
                    <label class="cases-form-label">Purpose of Hearing</label>
                    <select name="hearing_purpose" id="hearing_purpose" class="cases-form-select">
                        <option value="">Select purpose or enter custom</option>
                        <?php 
                        $standard_purposes = hearing_get_standard_purposes();
                        foreach ($standard_purposes as $purpose_key => $purpose_label) {
                            $selected = ($hearing['hearing_purpose'] == $purpose_key) ? ' selected' : '';
                            echo '<option value="' . htmlspecialchars($purpose_key) . '"' . $selected . '>';
                            echo htmlspecialchars($purpose_label);
                            echo '</option>';
                        }
                        ?>
                    </select>
                    <input type="text" name="custom_purpose" id="custom_purpose" class="cases-form-control" 
                           style="margin-top: 10px; display: <?php echo (!array_key_exists($hearing['hearing_purpose'], $standard_purposes) && !empty($hearing['hearing_purpose'])) ? 'block' : 'none'; ?>;" 
                           placeholder="Enter custom purpose" 
                           value="<?php echo (!array_key_exists($hearing['hearing_purpose'], $standard_purposes) && !empty($hearing['hearing_purpose'])) ? htmlspecialchars($hearing['hearing_purpose']) : ''; ?>">
                    <div class="cases-form-help">
                        Select from standard purposes or choose custom to enter your own
                    </div>
                </div>
            </div>

            <!-- Status & Outcome Section -->
            <div class="form-section">
                <h3 class="form-section-title">Status & Outcome</h3>
                
                <div class="cases-form-grid cases-form-grid-2">
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Status</label>
                        <select name="status" id="status" class="cases-form-select" required>
                            <?php 
                            // Load hearing constants for status options
                            require_once(__DIR__ . '/../../../config/hearing_constants.php');
                            $status_definitions = hearing_get_status_definitions();
                            
                            foreach ($status_definitions as $status_key => $status_def) {
                                $selected = ($hearing['status'] == $status_key) ? ' selected' : '';
                                $disabled = '';
                                
                                // Check if this status transition is valid
                                if (function_exists('hearing_is_valid_transition') && 
                                    !hearing_is_valid_transition($hearing['status'], $status_key) && 
                                    $hearing['status'] != $status_key) {
                                    $disabled = ' disabled';
                                }
                                
                                echo '<option value="' . $status_key . '"' . $selected . $disabled . '>';
                                echo $status_def['label'];
                                if ($disabled) {
                                    echo ' (Not allowed)';
                                }
                                echo '</option>';
                            }
                            ?>
                        </select>
                        <div class="cases-form-help" id="status-help">
                            <?php 
                            $current_status_info = $status_definitions[$hearing['status']] ?? null;
                            if ($current_status_info) {
                                echo '<i class="fas ' . $current_status_info['icon'] . '"></i> ';
                                echo $current_status_info['description'];
                            }
                            ?>
                        </div>
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label">Next Hearing Date</label>
                        <input type="date" name="next_date" id="next_date" class="cases-form-control" 
                               value="<?php echo $hearing['next_date'] ?? ''; ?>">
                    </div>
                </div>

                <div class="cases-form-group">
                    <label class="cases-form-label">Notes / Outcome</label>
                    <textarea name="description" id="description" class="cases-form-control cases-textarea" rows="4" 
                              placeholder="Enter hearing outcome, proceedings, or notes..."><?php echo htmlspecialchars($hearing['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="cases-btn cases-btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete Hearing
                </button>
                <div style="margin-left: auto; display: flex; gap: 10px;">
                    <a href="<?php echo admin_url('cases/details?id=' . $case['id']); ?>" class="cases-btn cases-btn-default">
                        Cancel
                    </a>
                    <button type="submit" class="cases-btn cases-btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Documents Section -->
    <?php if (!empty($hearing_documents)): ?>
    <div class="edit-form-container" style="margin-top: 30px;">
        <div class="edit-form-header">
            <h2 class="edit-form-title">Hearing Documents</h2>
            <a href="<?php echo admin_url('cases/documents/upload'); ?>" class="cases-btn cases-btn-primary cases-btn-sm"
               onclick="localStorage.setItem('document_upload_data', JSON.stringify({
                 hearing_id: <?php echo $hearing['id']; ?>,
                 case_id: <?php echo $case['id']; ?>,
                 customer_id: <?php echo $case['client_id']; ?>,
                 doc_type: 'hearing'
               }));">
                <i class="fas fa-plus"></i> Add Document
            </a>
        </div>
        
        <div class="documents-grid">
            <?php foreach ($hearing_documents as $doc): ?>
            <div class="document-item">
                <div class="document-icon">
                    <i class="fas fa-file-<?php 
                        switch(strtolower($doc['filetype'])) {
                            case 'pdf': echo 'pdf'; break;
                            case 'doc':
                            case 'docx': echo 'word'; break;
                            case 'xls':
                            case 'xlsx': echo 'excel'; break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                            case 'gif': echo 'image'; break;
                            default: echo 'alt';
                        }
                    ?>"></i>
                </div>
                <div class="document-details">
                    <div class="document-name"><?php echo htmlspecialchars($doc['file_name']); ?></div>
                    <div class="document-meta">
                        <span class="document-type"><?php echo strtoupper($doc['filetype']); ?></span>
                        <span class="document-date"><?php echo date('d M Y', strtotime($doc['dateadded'])); ?></span>
                    </div>
                </div>
                <div class="document-actions">
                    <a href="<?php echo admin_url('cases/documents/download/' . $doc['id']); ?>" class="cases-btn cases-btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="<?php echo admin_url('cases/documents/view/' . $doc['id']); ?>" class="cases-btn cases-btn-sm" target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hearing History -->
    <?php if (!empty($hearing_history)): ?>
    <div class="edit-form-container" style="margin-top: 30px;">
        <div class="edit-form-header">
            <h2 class="edit-form-title">Hearing History for this Case</h2>
            <span class="cases-text-muted"><?php echo count($hearing_history); ?> total hearings</span>
        </div>
        
        <div class="hearing-history">
            <?php foreach ($hearing_history as $history): ?>
            <div class="history-item <?php echo ($history['id'] == $hearing['id']) ? 'current' : ''; ?>">
                <div class="history-date">
                    <?php echo date('d M Y', strtotime($history['date'])); ?>
                    <?php if ($history['id'] == $hearing['id']): ?>
                        <span class="current-indicator">CURRENT</span>
                    <?php endif; ?>
                </div>
                <div class="history-status <?php echo 'status-' . strtolower($history['status']); ?>">
                    <?php echo $history['status']; ?>
                </div>
                <div class="history-details">
                    <div class="history-purpose"><?php echo htmlspecialchars($history['hearing_purpose'] ?? 'No purpose specified'); ?></div>
                    <?php if (!empty($history['description'])): ?>
                    <div class="history-description"><?php echo htmlspecialchars($history['description']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($history['next_date'])): ?>
                    <div class="history-next">Next: <?php echo date('d M Y', strtotime($history['next_date'])); ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($history['id'] != $hearing['id']): ?>
                <div class="history-actions">
                    <a href="<?php echo admin_url('cases/hearings/edit/' . $history['id']); ?>" class="cases-btn cases-btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Purpose dropdown handler
    document.getElementById('hearing_purpose').addEventListener('change', function() {
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

    // Enhanced status change handler with business rules
    document.getElementById('status').addEventListener('change', function() {
        const nextDateField = document.getElementById('next_date');
        const statusHelp = document.getElementById('status-help');
        const selectedStatus = this.value;
        
        // Load status definitions (passed from PHP)
        const statusDefinitions = <?php echo json_encode($status_definitions); ?>;
        const currentStatusInfo = statusDefinitions[selectedStatus];
        
        // Update help text
        if (currentStatusInfo && statusHelp) {
            statusHelp.innerHTML = '<i class="fas ' + currentStatusInfo.icon + '"></i> ' + currentStatusInfo.description;
        }
        
        // Business rule: Completed hearings don't need next date
        if (selectedStatus === 'Completed') {
            nextDateField.value = '';
            nextDateField.disabled = true;
            nextDateField.style.backgroundColor = '#f8f9fa';
        }
        // Business rule: Adjourned/Postponed hearings require next date
        else if (selectedStatus === 'Adjourned' || selectedStatus === 'Postponed') {
            nextDateField.disabled = false;
            nextDateField.style.backgroundColor = '';
            nextDateField.required = true;
            if (!nextDateField.value) {
                nextDateField.style.borderColor = '#dc3545';
            }
        }
        // Other statuses
        else {
            nextDateField.disabled = false;
            nextDateField.style.backgroundColor = '';
            nextDateField.required = false;
            nextDateField.style.borderColor = '';
        }
    });

    // Form submission handler
    document.getElementById('editHearingForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
        
        // Re-enable after 3 seconds if something goes wrong
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});

// Quick status update function
function quickStatusUpdate(status) {
    const statusSelect = document.getElementById('status');
    statusSelect.value = status;
    statusSelect.dispatchEvent(new Event('change'));
    
    // Show visual feedback
    const quickBtns = document.querySelectorAll('.quick-action-btn');
    quickBtns.forEach(btn => btn.style.opacity = '0.5');
    
    setTimeout(() => {
        quickBtns.forEach(btn => btn.style.opacity = '1');
    }, 300);
}

// Confirm delete function
function confirmDelete() {
    if (confirm('Are you sure you want to delete this hearing? This action cannot be undone.')) {
        window.location.href = '<?php echo admin_url('cases/hearings/delete/' . $hearing['id']); ?>';
    }
}
</script>

<?php init_tail(); ?>