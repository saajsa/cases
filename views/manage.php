<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'forms', 'status', 'modals']);
echo cases_page_wrapper_start(
    'Cases Management',
    'Manage consultations and track litigation cases',
    [
        [
            'text' => 'Add Consultation',
            'class' => 'cases-btn cases-btn-primary',
            'data' => ['toggle' => 'modal', 'target' => '#consultationModal']
        ],
        [
            'text' => 'Dashboard',
            'href' => admin_url('cases/caseboard'),
            'class' => 'cases-btn'
        ]
    ]
);
?>

<style>
/* Enhanced Loading States */
.cases-loading-container {
    position: relative;
    min-height: 400px;
    background: var(--cases-bg-primary);
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-border-radius);
    overflow: hidden;
}

.cases-loading-state {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--cases-bg-primary);
    z-index: 10;
    pointer-events: none; /* Allow clicks through when visible */
}

.cases-loading-container:not(.loading) .cases-loading-state {
    display: none; /* Completely hide when not active */
}

.cases-loading-spinner {
    position: relative;
    width: 48px;
    height: 48px;
    margin-bottom: 20px;
}

.cases-loading-spinner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 3px solid var(--cases-border);
    border-top-color: var(--cases-primary);
    border-radius: 50%;
    animation: cases-spin 1s linear infinite;
}

.cases-loading-spinner::after {
    content: '';
    position: absolute;
    top: 6px;
    left: 6px;
    width: 36px;
    height: 36px;
    border: 2px solid transparent;
    border-top-color: var(--cases-primary);
    border-radius: 50%;
    animation: cases-spin 0.6s linear infinite reverse;
}

@keyframes cases-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.cases-loading-text {
    font-size: var(--cases-font-size-base);
    color: var(--cases-text-light);
    font-weight: 500;
    margin: 0;
    animation: cases-pulse 2s ease-in-out infinite;
}

@keyframes cases-pulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}

/* Skeleton Loading Animation */
.cases-skeleton {
    background: linear-gradient(90deg, 
        var(--cases-bg-tertiary) 25%, 
        var(--cases-bg-secondary) 50%, 
        var(--cases-bg-tertiary) 75%
    );
    background-size: 200% 100%;
    animation: cases-skeleton-loading 2s infinite;
}

@keyframes cases-skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.cases-skeleton-card {
    background: var(--cases-bg-primary);
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-border-radius);
    padding: var(--cases-spacing-lg);
    margin-bottom: var(--cases-spacing-md);
}

.cases-skeleton-line {
    height: 16px;
    border-radius: 4px;
    margin-bottom: 12px;
}

.cases-skeleton-line.short {
    width: 60%;
}

.cases-skeleton-line.medium {
    width: 80%;
}

.cases-skeleton-line.long {
    width: 95%;
}

.cases-skeleton-line.title {
    height: 20px;
    width: 70%;
    margin-bottom: 16px;
}

/* Error State Styling */
.cases-error-state {
    text-align: center;
    padding: var(--cases-spacing-xl);
    color: var(--cases-danger);
    background: var(--cases-danger-bg);
    border: 1px solid var(--cases-danger-border);
    border-radius: var(--cases-border-radius);
}

.cases-error-icon {
    font-size: 2.5rem;
    margin-bottom: var(--cases-spacing-md);
    opacity: 0.7;
}

.cases-error-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: var(--cases-spacing-sm);
}

.cases-error-message {
    color: var(--cases-text-light);
    margin-bottom: var(--cases-spacing-md);
}

/* Enhanced Empty State */
.cases-empty-state {
    text-align: center;
    padding: var(--cases-spacing-xl);
    background: var(--cases-bg-primary);
    border: 2px dashed var(--cases-border);
    border-radius: var(--cases-border-radius);
    transition: var(--cases-transition);
}

.cases-empty-state:hover {
    border-color: var(--cases-primary);
    background: var(--cases-bg-secondary);
}

.cases-empty-icon {
    font-size: 3rem;
    color: var(--cases-text-muted);
    margin-bottom: var(--cases-spacing-md);
    opacity: 0.6;
}

.cases-empty-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--cases-text-light);
    margin-bottom: var(--cases-spacing-sm);
}

.cases-empty-description {
    color: var(--cases-text-muted);
    margin-bottom: var(--cases-spacing-md);
}

/* Content Transition */
.cases-content-transition {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.cases-content-transition.loaded {
    opacity: 1;
    transform: translateY(0);
}

/* Tab loading indicator */
.cases-tab-loading {
    position: relative;
    overflow: hidden;
}

.cases-tab-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--cases-primary), transparent);
    animation: cases-tab-progress 2s infinite;
}

@keyframes cases-tab-progress {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .cases-loading-container {
        min-height: 300px;
    }
    
    .cases-loading-spinner {
        width: 36px;
        height: 36px;
    }
    
    .cases-loading-spinner::after {
        top: 4px;
        left: 4px;
        width: 28px;
        height: 28px;
    }
    
    .cases-empty-icon {
        font-size: 2.5rem;
    }
}
</style>

<!-- Stats Grid -->
<div class="cases-grid cases-grid-4 cases-mb-lg">
    <div class="cases-card cases-text-center">
        <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); margin: 0 0 8px 0;" id="consultations-count">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Consultations</div>
    </div>
    <div class="cases-card cases-text-center">
        <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); margin: 0 0 8px 0;" id="cases-count">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Active Cases</div>
    </div>
    <div class="cases-card cases-text-center">
        <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); margin: 0 0 8px 0;" id="litigation-count">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Litigations</div>
    </div>
    <div class="cases-card cases-text-center">
        <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); margin: 0 0 8px 0;" id="upcoming-count">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Upcoming</div>
    </div>
</div>

<!-- Main Content -->
<?php echo cases_section_start(''); ?>
<!-- Tab Navigation -->
<div style="background: var(--cases-bg-tertiary); border-bottom: 1px solid var(--cases-border); padding: 0; margin: -30px -30px 30px -30px; display: flex;">
    <button class="cases-btn active" data-tab="consultations" style="background: none; border: none; padding: 20px 30px; font-size: 0.875rem; font-weight: 500; color: var(--cases-text-light); cursor: pointer; transition: var(--cases-transition); border-bottom: 2px solid transparent; border-radius: 0;">
        Consultations <span id="consultations-badge" class="cases-count-badge">0</span>
    </button>
    <button class="cases-btn" data-tab="cases" style="background: none; border: none; padding: 20px 30px; font-size: 0.875rem; font-weight: 500; color: var(--cases-text-light); cursor: pointer; transition: var(--cases-transition); border-bottom: 2px solid transparent; border-radius: 0;">
        Cases <span id="cases-badge" class="cases-count-badge">0</span>
    </button>
</div>

<!-- Consultations Tab -->
<div class="tab-content" id="consultations-tab">
    <!-- Search and Filters -->
    <div class="cases-flex cases-flex-between cases-mb-md cases-flex-wrap" style="gap: 15px; align-items: end;">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" class="cases-form-control" id="consultations-search" 
                   placeholder="Search consultations by client, contact, or tag...">
        </div>
        <select class="cases-form-control" id="consultations-filter" style="min-width: 150px;">
            <option value="">All Status</option>
            <option value="consultation">Consultation</option>
            <option value="litigation">Litigation</option>
        </select>
        <?php echo cases_button('Refresh', [
            'type' => 'default',
            'id' => 'refresh-consultations',
            'icon' => 'fas fa-sync-alt'
        ]); ?>
    </div>

    <!-- Consultations Container -->
    <div id="consultations-container" class="cases-loading-container">
        <div class="cases-loading-state">
            <div class="cases-loading-spinner"></div>
            <p class="cases-loading-text">Loading consultations...</p>
        </div>
    </div>
</div>

<!-- Cases Tab -->
<div class="tab-content" id="cases-tab" style="display: none;">
    <!-- Search and Filters -->
    <div class="cases-flex cases-flex-between cases-mb-md cases-flex-wrap" style="gap: 15px; align-items: end;">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" class="cases-form-control" id="cases-search" 
                   placeholder="Search cases by title, number, client, or court...">
        </div>
        <select class="cases-form-control" id="cases-filter" style="min-width: 150px;">
            <option value="">All Courts</option>
        </select>
        <?php echo cases_button('Refresh', [
            'type' => 'default',
            'id' => 'refresh-cases',
            'icon' => 'fas fa-sync-alt'
        ]); ?>
    </div>

    <!-- Cases Container -->
    <div id="cases-container" class="cases-loading-container">
        <div class="cases-loading-state">
            <div class="cases-loading-spinner"></div>
            <p class="cases-loading-text">Loading cases...</p>
        </div>
    </div>
</div>

<?php echo cases_section_end(); ?>

<?php echo cases_page_wrapper_end(); ?>

<!-- Consultation Modal (updated to include date & time) -->
<div class="modal fade" id="consultationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="cases-modal-content">
            <form id="consultationForm">
                <div class="cases-modal-header">
                    <h4 class="cases-modal-title" id="modal-title-text">Add Consultation</h4>
                    <button type="button" class="cases-modal-close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="cases-modal-body">
                    <input type="hidden" name="consultation_id" id="consultation_id">
                    
                    <div class="cases-grid cases-grid-2">
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Client</label>
                            <select name="client_id" id="client_id" class="cases-form-control" required>
                                <option value="">Select Client</option>
                                <?php if (isset($clients) && !empty($clients)): ?>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo htmlspecialchars($client['userid']); ?>">
                                            <?php echo htmlspecialchars($client['company'] ?: 'Individual Client'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="cases-invalid-feedback"></div>
                        </div>

                        <div class="cases-form-group" id="contact-group" style="display: none;">
                            <label class="cases-form-label cases-label-optional">Contact Person</label>
                            <select name="contact_id" id="contact_id" class="cases-form-control">
                                <option value="">Select Contact (Optional)</option>
                            </select>
                        </div>
                    </div>

                    <div class="cases-grid cases-grid-2">
                        <div class="cases-form-group">
                            <label class="cases-form-label">Tag</label>
                            <input type="text" name="tag" id="consultation_tag" class="cases-form-control" 
                                   placeholder="e.g., Family Law, Property, Criminal">
                        </div>
                        
                        <div class="cases-form-group">
                            <label class="cases-form-label">
                                Invoice 
                                <small style="color: var(--cases-warning);">(Recommended)</small>
                            </label>
                            <select name="invoice_id" id="invoice_id" class="cases-form-control">
                                <option value="">Select Invoice</option>
                            </select>
                            <div class="cases-invalid-feedback"></div>
                            <div class="cases-form-help">
                                If no invoices are available, you can create one after saving.
                            </div>
                        </div>
                    </div>

                    <div class="cases-grid cases-grid-2">
                        <?php
                        // Prepare current date/time defaults
                        $current_date = date('Y-m-d');
                        $current_time = date('H:i');
                        ?>
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Date</label>
                            <input type="date" 
                                   name="date" 
                                   id="consultation_date" 
                                   class="cases-form-control" 
                                   required
                                   value="<?php echo isset($consultation) && !empty($consultation->date) ? $consultation->date : $current_date; ?>"
                                   placeholder="<?php echo $current_date; ?>">
                            <div class="cases-invalid-feedback"></div>
                        </div>
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Time</label>
                            <input type="time" 
                                   name="time" 
                                   id="consultation_time" 
                                   class="cases-form-control" 
                                   required
                                   value="<?php echo isset($consultation) && !empty($consultation->time) ? date('H:i', strtotime($consultation->time)) : $current_time; ?>"
                                   placeholder="<?php echo $current_time; ?>">
                            <div class="cases-invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Consultation Note</label>
                        <textarea name="note" id="consultation-note" class="cases-form-control cases-textarea" rows="6" 
                                  placeholder="Enter detailed consultation notes..." required><?php echo isset($consultation) ? htmlspecialchars($consultation->note) : ''; ?></textarea>
                        <div class="cases-invalid-feedback"></div>
                    </div>
                </div>
                <div class="cases-modal-footer">
                    <?php echo cases_button('Cancel', [
                        'type' => 'default',
                        'data' => ['dismiss' => 'modal']
                    ]); ?>
                    <button type="submit" class="cases-btn cases-btn-primary">
                        <span id="submit-btn-text">Save Consultation</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Single script block: no duplicate event handlers
$(document).ready(function() {
    const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    const csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    let consultationsData = [], casesData = [];
    // Utility functions here (htmlEscape, formatDate, etc.)
    function htmlEscape(str){ if(str==null) return ''; return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
    function formatDate(d){ if(!d) return ''; const dt=new Date(d); return dt.toLocaleDateString(undefined,{year:'numeric',month:'short',day:'numeric'}); }
    function formatTime(t){ if(!t) return ''; const dt=new Date(t.includes('T')?t:'1970-01-01T'+t); return dt.toLocaleTimeString(undefined,{hour:'numeric',minute:'2-digit'}); }
    // Load lists
    function loadConsultations(){ /* ... */ }
    function loadCases(){ /* ... */ }
    // Bind form submission once
    $('#consultationForm').off('submit').on('submit', function(e){ e.preventDefault(); /* handle create/update */ });
    // Tab switching with proper event delegation
    $(document).on('click', '[data-tab]', function(){ 
        const targetTab = $(this).data('tab');
        $('[data-tab]').removeClass('active').css({
            color: 'var(--cases-text-light)',
            borderBottomColor: 'transparent',
            background: 'none'
        });
        $(this).addClass('active').css({
            color: 'var(--cases-primary)',
            background: 'var(--cases-bg-primary)',
            borderBottomColor: 'var(--cases-primary)'
        });
        
        $('.tab-content').hide();
        $(`#${targetTab}-tab`).show();
        
        if (targetTab === 'consultations' && consultationsData.length === 0) {
            loadConsultations();
        } else if (targetTab === 'cases' && casesData.length === 0) {
            loadCases();
        }
    });
    
    // Delegated event handling for dynamic elements
    $(document)
        .on('click', '[data-action="edit"]', function() {
            const id = $(this).data('id');
            editConsultation(id);
        })
        .on('click', '[data-action="delete"]', function() {
            const id = $(this).data('id');
            deleteConsultation(id);
        })
        .on('click', '[data-action="upgrade"]', function() {
            const id = $(this).data('id');
            upgradeToLitigation(id);
        });
    // Initial load
    loadConsultations();
    // Cases loaded on tab click
});
</script>


<!-- View Note Modal -->
<div class="modal fade" id="viewNoteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="cases-modal-content">
            <div class="cases-modal-header">
                <h4 class="cases-modal-title">Consultation Note</h4>
                <button type="button" class="cases-modal-close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="cases-modal-body">
                <div id="noteContent" class="cases-consultation-note"></div>
            </div>
            <div class="cases-modal-footer">
                <?php echo cases_button('Close', [
                    'type' => 'default',
                    'data' => ['dismiss' => 'modal']
                ]); ?>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="cases-modal-content">
            <form id="upgradeForm">
                <div class="cases-modal-header">
                    <h4 class="cases-modal-title">Register Litigation Case</h4>
                    <button type="button" class="cases-modal-close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="cases-modal-body">
                    <input type="hidden" name="litigation_consultation_id" id="litigation_consultation_id">
                    
                    <div class="cases-grid cases-grid-2">
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Case Title</label>
                            <input type="text" name="case_title" id="case_title" class="cases-form-control" 
                                   placeholder="Enter descriptive case title" required>
                            <div class="cases-invalid-feedback"></div>
                        </div>
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Case Number</label>
                            <input type="text" name="case_number" id="case_number" class="cases-form-control" 
                                   placeholder="Court assigned case number" required>
                            <div class="cases-invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="cases-grid cases-grid-2">
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Court</label>
                            <select name="court_id" id="court_id_upgrade" class="cases-form-control" required>
                                <option value="">Select Court</option>
                            </select>
                            <div class="cases-invalid-feedback"></div>
                        </div>
                        <div class="cases-form-group">
                            <label class="cases-form-label cases-label-required">Court Room / Judge</label>
                            <select name="court_room_id" id="court_room_id_upgrade" class="cases-form-control" required>
                                <option value="">Select Court Room</option>
                            </select>
                            <div class="cases-invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Date Filed</label>
                        <input type="date" name="date_filed" id="date_filed" class="cases-form-control" required>
                        <div class="cases-invalid-feedback"></div>
                    </div>
                </div>
                <div class="cases-modal-footer">
                    <?php echo cases_button('Cancel', [
                        'type' => 'default',
                        'data' => ['dismiss' => 'modal']
                    ]); ?>
                    <button type="submit" class="cases-btn cases-btn-success">
                        <span id="upgrade-btn-text">Register Case</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Clean, minimal JavaScript - FIXED VERSION with Better Loading States
document.addEventListener('DOMContentLoaded', function() {
    // Variables
    let consultationsData = [];
    let casesData = [];
    let csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    let csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    // ===============================
    // ENHANCED LOADING FUNCTIONS
    // ===============================
    
    function showSkeletonLoading(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = `
            <div class="cases-grid cases-grid-responsive">
                ${Array.from({length: 6}, () => `
                    <div class="cases-skeleton-card">
                        <div class="cases-skeleton cases-skeleton-line title"></div>
                        <div class="cases-skeleton cases-skeleton-line medium"></div>
                        <div class="cases-skeleton cases-skeleton-line short"></div>
                        <div class="cases-skeleton cases-skeleton-line long"></div>
                        <div style="margin-top: 20px; display: flex; gap: 10px;">
                            <div class="cases-skeleton cases-skeleton-line" style="width: 60px; height: 32px;"></div>
                            <div class="cases-skeleton cases-skeleton-line" style="width: 60px; height: 32px;"></div>
                            <div class="cases-skeleton cases-skeleton-line" style="width: 60px; height: 32px;"></div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    function showContentLoading(containerId, message = 'Loading...') {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Check if already has loading container structure
        if (!container.classList.contains('cases-loading-container')) {
            container.classList.add('cases-loading-container');
        }
        
        container.innerHTML = `
            <div class="cases-loading-state">
                <div class="cases-loading-spinner"></div>
                <p class="cases-loading-text">${htmlEscape(message)}</p>
            </div>
        `;
    }
    
    function showErrorState(containerId, message, retryCallback = null) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.classList.remove('cases-loading-container');
        
        let retryButton = '';
        if (retryCallback) {
            retryButton = `
                <button class="cases-btn cases-btn-primary" onclick="${retryCallback}">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            `;
        }
        
        container.innerHTML = `
            <div class="cases-error-state">
                <i class="fas fa-exclamation-triangle cases-error-icon"></i>
                <h5 class="cases-error-title">Something went wrong</h5>
                <p class="cases-error-message">${htmlEscape(message)}</p>
                ${retryButton}
            </div>
        `;
    }
    
    function showEmptyState(containerId, title, description, actionButton = null) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.classList.remove('cases-loading-container');
        
        let button = '';
        if (actionButton) {
            button = `
                <button class="cases-btn cases-btn-primary" 
                        data-toggle="modal" 
                        data-target="#${actionButton.modal || 'consultationModal'}">
                    <i class="fas fa-plus"></i> ${actionButton.text}
                </button>
            `;
        }
        
        container.innerHTML = `
            <div class="cases-empty-state">
                <i class="fas fa-${actionButton?.icon || 'folder-open'} cases-empty-icon"></i>
                <h5 class="cases-empty-title">${htmlEscape(title)}</h5>
                <p class="cases-empty-description">${htmlEscape(description)}</p>
                ${button}
            </div>
        `;
    }
    
    function showContentWithTransition(containerId, content) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.classList.remove('cases-loading-container');
        container.innerHTML = `<div class="cases-content-transition">${content}</div>`;
        
        // Trigger transition after a brief delay
        setTimeout(() => {
            const transition = container.querySelector('.cases-content-transition');
            if (transition) {
                transition.classList.add('loaded');
            }
        }, 50);
    }
    
    function addTabLoadingIndicator(tabButton) {
        if (tabButton && !tabButton.classList.contains('cases-tab-loading')) {
            tabButton.classList.add('cases-tab-loading');
            setTimeout(() => {
                tabButton.classList.remove('cases-tab-loading');
            }, 2000);
        }
    }
    
    // ===============================
    // UTILITY FUNCTIONS
    // ===============================
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return 'N/A';
        }
    }
    
    function htmlEscape(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    function buildInvoiceDisplayText(invoice) {
        let displayText = invoice.formatted_number || 'Invoice #' + invoice.id;
        
        if (invoice.total && parseFloat(invoice.total) > 0) {
            const currency = invoice.currency_symbol || '₹';
            displayText += ' - ' + currency + parseFloat(invoice.total).toFixed(2);
        }
        
        if (invoice.status_text) {
            displayText += ' (' + invoice.status_text + ')';
        }
        
        return displayText;
    }
    
    // Update stats
    function updateStats() {
        const consultationsCount = consultationsData.length;
        const casesCount = casesData.length;
        let litigationCount = 0;
        
        consultationsData.forEach(c => {
            if (c.phase === 'litigation') litigationCount++;
        });
        
        // Animate counter updates
        const counters = [
            { element: document.getElementById('consultations-count'), target: consultationsCount },
            { element: document.getElementById('cases-count'), target: casesCount },
            { element: document.getElementById('litigation-count'), target: litigationCount },
            { element: document.getElementById('consultations-badge'), target: consultationsCount },
            { element: document.getElementById('cases-badge'), target: casesCount },
            { element: document.getElementById('upcoming-count'), target: 0 }
        ];
        
        counters.forEach(counter => {
            if (counter.element) {
                animateCounter(counter.element, parseInt(counter.element.textContent) || 0, counter.target);
            }
        });
    }
    
    function animateCounter(element, start, end, duration = 500) {
        const range = end - start;
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(start + (range * progress));
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }
        
        requestAnimationFrame(updateCounter);
    }
    
    // ===============================
    // RENDER FUNCTIONS
    // ===============================
    
    // Render consultations with cases framework
    function renderConsultations(data) {
        const containerId = 'consultations-container';
        
        if (!data || data.length === 0) {
            showEmptyState(
                containerId,
                'No consultations found',
                'Start by adding your first consultation to track client meetings and legal advice.',
                { text: 'Add Consultation', icon: 'comments', modal: 'consultationModal' }
            );
            return;
        }
        
        let html = '<div class="cases-grid cases-grid-responsive">';
        
        data.forEach(consultation => {
            const statusType = consultation.phase === 'litigation' ? 'litigation' : 'consultation';
            const statusText = consultation.phase === 'litigation' ? 'Litigation' : 'Consultation';
            
            html += `
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div class="cases-card-title">${htmlEscape(consultation.client_name || 'Unknown Client')}</div>
                        <span class="cases-status-badge cases-status-${statusType}">${statusText}</span>
                    </div>
                    
                    <div class="cases-card-body">
                        <div class="cases-card-meta-grid">
                            ${consultation.contact_name ? `
                                <div class="cases-card-meta-item">
                                    <span class="cases-card-meta-label">Contact:</span>
                                    <span class="cases-card-meta-value">${htmlEscape(consultation.contact_name)}</span>
                                </div>
                            ` : ''}
                            ${consultation.tag ? `
                                <div class="cases-card-meta-item">
                                    <span class="cases-card-meta-label">Tag:</span>
                                    <span class="cases-card-meta-value">${htmlEscape(consultation.tag)}</span>
                                </div>
                            ` : ''}
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label">Date:</span>
                                <span class="cases-card-meta-value">${formatDate(consultation.date_added)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cases-card-footer">
                        <div class="cases-card-actions">
                            <button class="cases-action-btn cases-btn-default" data-action="view" data-id="${consultation.id}">
                                View
                            </button>
                            <button class="cases-action-btn cases-btn-primary" data-action="edit" data-id="${consultation.id}">
                                Edit
                            </button>
                            ${consultation.phase === 'consultation' ? `
                                <button class="cases-action-btn cases-btn-success" data-action="upgrade" data-id="${consultation.id}">
                                    Upgrade
                                </button>
                            ` : ''}
                            <button class="cases-action-btn cases-btn-danger" data-action="delete" data-id="${consultation.id}">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        showContentWithTransition(containerId, html);
    }
    
    // Render cases with cases framework
    function renderCases(data) {
        const containerId = 'cases-container';
        
        if (!data || data.length === 0) {
            showEmptyState(
                containerId,
                'No cases found',
                'Cases will appear here when consultations are upgraded to litigation cases.',
                { text: 'View Consultations', icon: 'briefcase' }
            );
            return;
        }
        
        let html = '<div class="cases-grid cases-grid-responsive">';
        
        data.forEach(caseItem => {
            html += `
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div class="cases-card-title">${htmlEscape(caseItem.case_title)}</div>
                        <span class="cases-status-badge cases-status-active">${htmlEscape(caseItem.case_number)}</span>
                    </div>
                    
                    <div class="cases-card-body">
                        <div class="cases-card-meta-grid">
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label">Client:</span>
                                <span class="cases-card-meta-value">${htmlEscape(caseItem.client_name || 'Unknown Client')}</span>
                            </div>
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label">Court:</span>
                                <span class="cases-card-meta-value">${htmlEscape(caseItem.court_display || 'Court not specified')}</span>
                            </div>
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label">Filed:</span>
                                <span class="cases-card-meta-value">${formatDate(caseItem.date_filed)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cases-card-footer">
                        <div class="cases-card-actions">
                            <a href="${admin_url}cases/details?id=${encodeURIComponent(caseItem.id)}" class="cases-action-btn cases-btn-primary">
                                Details
                            </a>
                            <a href="${admin_url}cases/hearings/add?case_id=${encodeURIComponent(caseItem.id)}" class="cases-action-btn cases-btn-success">
                                Add Hearing
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        showContentWithTransition(containerId, html);
    }
    
    // ===============================
    // DATA LOADING FUNCTIONS
    // ===============================
    
    // Load data functions
    function loadConsultations() {
        console.log('Loading consultations...');
        showSkeletonLoading('consultations-container');
        
        const consultationsTab = document.querySelector('[data-tab="consultations"]');
        addTabLoadingIndicator(consultationsTab);
        
        fetch(admin_url + 'cases/consultations_list', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Consultations response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Consultations data received:', data);
            if (data && data.success) {
                consultationsData = data.data || [];
                renderConsultations(consultationsData);
                updateStats();
            } else {
                throw new Error(data.message || 'Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error loading consultations:', error);
            showErrorState('consultations-container', 'Failed to load consultations: ' + error.message, 'loadConsultations()');
        });
    }
    
    function loadCases() {
        console.log('Loading cases...');
        showSkeletonLoading('cases-container');
        
        const casesTab = document.querySelector('[data-tab="cases"]');
        addTabLoadingIndicator(casesTab);
        
        fetch(admin_url + 'cases/cases_list', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Cases response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Cases data received:', data);
            if (data && data.success) {
                casesData = data.data || [];
                renderCases(casesData);
                updateStats();
            } else {
                throw new Error(data.message || 'Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error loading cases:', error);
            showErrorState('cases-container', 'Failed to load cases: ' + error.message, 'loadCases()');
        });
    }
    
    function loadContactsByClient(clientId) {
        if (!clientId) {
            const contactGroup = document.getElementById('contact-group');
            if (contactGroup) contactGroup.style.display = 'none';
            return;
        }
        
        fetch(admin_url + 'cases/get_contacts_by_client/' + clientId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('contact_id');
            const contactGroup = document.getElementById('contact-group');
            
            if (!select) return;
            
            select.innerHTML = '<option value="">Select Contact (Optional)</option>';
            
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(contact => {
                    const contactName = contact.full_name || (contact.firstname + ' ' + contact.lastname).trim();
                    select.innerHTML += `<option value="${contact.id}">${htmlEscape(contactName)}</option>`;
                });
                if (contactGroup) contactGroup.style.display = 'block';
            } else {
                select.innerHTML += '<option value="">No contacts available</option>';
                if (contactGroup) contactGroup.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading contacts:', error);
            const select = document.getElementById('contact_id');
            if (select) {
                select.innerHTML = '<option value="">Error loading contacts</option>';
            }
            const contactGroup = document.getElementById('contact-group');
            if (contactGroup) contactGroup.style.display = 'block';
        });
    }
    
    function loadInvoicesByClient(clientId) {
        const select = document.getElementById('invoice_id');
        if (!select) return;
        
        if (!clientId) {
            select.innerHTML = '<option value="">Select Invoice</option>';
            return;
        }
        
        const formData = new FormData();
        formData.append(csrfTokenName, csrfTokenHash);
        
        fetch(admin_url + 'cases/get_invoices_by_client/' + clientId, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">Select Invoice</option>';
            
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(invoice => {
                    const displayText = buildInvoiceDisplayText(invoice);
                    select.innerHTML += `<option value="${invoice.id}">${htmlEscape(displayText)}</option>`;
                });
            } else {
                select.innerHTML += '<option value="">No invoices available</option>';
                select.innerHTML += '<option value="skip_invoice">Continue Without Invoice</option>';
            }
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            select.innerHTML = '<option value="">Error loading invoices</option>';
        });
    }
    
    // ===============================
    // TAB FUNCTIONALITY
    // ===============================
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Update active states
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.color = 'var(--cases-text-light)';
                btn.style.borderBottomColor = 'transparent';
                btn.style.background = 'none';
            });
            
            this.classList.add('active');
            this.style.color = 'var(--cases-primary)';
            this.style.background = 'var(--cases-bg-primary)';
            this.style.borderBottomColor = 'var(--cases-primary)';
            
            // Show/hide content
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.style.display = 'block';
            }
            
            // Load data for active tab if not already loaded
            if (targetTab === 'consultations' && consultationsData.length === 0) {
                loadConsultations();
            } else if (targetTab === 'cases' && casesData.length === 0) {
                loadCases();
            }
        });
    });
    
    // ===============================
    // GLOBAL FUNCTIONS FOR ONCLICK HANDLERS
    // ===============================
    
    // Global functions for onclick handlers
    window.viewNote = function(id) {
        console.log('Viewing note for consultation:', id);
        const consultation = consultationsData.find(c => c.id == id);
        if (!consultation) {
            console.error('Consultation not found with id:', id);
            alert('Consultation not found');
            return;
        }
        
        const noteContent = document.getElementById('noteContent');
        if (noteContent) {
            noteContent.innerHTML = consultation.note || 'No note available';
        }
        $('#viewNoteModal').modal('show');
    };
    
    window.editConsultation = function(id) {
        const consultation = consultationsData.find(c => c.id == id);
        if (!consultation) {
            console.error('Consultation not found with id:', id);
            alert('Consultation not found');
            return;
        }
        
        console.log('Editing consultation:', consultation);
        
        // Populate form fields
        const consultationIdField = document.getElementById('consultation_id');
        const clientIdField = document.getElementById('client_id');
        const tagField = document.getElementById('consultation_tag');
        const noteField = document.getElementById('consultation-note');
        const modalTitleField = document.getElementById('modal-title-text');
        const submitBtnField = document.getElementById('submit-btn-text');
        
        if (consultationIdField) consultationIdField.value = consultation.id;
        if (clientIdField) clientIdField.value = consultation.client_id;
        if (tagField) tagField.value = consultation.tag || '';
        if (noteField) noteField.value = consultation.note || '';
        
        // Update modal title and button text
        if (modalTitleField) modalTitleField.textContent = 'Edit Consultation';
        if (submitBtnField) submitBtnField.textContent = 'Update Consultation';
        
        // Load contacts and invoices for this client
        if (consultation.client_id) {
            loadContactsByClient(consultation.client_id);
            loadInvoicesByClient(consultation.client_id);
            
            // Set contact after a brief delay to allow the options to load
            setTimeout(() => {
                const contactField = document.getElementById('contact_id');
                if (contactField && consultation.contact_id) {
                    contactField.value = consultation.contact_id;
                }
            }, 500);
        }
        
        // Show the modal
        $('#consultationModal').modal('show');
    };
    
    window.upgradeToLitigation = function(id) {
        console.log('Upgrading consultation to litigation:', id);
        const litigationIdField = document.getElementById('litigation_consultation_id');
        if (litigationIdField) {
            litigationIdField.value = id;
        }
        
        // Load courts
        fetch(admin_url + 'cases/courts/get_all_courts', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Courts loaded:', data);
            const select = document.getElementById('court_id_upgrade');
            if (select) {
                select.innerHTML = '<option value="">Select Court</option>';
                if (data.success && data.data) {
                    data.data.forEach(court => {
                        select.innerHTML += `<option value="${court.id}">${htmlEscape(court.name)}</option>`;
                    });
                } else {
                    select.innerHTML += '<option value="">No courts available</option>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading courts:', error);
            const select = document.getElementById('court_id_upgrade');
            if (select) {
                select.innerHTML = '<option value="">Error loading courts</option>';
            }
        });
        
        $('#upgradeModal').modal('show');
    };
    
    window.deleteConsultation = function(id) {
        console.log('Deleting consultation:', id);
        if (!confirm('Are you sure you want to delete this consultation?')) return;
        
        const formData = new FormData();
        formData.append(csrfTokenName, csrfTokenHash);
        
        fetch(admin_url + 'cases/delete_consultation/' + id, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Delete response:', data);
            if (data.success) {
                loadConsultations();
                alert('Consultation deleted successfully');
            } else {
                alert('Failed to delete consultation: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error occurred');
        });
    };
    
    // Global retry functions
    window.loadConsultations = loadConsultations;
    window.loadCases = loadCases;
    
    // ===============================
    // EVENT LISTENERS
    // ===============================
    
    // Form submissions
    const consultationForm = document.getElementById('consultationForm');
    if (consultationForm) {
        consultationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append(csrfTokenName, csrfTokenHash);
            
            // Determine if this is an edit or create operation
            const consultationId = document.getElementById('consultation_id').value;
            const url = consultationId ? 
                admin_url + 'cases/update_consultation' : 
                admin_url + 'cases/create_consultation';
            
            // Add consultation ID to form data if editing
            if (consultationId) {
                formData.append('id', consultationId);
            }
            
            console.log('Submitting to:', url, 'with consultation_id:', consultationId);
            
            const submitBtn = document.getElementById('submit-btn-text');
            const originalText = submitBtn ? submitBtn.textContent : '';
            
            if (submitBtn) {
                submitBtn.textContent = 'Saving...';
                submitBtn.parentElement.disabled = true;
            }
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    $('#consultationModal').modal('hide');
                    loadConsultations();
                    alert(consultationId ? 'Consultation updated successfully' : 'Consultation saved successfully');
                    
                    // Reset form and modal state
                    this.reset();
                    resetModalState();
                } else {
                    alert('Failed to save consultation: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.parentElement.disabled = false;
                }
            });
        });
    }
    
    const upgradeForm = document.getElementById('upgradeForm');
    if (upgradeForm) {
        upgradeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append(csrfTokenName, csrfTokenHash);
            
            const submitBtn = document.getElementById('upgrade-btn-text');
            const originalText = submitBtn ? submitBtn.textContent : '';
            
            if (submitBtn) {
                submitBtn.textContent = 'Registering...';
                submitBtn.parentElement.disabled = true;
            }
            
            fetch(admin_url + 'cases/upgrade_to_litigation', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#upgradeModal').modal('hide');
                    loadConsultations();
                    loadCases();
                    alert('Case registered successfully');
                    this.reset();
                } else {
                    alert('Failed to register case: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.parentElement.disabled = false;
                }
            });
        });
    }
    
    // Function to reset modal state
    function resetModalState() {
        const modalTitle = document.getElementById('modal-title-text');
        const submitBtn = document.getElementById('submit-btn-text');
        const consultationIdField = document.getElementById('consultation_id');
        const contactGroup = document.getElementById('contact-group');
        
        if (modalTitle) modalTitle.textContent = 'Add Consultation';
        if (submitBtn) submitBtn.textContent = 'Save Consultation';
        if (consultationIdField) consultationIdField.value = '';
        if (contactGroup) contactGroup.style.display = 'none';
    }
    
    // Client change handler
    const clientIdField = document.getElementById('client_id');
    if (clientIdField) {
        clientIdField.addEventListener('change', function() {
            const clientId = this.value;
            if (clientId) {
                loadContactsByClient(clientId);
                loadInvoicesByClient(clientId);
            } else {
                const contactGroup = document.getElementById('contact-group');
                const contactSelect = document.getElementById('contact_id');
                const invoiceSelect = document.getElementById('invoice_id');
                
                if (contactGroup) contactGroup.style.display = 'none';
                if (contactSelect) contactSelect.innerHTML = '<option value="">Select Contact (Optional)</option>';
                if (invoiceSelect) invoiceSelect.innerHTML = '<option value="">Select Invoice</option>';
            }
        });
    }
    
    // Court change handler
    const courtIdField = document.getElementById('court_id_upgrade');
    if (courtIdField) {
        courtIdField.addEventListener('change', function() {
            const courtId = this.value;
            const select = document.getElementById('court_room_id_upgrade');
            
            if (!select) return;
            
            if (courtId) {
                fetch(admin_url + 'cases/courts/get_rooms_by_court/' + courtId, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Select Court Room</option>';
                    if (data.success && data.data) {
                        data.data.forEach(room => {
                            select.innerHTML += `<option value="${room.id}">Court ${room.court_no} - ${htmlEscape(room.judge_name)}</option>`;
                        });
                    }
                });
            } else {
                select.innerHTML = '<option value="">Select Court Room</option>';
            }
        });
    }
    
    // Search functionality
    const consultationsSearch = document.getElementById('consultations-search');
    if (consultationsSearch) {
        consultationsSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = consultationsData.filter(c => 
                (c.client_name || '').toLowerCase().includes(searchTerm) ||
                (c.contact_name || '').toLowerCase().includes(searchTerm) ||
                (c.tag || '').toLowerCase().includes(searchTerm)
            );
            renderConsultations(filtered);
        });
    }
    
    const casesSearch = document.getElementById('cases-search');
    if (casesSearch) {
        casesSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = casesData.filter(c => 
                (c.case_title || '').toLowerCase().includes(searchTerm) ||
                (c.case_number || '').toLowerCase().includes(searchTerm) ||
                (c.client_name || '').toLowerCase().includes(searchTerm) ||
                (c.court_display || '').toLowerCase().includes(searchTerm)
            );
            renderCases(filtered);
        });
    }
    
    // Filter functionality
    const consultationsFilter = document.getElementById('consultations-filter');
    if (consultationsFilter) {
        consultationsFilter.addEventListener('change', function() {
            const filterValue = this.value;
            let filtered = consultationsData;
            
            if (filterValue) {
                filtered = consultationsData.filter(c => c.phase === filterValue);
            }
            
            renderConsultations(filtered);
        });
    }
    
    // Refresh buttons
    const refreshConsultations = document.getElementById('refresh-consultations');
    if (refreshConsultations) {
        refreshConsultations.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            loadConsultations();
            
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            }, 2000);
        });
    }
    
    const refreshCases = document.getElementById('refresh-cases');
    if (refreshCases) {
        refreshCases.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            loadCases();
            
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            }, 2000);
        });
    }
    
    // Modal reset handlers
    $('#consultationModal').on('hidden.bs.modal', function () {
        const form = document.getElementById('consultationForm');
        if (form) {
            form.reset();
            resetModalState();
        }
    });
    
    $('#upgradeModal').on('hidden.bs.modal', function () {
        const form = document.getElementById('upgradeForm');
        if (form) form.reset();
    });
    
    // ===============================
    // INITIALIZATION
    // ===============================
    
    // Load initial data
    console.log('Initializing data load...');
    loadConsultations();
    loadCases();
    // Don't load cases initially - wait for tab click for better performance
    
    console.log('Cases management JavaScript initialized successfully with enhanced loading states');
});
</script>

<?php init_tail(); ?>
