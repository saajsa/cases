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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 8px 0;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #666666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.main-content {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    overflow: hidden;
}

.tab-navigation {
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
    padding: 0;
    margin: 0;
    display: flex;
}

.tab-btn {
    background: none;
    border: none;
    padding: 20px 30px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #666666;
    cursor: pointer;
    transition: all 0.15s ease;
    border-bottom: 2px solid transparent;
}

.tab-btn.active {
    color: #1a1a1a;
    background: #ffffff;
    border-bottom-color: #1a1a1a;
}

.tab-btn:hover {
    color: #1a1a1a;
    background: #ffffff;
}

.tab-content {
    padding: 30px;
}

.search-filters {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 15px;
    margin-bottom: 25px;
    align-items: end;
}

.search-group {
    position: relative;
}

.search-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.search-input:focus {
    outline: none;
    border-color: #1a1a1a;
}

.filter-select {
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
    min-width: 150px;
}

.filter-select:focus {
    outline: none;
    border-color: #1a1a1a;
}

.refresh-btn {
    padding: 12px 20px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    background: #ffffff;
    color: #2c2c2c;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.refresh-btn:hover {
    background: #f8f8f8;
    border-color: #999999;
}

.items-grid {
    display: grid;
    gap: 20px;
}

.item-card {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 25px;
    border-radius: 2px;
    transition: all 0.15s ease;
}

.item-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-color: #d1d1d1;
}

.item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.item-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 5px 0;
}

.item-meta {
    font-size: 0.875rem;
    color: #666666;
    margin: 0 0 15px 0;
}

.item-details {
    margin-bottom: 20px;
}

.item-detail {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f5f5f5;
    font-size: 0.875rem;
}

.item-detail:last-child {
    border-bottom: none;
}

.item-detail-label {
    color: #666666;
    font-weight: 500;
}

.item-detail-value {
    color: #2c2c2c;
    font-weight: 400;
}

.item-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    border-radius: 1px;
    padding: 8px 16px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.15s ease;
    text-decoration: none;
    border: 1px solid;
    cursor: pointer;
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

.action-btn.btn-info {
    background: #ffffff;
    border-color: #1a6bcc;
    color: #1a6bcc;
}

.action-btn.btn-info:hover {
    background: #1a6bcc;
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
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid;
}

.status-consultation { 
    background: #eff8ff; 
    color: #1a6bcc; 
    border-color: #1a6bcc; 
}

.status-litigation { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-active { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-scheduled { 
    background: #eff8ff; 
    color: #1a6bcc; 
    border-color: #1a6bcc; 
}

.status-completed { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-pending { 
    background: #fff8e6; 
    color: #cc8c1a; 
    border-color: #cc8c1a; 
}

.empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #999999;
    background: #fafafa;
    border: 1px dashed #d1d1d1;
    border-radius: 2px;
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

.loading-state {
    text-align: center;
    padding: 60px 40px;
    color: #999999;
}

.loading-spinner {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #cccccc;
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

.form-control.is-invalid {
    border-color: #cc1a1a;
}

.invalid-feedback {
    display: block;
    margin-top: 5px;
    font-size: 0.75rem;
    color: #cc1a1a;
}

.help-text {
    font-size: 0.75rem;
    color: #999999;
    margin-top: 5px;
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
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .search-filters {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .tab-navigation {
        flex-direction: column;
    }
    
    .tab-btn {
        text-align: left;
    }
    
    .tab-content {
        padding: 20px;
    }
    
    .item-actions {
        justify-content: stretch;
    }
    
    .action-btn {
        flex: 1;
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
                    <h1>Cases Management</h1>
                    <div class="subtitle">Manage consultations and track litigation cases</div>
                </div>
                <div class="col-md-4">
                    <div class="page-actions text-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#consultationModal">
                            Add Consultation
                        </button>
                        <a href="<?php echo admin_url('cases/caseboard'); ?>" class="btn">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="consultations-count">-</div>
                <div class="stat-label">Consultations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="cases-count">-</div>
                <div class="stat-label">Active Cases</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="litigation-count">-</div>
                <div class="stat-label">Litigations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="upcoming-count">-</div>
                <div class="stat-label">Upcoming</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" data-tab="consultations">
                    Consultations <span id="consultations-badge">0</span>
                </button>
                <button class="tab-btn" data-tab="cases">
                    Cases <span id="cases-badge">0</span>
                </button>
            </div>

            <!-- Consultations Tab -->
            <div class="tab-content" id="consultations-tab">
                <!-- Search and Filters -->
                <div class="search-filters">
                    <div class="search-group">
                        <input type="text" class="search-input" id="consultations-search" 
                               placeholder="Search consultations by client, contact, or tag...">
                    </div>
                    <select class="filter-select" id="consultations-filter">
                        <option value="">All Status</option>
                        <option value="consultation">Consultation</option>
                        <option value="litigation">Litigation</option>
                    </select>
                    <button class="refresh-btn" id="refresh-consultations">
                        Refresh
                    </button>
                </div>

                <!-- Consultations Container -->
                <div id="consultations-container">
                    <div class="loading-state">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <p>Loading consultations...</p>
                    </div>
                </div>
            </div>

            <!-- Cases Tab -->
            <div class="tab-content" id="cases-tab" style="display: none;">
                <!-- Search and Filters -->
                <div class="search-filters">
                    <div class="search-group">
                        <input type="text" class="search-input" id="cases-search" 
                               placeholder="Search cases by title, number, client, or court...">
                    </div>
                    <select class="filter-select" id="cases-filter">
                        <option value="">All Courts</option>
                    </select>
                    <button class="refresh-btn" id="refresh-cases">
                        Refresh
                    </button>
                </div>

                <!-- Cases Container -->
                <div id="cases-container">
                    <div class="loading-state">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <p>Loading cases...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consultation Modal -->
<div class="modal fade" id="consultationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="consultationForm">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-text">Add Consultation</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="consultation_id" id="consultation_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Client <span style="color: #cc1a1a;">*</span>
                                </label>
                                <select name="client_id" id="client_id" class="form-control" required>
                                    <option value="">Select Client</option>
                                    <?php if (isset($clients) && !empty($clients)): ?>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo htmlspecialchars($client['userid']); ?>">
                                                <?php echo htmlspecialchars($client['company'] ?: 'Individual Client'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="form-group" id="contact-group" style="display: none;">
                                <label class="form-label">Contact Person</label>
                                <select name="contact_id" id="contact_id" class="form-control">
                                    <option value="">Select Contact (Optional)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tag</label>
                                <input type="text" name="tag" id="consultation_tag" class="form-control" 
                                       placeholder="e.g., Family Law, Property, Criminal">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Invoice <span style="color: #cc8c1a;">*</span>
                                    <small style="color: #999999;">(Recommended)</small>
                                </label>
                                <select name="invoice_id" id="invoice_id" class="form-control">
                                    <option value="">Select Invoice</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <div class="help-text">
                                    If no invoices are available, you can create one after saving.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Consultation Note <span style="color: #cc1a1a;">*</span>
                        </label>
                        <textarea name="note" id="consultation-note" class="form-control" rows="6" 
                                  placeholder="Enter detailed consultation notes..." required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="action-btn btn-primary">
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
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Consultation Note</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="noteContent" style="background: #fafafa; padding: 20px; border: 1px solid #e1e1e1; border-left: 3px solid #666666; line-height: 1.6; font-size: 0.875rem; color: #2c2c2c; border-radius: 2px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn btn-default" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="upgradeForm">
                <div class="modal-header">
                    <h4 class="modal-title">Register Litigation Case</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="litigation_consultation_id" id="litigation_consultation_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Case Title <span style="color: #cc1a1a;">*</span>
                                </label>
                                <input type="text" name="case_title" id="case_title" class="form-control" 
                                       placeholder="Enter descriptive case title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Case Number <span style="color: #cc1a1a;">*</span>
                                </label>
                                <input type="text" name="case_number" id="case_number" class="form-control" 
                                       placeholder="Court assigned case number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Court <span style="color: #cc1a1a;">*</span>
                                </label>
                                <select name="court_id" id="court_id_upgrade" class="form-control" required>
                                    <option value="">Select Court</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Court Room / Judge <span style="color: #cc1a1a;">*</span>
                                </label>
                                <select name="court_room_id" id="court_room_id_upgrade" class="form-control" required>
                                    <option value="">Select Court Room</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Date Filed <span style="color: #cc1a1a;">*</span>
                        </label>
                        <input type="date" name="date_filed" id="date_filed" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="action-btn btn-success">
                        Register Case
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
// Clean, minimal JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Variables
    let consultationsData = [];
    let casesData = [];
    let csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    let csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Update active states
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide content
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById(targetTab + '-tab').style.display = 'block';
        });
    });
    
    // Load initial data
    loadConsultations();
    loadCases();
    
    // Render consultations with minimalist cards
    function renderConsultations(data) {
        const container = document.getElementById('consultations-container');
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h5>No consultations found</h5>
                    <p>Start by adding your first consultation</p>
                    <button class="action-btn btn-primary" data-toggle="modal" data-target="#consultationModal">
                        Add Consultation
                    </button>
                </div>
            `;
            return;
        }
        
        let html = '<div class="items-grid">';
        
        data.forEach(consultation => {
            const statusClass = consultation.phase === 'litigation' ? 'status-litigation' : 'status-consultation';
            const statusText = consultation.phase === 'litigation' ? 'Litigation' : 'Consultation';
            
            html += `
                <div class="item-card">
                    <div class="item-header">
                        <div>
                            <h3 class="item-title">${htmlEscape(consultation.client_name || 'Unknown Client')}</h3>
                            <div class="item-meta">
                                <span class="status-badge ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="item-details">
                        ${consultation.contact_name ? `
                            <div class="item-detail">
                                <span class="item-detail-label">Contact:</span>
                                <span class="item-detail-value">${htmlEscape(consultation.contact_name)}</span>
                            </div>
                        ` : ''}
                        ${consultation.tag ? `
                            <div class="item-detail">
                                <span class="item-detail-label">Tag:</span>
                                <span class="item-detail-value">${htmlEscape(consultation.tag)}</span>
                            </div>
                        ` : ''}
                        <div class="item-detail">
                            <span class="item-detail-label">Date:</span>
                            <span class="item-detail-value">${formatDate(consultation.date_added)}</span>
                        </div>
                    </div>
                    
                    <div class="item-actions">
                        <button class="action-btn btn-default" onclick="viewNote(${consultation.id})">
                            View
                        </button>
                        <button class="action-btn btn-primary" onclick="editConsultation(${consultation.id})">
                            Edit
                        </button>
                        ${consultation.phase === 'consultation' ? `
                            <button class="action-btn btn-success" onclick="upgradeToLitigation(${consultation.id})">
                                Upgrade
                            </button>
                        ` : ''}
                        <button class="action-btn btn-danger" onclick="deleteConsultation(${consultation.id})">
                            Delete
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }
    
    // Render cases with minimalist cards
    function renderCases(data) {
        const container = document.getElementById('cases-container');
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-briefcase"></i>
                    <h5>No cases found</h5>
                    <p>Cases will appear here when consultations are upgraded</p>
                </div>
            `;
            return;
        }
        
        let html = '<div class="items-grid">';
        
        data.forEach(caseItem => {
            html += `
                <div class="item-card">
                    <div class="item-header">
                        <div>
                            <h3 class="item-title">${htmlEscape(caseItem.case_title)}</h3>
                            <div class="item-meta">
                                <span class="status-badge status-active">${htmlEscape(caseItem.case_number)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="item-details">
                        <div class="item-detail">
                            <span class="item-detail-label">Client:</span>
                            <span class="item-detail-value">${htmlEscape(caseItem.client_name || 'Unknown Client')}</span>
                        </div>
                        <div class="item-detail">
                            <span class="item-detail-label">Court:</span>
                            <span class="item-detail-value">${htmlEscape(caseItem.court_display || 'Court not specified')}</span>
                        </div>
                        <div class="item-detail">
                            <span class="item-detail-label">Filed:</span>
                            <span class="item-detail-value">${formatDate(caseItem.date_filed)}</span>
                        </div>
                    </div>
                    
                    <div class="item-actions">
                        <a href="${admin_url}cases/details?id=${encodeURIComponent(caseItem.id)}" class="action-btn btn-primary">
                            Details
                        </a>
                        <a href="${admin_url}cases/hearings/add?case_id=${encodeURIComponent(caseItem.id)}" class="action-btn btn-success">
                            Add Hearing
                        </a>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }
    
    // Load data functions
    function loadConsultations() {
        showLoading('consultations-container');
        
        fetch(admin_url + 'cases/consultations_list')
            .then(response => response.json())
            .then(data => {
                consultationsData = data.data || [];
                renderConsultations(consultationsData);
                updateStats();
            })
            .catch(error => {
                console.error('Error:', error);
                showError('consultations-container', 'Failed to load consultations');
            });
    }
    
    function loadCases() {
        showLoading('cases-container');
        
        fetch(admin_url + 'cases/cases_list')
            .then(response => response.json())
            .then(data => {
                casesData = data.data || [];
                renderCases(casesData);
                updateStats();
            })
            .catch(error => {
                console.error('Error:', error);
                showError('cases-container', 'Failed to load cases');
            });
    }
    
    // Update stats
    function updateStats() {
        const consultationsCount = consultationsData.length;
        const casesCount = casesData.length;
        let litigationCount = 0;
        
        consultationsData.forEach(c => {
            if (c.phase === 'litigation') litigationCount++;
        });
        
        document.getElementById('consultations-count').textContent = consultationsCount;
        document.getElementById('cases-count').textContent = casesCount;
        document.getElementById('litigation-count').textContent = litigationCount;
        document.getElementById('consultations-badge').textContent = consultationsCount;
        document.getElementById('cases-badge').textContent = casesCount;
        document.getElementById('upcoming-count').textContent = '0';
    }
    
    // Utility functions
    function showLoading(containerId) {
        document.getElementById(containerId).innerHTML = `
            <div class="loading-state">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p>Loading...</p>
            </div>
        `;
    }
    
    function showError(containerId, message) {
        document.getElementById(containerId).innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h5>Error</h5>
                <p>${htmlEscape(message)}</p>
                <button class="action-btn btn-primary" onclick="location.reload()">
                    Retry
                </button>
            </div>
        `;
    }
    
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
    
    // Global functions for onclick handlers
    window.viewNote = function(id) {
        const consultation = consultationsData.find(c => c.id == id);
        if (!consultation) return;
        
        document.getElementById('noteContent').innerHTML = consultation.note || 'No note available';
        $('#viewNoteModal').modal('show');
    };
    
    window.editConsultation = function(id) {
        // Implementation for edit
        const consultation = consultationsData.find(c => c.id == id);
        if (!consultation) return;
        
        document.getElementById('consultation_id').value = consultation.id;
        document.getElementById('client_id').value = consultation.client_id;
        document.getElementById('consultation_tag').value = consultation.tag || '';
        document.getElementById('consultation-note').value = consultation.note || '';
        document.getElementById('modal-title-text').textContent = 'Edit Consultation';
        document.getElementById('submit-btn-text').textContent = 'Update Consultation';
        
        $('#consultationModal').modal('show');
    };
    
    window.upgradeToLitigation = function(id) {
        document.getElementById('litigation_consultation_id').value = id;
        
        // Load courts
        fetch(admin_url + 'cases/courts/get_all_courts')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const select = document.getElementById('court_id_upgrade');
                    select.innerHTML = '<option value="">Select Court</option>';
                    data.data.forEach(court => {
                        select.innerHTML += `<option value="${court.id}">${htmlEscape(court.name)}</option>`;
                    });
                }
            });
        
        $('#upgradeModal').modal('show');
    };
    
    window.deleteConsultation = function(id) {
        if (!confirm('Are you sure you want to delete this consultation?')) return;
        
        const formData = new FormData();
        formData.append(csrfTokenName, csrfTokenHash);
        
        fetch(admin_url + 'cases/delete_consultation/' + id, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadConsultations();
                alert('Consultation deleted successfully');
            } else {
                alert('Failed to delete consultation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error occurred');
        });
    };
    
    // Form submissions
    document.getElementById('consultationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append(csrfTokenName, csrfTokenHash);
        
        fetch(admin_url + 'cases/create_consultation', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#consultationModal').modal('hide');
                loadConsultations();
                alert('Consultation saved successfully');
                this.reset();
            } else {
                alert('Failed to save consultation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error occurred');
        });
    });
    
    document.getElementById('upgradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append(csrfTokenName, csrfTokenHash);
        
        fetch(admin_url + 'cases/upgrade_to_litigation', {
            method: 'POST',
            body: formData
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
                alert('Failed to register case');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error occurred');
        });
    });
    
    // Client change handler
    document.getElementById('client_id').addEventListener('change', function() {
        const clientId = this.value;
        if (clientId) {
            loadContactsByClient(clientId);
            loadInvoicesByClient(clientId);
        } else {
            document.getElementById('contact-group').style.display = 'none';
            document.getElementById('contact_id').innerHTML = '<option value="">Select Contact (Optional)</option>';
            document.getElementById('invoice_id').innerHTML = '<option value="">Select Invoice</option>';
        }
    });
    
    // Court change handler
    document.getElementById('court_id_upgrade').addEventListener('change', function() {
        const courtId = this.value;
        if (courtId) {
            fetch(admin_url + 'cases/courts/get_rooms_by_court/' + courtId)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('court_room_id_upgrade');
                    select.innerHTML = '<option value="">Select Court Room</option>';
                    if (data.success && data.data) {
                        data.data.forEach(room => {
                            select.innerHTML += `<option value="${room.id}">Court ${room.court_no} - ${htmlEscape(room.judge_name)}</option>`;
                        });
                    }
                });
        }
    });
    
    function loadContactsByClient(clientId) {
        fetch(admin_url + 'cases/get_contacts_by_client/' + clientId)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('contact_id');
                select.innerHTML = '<option value="">Select Contact (Optional)</option>';
                
                if (data.success && data.data && data.data.length > 0) {
                    data.data.forEach(contact => {
                        const contactName = contact.full_name || (contact.firstname + ' ' + contact.lastname).trim();
                        select.innerHTML += `<option value="${contact.id}">${htmlEscape(contactName)}</option>`;
                    });
                    document.getElementById('contact-group').style.display = 'block';
                } else {
                    select.innerHTML += '<option value="">No contacts available</option>';
                    document.getElementById('contact-group').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading contacts:', error);
                document.getElementById('contact_id').innerHTML = '<option value="">Error loading contacts</option>';
                document.getElementById('contact-group').style.display = 'block';
            });
    }
    
    function loadInvoicesByClient(clientId) {
        const formData = new FormData();
        formData.append(csrfTokenName, csrfTokenHash);
        
        fetch(admin_url + 'cases/get_invoices_by_client/' + clientId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('invoice_id');
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
            document.getElementById('invoice_id').innerHTML = '<option value="">Error loading invoices</option>';
        });
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
    
    // Search functionality
    document.getElementById('consultations-search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filtered = consultationsData.filter(c => 
            (c.client_name || '').toLowerCase().includes(searchTerm) ||
            (c.contact_name || '').toLowerCase().includes(searchTerm) ||
            (c.tag || '').toLowerCase().includes(searchTerm)
        );
        renderConsultations(filtered);
    });
    
    document.getElementById('cases-search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filtered = casesData.filter(c => 
            (c.case_title || '').toLowerCase().includes(searchTerm) ||
            (c.case_number || '').toLowerCase().includes(searchTerm) ||
            (c.client_name || '').toLowerCase().includes(searchTerm) ||
            (c.court_display || '').toLowerCase().includes(searchTerm)
        );
        renderCases(filtered);
    });
    
    // Filter functionality
    document.getElementById('consultations-filter').addEventListener('change', function() {
        const filterValue = this.value;
        let filtered = consultationsData;
        
        if (filterValue) {
            filtered = consultationsData.filter(c => c.phase === filterValue);
        }
        
        renderConsultations(filtered);
    });
    
    // Refresh buttons
    document.getElementById('refresh-consultations').addEventListener('click', loadConsultations);
    document.getElementById('refresh-cases').addEventListener('click', loadCases);
});
</script>

<?php init_tail(); ?>