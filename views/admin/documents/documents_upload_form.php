<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'forms', 'status', 'modals', 'tables', 'wizard']);
echo cases_page_wrapper_start(
    'Document Upload',
    'Upload and manage legal documents',
    [
        [
            'text' => 'Search Documents',
            'href' => admin_url('cases/documents/search'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-search'
        ],
        [
            'text' => 'Document Manager',
            'href' => admin_url('cases/documents'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-folder'
        ]
    ]
);
?>

<!-- Loader overlay -->
<div id="document-loader" class="cases-loading-overlay" style="display: none;">
    <div class="cases-loading-content">
        <div class="cases-loading-spinner"></div>
        <div class="cases-loading-text">Loading document details...</div>
    </div>
</div>

<!-- Upload Wizard Container -->
<div class="cases-upload-wizard">
    
    <!-- Progress Steps -->
    <div class="cases-upload-steps">
        <div class="cases-upload-step active" data-step="1">
            <div class="cases-step-number">1</div>
            <div class="cases-step-label">Select File</div>
        </div>
        <div class="cases-upload-step" data-step="2">
            <div class="cases-step-number">2</div>
            <div class="cases-step-label">Choose Client</div>
        </div>
        <div class="cases-upload-step" data-step="3">
            <div class="cases-step-number">3</div>
            <div class="cases-step-label">Link Document</div>
        </div>
        <div class="cases-upload-step" data-step="4">
            <div class="cases-step-number">4</div>
            <div class="cases-step-label">Review & Upload</div>
        </div>
    </div>

    <?php echo form_open_multipart(admin_url('cases/documents/upload'), ['id' => 'document-upload-form']); ?>

    <!-- Step 1: File Selection -->
    <div class="cases-upload-content active" data-step="1">
        <div class="cases-upload-step-header">
            <h3><i class="fas fa-file-upload"></i> Select Document to Upload</h3>
            <p>Choose the file you want to upload to the document management system</p>
        </div>

        <div class="cases-file-upload-zone" id="file-drop-zone">
            <div class="cases-file-upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="cases-file-upload-text">
                <strong>Drop your file here or click to browse</strong>
                <small>Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</small>
            </div>
            <input type="file" name="document" id="document" class="cases-file-input" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
            <div class="cases-file-preview" id="file-preview" style="display:none;">
                <div class="cases-file-info">
                    <div class="cases-file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="cases-file-details">
                        <div class="cases-file-name" id="file-name"></div>
                        <div class="cases-file-size" id="file-size"></div>
                        <div class="cases-file-actions">
                            <button type="button" class="cases-btn-link" id="change-file">
                                <i class="fas fa-edit"></i> Change File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cases-upload-actions">
            <button type="button" class="cases-btn cases-btn-primary" id="next-step-1" disabled>
                Next: Choose Client <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Client Selection -->
    <div class="cases-upload-content" data-step="2">
        <div class="cases-upload-step-header">
            <h3><i class="fas fa-users"></i> Select Client</h3>
            <p>Choose which client this document belongs to</p>
        </div>

        <div class="cases-form-group">
            <div class="cases-search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="customer_search" class="cases-form-control" placeholder="Start typing to search for a client..." autocomplete="off">
                <input type="hidden" name="customer_id" id="customer_id">
                <div class="cases-search-results" id="search-results" style="display: none;"></div>
            </div>
        </div>

        <div class="cases-selected-client" id="selected-client" style="display:none;">
            <div class="cases-client-card">
                <div class="cases-client-info">
                    <div class="cases-client-name" id="client-name"></div>
                    <div class="cases-client-details" id="client-details"></div>
                </div>
                <button type="button" class="cases-btn-link" id="change-client">
                    <i class="fas fa-edit"></i> Change
                </button>
            </div>
        </div>

        <div class="cases-upload-actions">
            <button type="button" class="cases-btn cases-btn-secondary" id="prev-step-2">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="button" class="cases-btn cases-btn-primary" id="next-step-2" disabled>
                Next: Link Document <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Document Association -->
    <div class="cases-upload-content" data-step="3">
        <div class="cases-upload-step-header">
            <h3><i class="fas fa-link"></i> Link Document</h3>
            <p>Connect this document to the relevant case, consultation, or invoice</p>
        </div>

        <!-- Document Linking Interface -->
        <div class="cases-document-linking">
            
            <!-- Connection Type Selection -->
            <div class="cases-connection-types" id="connection-types">
                <div class="cases-connection-grid">
                    
                    <!-- General Document Option -->
                    <div class="cases-connection-card" data-type="customer" data-category="general" role="button" tabindex="0">
                        <div class="cases-connection-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="cases-connection-content">
                            <h4 class="cases-connection-title">General Document</h4>
                            <p class="cases-connection-description">Client document not linked to specific matter</p>
                            <div class="cases-connection-badge">
                                <i class="fas fa-check"></i> Quick Save
                            </div>
                        </div>
                        <input type="radio" name="doc_owner_type" value="customer" class="cases-connection-radio">
                    </div>

                    <!-- Legal Matter Option -->
                    <div class="cases-connection-card" data-type="case" data-category="legal" role="button" tabindex="0">
                        <div class="cases-connection-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="cases-connection-content">
                            <h4 class="cases-connection-title">Legal Matter</h4>
                            <p class="cases-connection-description">Associate with case, hearing, or legal proceeding</p>
                            <div class="cases-connection-badge">
                                <i class="fas fa-layer-group"></i> Multi-level
                            </div>
                        </div>
                        <input type="radio" name="doc_owner_type" value="case" class="cases-connection-radio">
                    </div>

                    <!-- Business Document Option -->
                    <div class="cases-connection-card" data-type="invoice" data-category="business" role="button" tabindex="0">
                        <div class="cases-connection-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="cases-connection-content">
                            <h4 class="cases-connection-title">Business Document</h4>
                            <p class="cases-connection-description">Invoice, contract, or business correspondence</p>
                            <div class="cases-connection-badge">
                                <i class="fas fa-dollar-sign"></i> Financial
                            </div>
                        </div>
                        <input type="radio" name="doc_owner_type" value="invoice" class="cases-connection-radio">
                    </div>

                    <!-- Consultation Option -->
                    <div class="cases-connection-card" data-type="consultation" data-category="consultation" role="button" tabindex="0">
                        <div class="cases-connection-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="cases-connection-content">
                            <h4 class="cases-connection-title">Consultation</h4>
                            <p class="cases-connection-description">Initial meeting or consultation notes</p>
                            <div class="cases-connection-badge">
                                <i class="fas fa-comments"></i> Advisory
                            </div>
                        </div>
                        <input type="radio" name="doc_owner_type" value="consultation" class="cases-connection-radio">
                    </div>
                    
                </div>
            </div>

            <!-- Progressive Disclosure for Legal Matter -->
            <div class="cases-legal-matter-options" id="legal-matter-options" style="display: none;">
                <div class="cases-section-divider">
                    <span class="cases-divider-text">Select Legal Matter Type</span>
                </div>
                
                <div class="cases-legal-grid">
                    <div class="cases-legal-option" data-subtype="case">
                        <div class="cases-legal-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="cases-legal-content">
                            <h5>Case Document</h5>
                            <p>General case file or evidence</p>
                        </div>
                    </div>
                    
                    <div class="cases-legal-option" data-subtype="hearing">
                        <div class="cases-legal-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="cases-legal-content">
                            <h5>Hearing Document</h5>
                            <p>Specific to court hearing</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entity Selection Interface -->
            <div class="cases-entity-selection" id="entity-selection" style="display: none;">
                <div class="cases-section-divider">
                    <span class="cases-divider-text">Choose Specific Item</span>
                </div>

                <!-- Search and Filter Controls -->
                <div class="cases-search-controls">
                    <div class="cases-search-input-wrapper">
                        <i class="fas fa-search cases-search-icon"></i>
                        <input type="text" id="entity-search" class="cases-search-input" placeholder="Search...">
                    </div>
                    <div class="cases-filter-controls">
                        <button type="button" class="cases-filter-btn" id="recent-filter">
                            <i class="fas fa-clock"></i> Recent
                        </button>
                        <button type="button" class="cases-filter-btn" id="active-filter">
                            <i class="fas fa-bolt"></i> Active
                        </button>
                    </div>
                </div>

                <!-- Entity List Container -->
                <div class="cases-entity-list-container">
                    <div class="cases-entity-list" id="entity-list">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                    
                    <!-- Loading State -->
                    <div class="cases-loading-state" id="entity-loading" style="display: none;">
                        <div class="cases-loading-spinner"></div>
                        <p>Loading items...</p>
                    </div>
                    
                    <!-- Empty State -->
                    <div class="cases-empty-state" id="entity-empty" style="display: none;">
                        <i class="fas fa-inbox"></i>
                        <h5>No items found</h5>
                        <p>Try adjusting your search or create a new item</p>
                    </div>
                </div>

                <!-- Hidden form inputs for SelectPicker compatibility -->
                <div style="display: none;">
                    <select name="invoice_id" id="invoice_id" class="cases-form-control selectpicker" data-live-search="true">
                        <option value="">Select Invoice</option>
                    </select>
                    <select name="consultation_id" id="consultation_id" class="cases-form-control selectpicker" data-live-search="true">
                        <option value="">Select Consultation</option>
                    </select>
                    <select name="case_id" id="case_id" class="cases-form-control selectpicker" data-live-search="true">
                        <option value="">Select Case</option>
                    </select>
                    <select name="hearing_id" id="hearing_id" class="cases-form-control selectpicker" data-live-search="true">
                        <option value="">Select Hearing</option>
                    </select>
                </div>
            </div>

            <!-- Selection Summary -->
            <div class="cases-selection-summary" id="selection-summary" style="display: none;">
                <div class="cases-summary-card">
                    <div class="cases-summary-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="cases-summary-content">
                        <h5>Document will be linked to:</h5>
                        <p id="summary-text">No selection made</p>
                    </div>
                    <button type="button" class="cases-summary-change" id="change-selection">
                        <i class="fas fa-edit"></i> Change
                    </button>
                </div>
            </div>

        </div>

        <div class="cases-upload-actions">
            <button type="button" class="cases-btn cases-btn-secondary" id="prev-step-3">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="button" class="cases-btn cases-btn-primary" id="next-step-3" disabled>
                Next: Review & Upload <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 4: Review and Upload -->
    <div class="cases-upload-content" data-step="4">
        <div class="cases-upload-step-header">
            <h3><i class="fas fa-check-circle"></i> Review & Upload</h3>
            <p>Please review the information below and upload your document</p>
        </div>

        <div class="cases-upload-summary">
            <div class="cases-summary-section">
                <h4><i class="fas fa-file"></i> Document Details</h4>
                <div class="cases-summary-item">
                    <label>File:</label>
                    <span id="summary-file">-</span>
                </div>
                <div class="cases-summary-item">
                    <label>Size:</label>
                    <span id="summary-size">-</span>
                </div>
            </div>

            <div class="cases-summary-section">
                <h4><i class="fas fa-user"></i> Client Information</h4>
                <div class="cases-summary-item">
                    <label>Client:</label>
                    <span id="summary-client">-</span>
                </div>
            </div>

            <div class="cases-summary-section">
                <h4><i class="fas fa-link"></i> Document Association</h4>
                <div class="cases-summary-item">
                    <label>Type:</label>
                    <span id="summary-type">-</span>
                </div>
                <div class="cases-summary-item" id="summary-relation-item" style="display:none;">
                    <label>Related to:</label>
                    <span id="summary-relation">-</span>
                </div>
            </div>

            <div class="cases-summary-section">
                <h4><i class="fas fa-tag"></i> Additional Information</h4>
                <div class="cases-form-group">
                    <label class="cases-form-label">Document Tag (Optional)</label>
                    <input type="text" name="document_tag" id="document_tag" class="cases-form-control" placeholder="Add a tag to help categorize this document...">
                    <small class="cases-form-help">Examples: "Contract", "Evidence", "Correspondence", etc.</small>
                </div>
            </div>
        </div>

        <div class="cases-upload-actions">
            <button type="button" class="cases-btn cases-btn-secondary" id="prev-step-4">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="submit" class="cases-btn cases-btn-success" id="upload-document">
                <i class="fas fa-cloud-upload-alt"></i> Upload Document
            </button>
        </div>
    </div>

    <?php echo form_close(); ?>
</div>

<?php echo cases_section_end(); ?>
<?php echo cases_page_wrapper_end(); ?>

<!-- Additional CSS for connection cards -->
<style>
.cases-connection-card {
    cursor: pointer !important;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.cases-connection-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.cases-connection-card.active {
    border-color: #28a745;
    background-color: #f8f9fa;
}

.cases-connection-card:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

.cases-connection-radio {
    pointer-events: none;
}

/* Hearing selection styles */
.cases-hearing-case-item {
    border-left: 3px solid #2196f3;
}

.cases-hearing-case-item:hover {
    background-color: #f3f9ff;
}

.cases-entity-arrow {
    color: #666;
    margin-left: auto;
}

.cases-fallback-option {
    border-left: 3px solid #ff9800;
    background-color: #fff8e1;
}

.cases-btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 3px;
    border: 1px solid #ddd;
    background: white;
    color: #666;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.cases-btn-sm:hover {
    background: #f5f5f5;
    border-color: #ccc;
}
</style>

<!-- Load the modular document upload wizard -->
<script src="<?php echo base_url('modules/cases/assets/js/document-upload-wizard.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing wizard...');
    
    // Initialize the document upload wizard
    const wizard = new DocumentUploadWizard({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        adminUrl: '<?php echo admin_url(); ?>',
        clients: <?php echo json_encode($customers); ?>
    });
    
    console.log('Wizard initialized:', wizard);
});
</script>

<?php init_tail(); ?>