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
    <div id="consultations-container">
        <?php echo cases_loading_state('Loading consultations...'); ?>
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
    <div id="cases-container">
        <?php echo cases_loading_state('Loading cases...'); ?>
    </div>
</div>

<?php echo cases_section_end(); ?>

<?php echo cases_page_wrapper_end(); ?>

<!-- Consultation Modal -->
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

                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Consultation Note</label>
                        <textarea name="note" id="consultation-note" class="cases-form-control cases-textarea" rows="6" 
                                  placeholder="Enter detailed consultation notes..." required></textarea>
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
// FIXED AND RECODED JavaScript for Cases Management
document.addEventListener('DOMContentLoaded', function() {
    // Core variables
    let consultationsData = [];
    let casesData = [];
    const csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    const csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    console.log('🚀 Cases Management System Starting...');
    console.log('Admin URL:', admin_url);
    
    // ===== UTILITY FUNCTIONS =====
    
    function showLoading(containerId, message = 'Loading...') {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = `
            <div class="cases-loading-state" style="text-align: center; padding: 60px 40px; color: #666;">
                <div style="margin-bottom: 20px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--cases-primary);"></i>
                </div>
                <p style="margin: 0; font-weight: 500;">${message}</p>
            </div>
        `;
    }
    
    function showError(containerId, message, details = null) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const timestamp = new Date().toLocaleString();
        const debugInfo = details ? `
            <details style="margin-top: 15px; text-align: left;">
                <summary style="cursor: pointer; color: #6c757d; font-size: 0.875rem;">Show Debug Info</summary>
                <div style="background: #f8f9fa; padding: 15px; margin-top: 10px; border-radius: 5px; font-family: monospace; font-size: 0.8rem; border: 1px solid #e9ecef;">
                    <p><strong>Error:</strong> ${htmlEscape(details)}</p>
                    <p><strong>Time:</strong> ${timestamp}</p>
                    <p><strong>Container:</strong> ${containerId}</p>
                    <p><strong>URL:</strong> ${window.location.href}</p>
                </div>
            </details>
        ` : '';
        
        container.innerHTML = `
            <div class="cases-empty-state" style="text-align: center; padding: 60px 40px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 20px;"></i>
                <h5 style="color: #dc3545; margin-bottom: 15px;">Error Loading Data</h5>
                <p style="color: #6c757d; margin-bottom: 25px;">${htmlEscape(message)}</p>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <button class="cases-btn cases-btn-primary" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Reload Page
                    </button>
                    <button class="cases-btn cases-btn-default" onclick="retryLoad('${containerId}')">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
                ${debugInfo}
            </div>
        `;
    }
    
    function showEmpty(containerId, title, message, actionButton = null) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const action = actionButton ? `
            <button class="cases-btn cases-btn-primary" ${actionButton.onclick ? `onclick="${actionButton.onclick}"` : ''} 
                    ${actionButton.modal ? `data-toggle="modal" data-target="${actionButton.modal}"` : ''}>
                ${actionButton.icon ? `<i class="${actionButton.icon}"></i> ` : ''}${actionButton.text}
            </button>
        ` : '';
        
        container.innerHTML = `
            <div class="cases-empty-state" style="text-align: center; padding: 60px 40px; color: #6c757d;">
                <i class="fas fa-${actionButton?.emptyIcon || 'inbox'}" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                <h5 style="margin-bottom: 10px; color: #495057;">${title}</h5>
                <p style="margin-bottom: 25px;">${message}</p>
                ${action}
            </div>
        `;
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
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric'
            }) + ' ' + date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }
    
    // ===== ENHANCED FETCH WITH MIXED CONTENT HANDLING =====
    
    function safeFetch(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            },
            credentials: 'same-origin'
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        console.log(`📡 Fetching: ${url}`);
        
        return fetch(url, finalOptions)
            .then(response => {
                console.log(`📥 Response: ${response.status} for ${url}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response.text(); // Always get as text first
            })
            .then(text => {
                console.log(`📄 Response length: ${text.length} chars`);
                console.log(`📄 Response preview: ${text.substring(0, 100)}...`);
                
                // Handle mixed content (script + JSON)
                if (text.trim().startsWith('<script>')) {
                    console.warn('⚠️ Mixed content detected! Extracting JSON...');
                    
                    const jsonMatch = text.match(/\{.*\}/s);
                    if (jsonMatch) {
                        const jsonPart = jsonMatch[0];
                        console.log(`✅ Extracted JSON: ${jsonPart.substring(0, 100)}...`);
                        return JSON.parse(jsonPart);
                    } else {
                        throw new Error('No JSON found in mixed content response');
                    }
                }
                
                // Handle pure HTML response (error pages)
                if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
                    throw new Error('Received HTML page instead of JSON (possible login redirect)');
                }
                
                // Try normal JSON parsing
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('❌ JSON parse failed:', e);
                    console.error('📄 Full response:', text.substring(0, 1000));
                    throw new Error(`Invalid JSON response: ${e.message}`);
                }
            })
            .catch(error => {
                console.error(`❌ Fetch failed for ${url}:`, error);
                throw error;
            });
    }
    
    // ===== TAB SYSTEM =====
    
    function initializeTabs() {
        const tabButtons = document.querySelectorAll('[data-tab]');
        const tabContents = document.querySelectorAll('.tab-content');
        
        if (tabButtons.length === 0) {
            console.warn('⚠️ No tab buttons found');
            return;
        }
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                console.log(`🔄 Switching to tab: ${targetTab}`);
                
                // Update button states
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
                
                // Update content visibility
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
                
                const targetContent = document.getElementById(targetTab + '-tab');
                if (targetContent) {
                    targetContent.style.display = 'block';
                } else {
                    console.error(`❌ Tab content not found: ${targetTab}-tab`);
                }
            });
        });
        
        console.log('✅ Tabs initialized');
    }
    
    // ===== DATA LOADING FUNCTIONS =====
    
    function loadConsultations() {
        console.log('📋 Loading consultations...');
        showLoading('consultations-container', 'Loading consultations...');
        
        safeFetch(admin_url + 'cases/consultations_list')
            .then(data => {
                console.log('📋 Consultations response:', data);
                
                if (data && data.success) {
                    consultationsData = Array.isArray(data.data) ? data.data : [];
                    console.log(`✅ Loaded ${consultationsData.length} consultations`);
                    renderConsultations(consultationsData);
                    updateStats();
                } else {
                    const errorMsg = data?.message || 'Invalid response structure';
                    console.error('❌ Invalid consultations response:', data);
                    showError('consultations-container', 'Failed to load consultations', errorMsg);
                }
            })
            .catch(error => {
                console.error('❌ Consultations load failed:', error);
                showError('consultations-container', 'Network error loading consultations', error.message);
            });
    }
    
    function loadCases() {
        console.log('💼 Loading cases...');
        showLoading('cases-container', 'Loading cases...');
        
        safeFetch(admin_url + 'cases/cases_list')
            .then(data => {
                console.log('💼 Cases response:', data);
                
                if (data && data.success) {
                    casesData = Array.isArray(data.data) ? data.data : [];
                    console.log(`✅ Loaded ${casesData.length} cases`);
                    renderCases(casesData);
                    updateStats();
                } else {
                    const errorMsg = data?.message || 'Invalid response structure';
                    console.error('❌ Invalid cases response:', data);
                    showError('cases-container', 'Failed to load cases', errorMsg);
                }
            })
            .catch(error => {
                console.error('❌ Cases load failed:', error);
                showError('cases-container', 'Network error loading cases', error.message);
            });
    }
    
    // ===== RENDERING FUNCTIONS =====
    
    function renderConsultations(data) {
        const container = document.getElementById('consultations-container');
        if (!container) {
            console.error('❌ Consultations container not found');
            return;
        }
        
        if (!data || data.length === 0) {
            showEmpty('consultations-container', 
                'No Consultations Found', 
                'Start by adding your first consultation',
                {
                    text: 'Add Consultation',
                    icon: 'fas fa-plus',
                    modal: '#consultationModal',
                    emptyIcon: 'comments'
                }
            );
            return;
        }
        
        let html = '<div class="cases-grid cases-grid-responsive">';
        
        data.forEach(consultation => {
            const statusType = consultation.phase === 'litigation' ? 'litigation' : 'consultation';
            const statusText = consultation.phase === 'litigation' ? 'Litigation' : 'Consultation';
            const statusColor = consultation.phase === 'litigation' ? 'success' : 'info';
            
            html += `
                <div class="cases-card cases-hover-lift" style="transition: all 0.2s ease;">
                    <div class="cases-card-header">
                        <div class="cases-card-title" style="font-weight: 600; margin-bottom: 5px;">
                            ${htmlEscape(consultation.client_name || 'Unknown Client')}
                        </div>
                        <span class="cases-status-badge cases-status-${statusType}" 
                              style="background: var(--cases-${statusColor}); color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem;">
                            ${statusText}
                        </span>
                    </div>
                    
                    <div class="cases-card-body">
                        <div class="cases-card-meta-grid" style="display: grid; grid-template-columns: 1fr; gap: 8px;">
                            ${consultation.contact_name ? `
                                <div class="cases-card-meta-item">
                                    <span class="cases-card-meta-label" style="color: #6c757d; font-size: 0.8rem;">Contact:</span>
                                    <span class="cases-card-meta-value" style="font-weight: 500;">${htmlEscape(consultation.contact_name)}</span>
                                </div>
                            ` : ''}
                            ${consultation.tag ? `
                                <div class="cases-card-meta-item">
                                    <span class="cases-card-meta-label" style="color: #6c757d; font-size: 0.8rem;">Category:</span>
                                    <span class="cases-card-meta-value" style="font-weight: 500; color: var(--cases-primary);">${htmlEscape(consultation.tag)}</span>
                                </div>
                            ` : ''}
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label" style="color: #6c757d; font-size: 0.8rem;">Date:</span>
                                <span class="cases-card-meta-value" style="font-weight: 500;">${formatDate(consultation.date_added)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cases-card-footer">
                        <div class="cases-card-actions" style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <button class="cases-action-btn cases-btn-default" onclick="viewNote(${consultation.id})" 
                                    style="padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="cases-action-btn cases-btn-primary" onclick="editConsultation(${consultation.id})"
                                    style="padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            ${consultation.phase === 'consultation' ? `
                                <button class="cases-action-btn cases-btn-success" onclick="upgradeToLitigation(${consultation.id})"
                                        style="padding: 5px 10px; font-size: 0.8rem;">
                                    <i class="fas fa-arrow-up"></i> Upgrade
                                </button>
                            ` : ''}
                            <button class="cases-action-btn cases-btn-danger" onclick="deleteConsultation(${consultation.id})"
                                    style="padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
        console.log('✅ Consultations rendered');
    }
    
    function renderCases(data) {
        const container = document.getElementById('cases-container');
        if (!container) {
            console.error('❌ Cases container not found');
            return;
        }
        
        if (!data || data.length === 0) {
            showEmpty('cases-container', 
                'No Cases Found', 
                'Cases will appear here when consultations are upgraded to litigation',
                {
                    emptyIcon: 'briefcase'
                }
            );
            return;
        }
        
        let html = '<div class="cases-grid cases-grid-responsive">';
        
        data.forEach(caseItem => {
            html += `
                <div class="cases-card cases-hover-lift" style="transition: all 0.2s ease;">
                    <div class="cases-card-header">
                        <div class="cases-card-title" style="font-weight: 600; margin-bottom: 5px;">
                            ${htmlEscape(caseItem.case_title || 'Untitled Case')}
                        </div>
                        <span class="cases-status-badge cases-status-active" 
                              style="background: var(--cases-primary); color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem;">
                            ${htmlEscape(caseItem.case_number || 'No Number')}
                        </span>
                    </div>
                    
                    <div class="cases-card-body">
                        <div class="cases-card-meta-grid" style="display: grid; grid-template-columns: 1fr; gap: 8px;">
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label" style="color: #6c757d; font-size: 0.8rem;">Client:</span>
                                <span class="cases-card-meta-value" style="font-weight: 500;">${htmlEscape(caseItem.client_name || 'Unknown Client')}</span>
                            </div>
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label" style="color: #6c757d; font-size: 0.8rem;">Court:</span>
                                <span class="cases-card-meta-value" style="font-weight: 500;">${htmlEscape(caseItem.court_display || 'Court not specified')}</span>
                            </div>
                            <div class="cases-card-meta-item">
                                <span class="cases-card-meta-label" style="color: #6c757d; font-size: 0.8rem;">Filed:</span>
                                <span class="cases-card-meta-value" style="font-weight: 500;">${formatDate(caseItem.date_filed)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cases-card-footer">
                        <div class="cases-card-actions" style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="${admin_url}cases/details?id=${encodeURIComponent(caseItem.id)}" 
                               class="cases-action-btn cases-btn-primary" style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;">
                                <i class="fas fa-info-circle"></i> Details
                            </a>
                            <a href="${admin_url}cases/hearings/add?case_id=${encodeURIComponent(caseItem.id)}" 
                               class="cases-action-btn cases-btn-success" style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;">
                                <i class="fas fa-calendar-plus"></i> Add Hearing
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
        console.log('✅ Cases rendered');
    }
    
    // ===== STATISTICS UPDATE =====
    
    function updateStats() {
        const consultationsCount = consultationsData.length;
        const casesCount = casesData.length;
        let litigationCount = 0;
        
        consultationsData.forEach(c => {
            if (c.phase === 'litigation') litigationCount++;
        });
        
        const elements = {
            'consultations-count': consultationsCount,
            'cases-count': casesCount,
            'litigation-count': litigationCount,
            'consultations-badge': consultationsCount,
            'cases-badge': casesCount,
            'upcoming-count': 0 // TODO: Calculate from hearings
        };
        
        Object.keys(elements).forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                const oldValue = element.textContent;
                const newValue = elements[id];
                element.textContent = newValue;
                
                // Add animation for changed values
                if (oldValue !== '-' && oldValue !== newValue.toString()) {
                    element.style.transform = 'scale(1.1)';
                    element.style.color = 'var(--cases-success)';
                    setTimeout(() => {
                        element.style.transform = 'scale(1)';
                        element.style.color = '';
                    }, 300);
                }
            }
        });
        
        console.log('📊 Stats updated:', elements);
    }
    
    // ===== GLOBAL ACTION FUNCTIONS =====
    
    window.viewNote = function(id) {
        console.log(`👁️ Viewing note for consultation: ${id}`);
        const consultation = consultationsData.find(c => c.id == id);
        if (!consultation) {
            alert('Consultation not found');
            return;
        }
        
        const noteContent = document.getElementById('noteContent');
        if (noteContent) {
            noteContent.innerHTML = consultation.note || '<em>No note available</em>';
        }
        
        // Show modal
        if (typeof $ !== 'undefined' && $.fn.modal) {
            $('#viewNoteModal').modal('show');
        }
    };
    
    window.editConsultation = function(id) {
        console.log(`✏️ Editing consultation: ${id}`);
        // TODO: Implement edit functionality
        alert('Edit functionality will be implemented');
    };
    
    window.upgradeToLitigation = function(id) {
        console.log(`⬆️ Upgrading consultation to litigation: ${id}`);
        // TODO: Implement upgrade functionality
        alert('Upgrade functionality will be implemented');
    };
    
    window.deleteConsultation = function(id) {
        console.log(`🗑️ Deleting consultation: ${id}`);
        if (!confirm('Are you sure you want to delete this consultation?')) return;
        
        // TODO: Implement delete functionality
        alert('Delete functionality will be implemented');
    };
    
    // ===== RETRY FUNCTIONALITY =====
    
    window.retryLoad = function(containerId) {
        console.log(`🔄 Retrying load for: ${containerId}`);
        if (containerId === 'consultations-container') {
            loadConsultations();
        } else if (containerId === 'cases-container') {
            loadCases();
        }
    };
    
    // ===== INITIALIZATION =====
    
    function initialize() {
        console.log('🔧 Initializing Cases Management System...');
        
        // Initialize tabs
        initializeTabs();
        
        // Load initial data
        loadConsultations();
        loadCases();
        
        // Make functions globally available
        window.loadConsultations = loadConsultations;
        window.loadCases = loadCases;
        window.safeFetch = safeFetch;
        
        console.log('✅ Cases Management System initialized successfully!');
    }
    
    // Start the system
    initialize();
});
</script>

<?php init_tail(); ?>