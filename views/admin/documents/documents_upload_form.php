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
            <p>Choose what this document relates to</p>
        </div>

        <!-- Quick Actions for Common Scenarios -->
        <div class="cases-quick-actions">
            <button type="button" class="cases-quick-action" data-type="general">
                <i class="fas fa-folder"></i>
                <span>General Client Document</span>
                <small>Not related to any specific case or invoice</small>
            </button>
            <button type="button" class="cases-quick-action" data-type="case">
                <i class="fas fa-gavel"></i>
                <span>Litigation</span>
                <small>Associate with legal case or hearing</small>
            </button>
            <button type="button" class="cases-quick-action" data-type="invoice">
                <i class="fas fa-file-invoice"></i>
                <span>Link to Invoice</span>
                <small>Billing or payment related document</small>
            </button>
            <button type="button" class="cases-quick-action" data-type="consultation">
                <i class="fas fa-comments"></i>
                <span>Consultation</span>
                <small>Client consultation document</small>
            </button>
        </div>

        <!-- Document Type Selection (Hidden initially) -->
        <div class="cases-document-types" id="document-types" style="display:none;">
            <label class="cases-form-label">What type of document is this?</label>
            <div class="cases-doc-type-grid">
                <div class="cases-doc-type-option" data-type="invoice">
                    <div class="cases-doc-type-icon"><i class="fas fa-file-invoice"></i></div>
                    <label class="cases-doc-type-label"><?php echo _l('invoice'); ?></label>
                    <input type="radio" name="doc_owner_type" value="invoice">
                </div>
                <div class="cases-doc-type-option" data-type="customer">
                    <div class="cases-doc-type-icon"><i class="fas fa-building"></i></div>
                    <label class="cases-doc-type-label"><?php echo _l('customer'); ?></label>
                    <input type="radio" name="doc_owner_type" value="customer">
                </div>
                <div class="cases-doc-type-option" data-type="consultation">
                    <div class="cases-doc-type-icon"><i class="fas fa-comments"></i></div>
                    <label class="cases-doc-type-label"><?php echo _l('consultation'); ?></label>
                    <input type="radio" name="doc_owner_type" value="consultation">
                </div>
                <div class="cases-doc-type-option" data-type="case">
                    <div class="cases-doc-type-icon"><i class="fas fa-briefcase"></i></div>
                    <label class="cases-doc-type-label"><?php echo _l('case'); ?></label>
                    <input type="radio" name="doc_owner_type" value="case">
                </div>
                <div class="cases-doc-type-option" data-type="hearing">
                    <div class="cases-doc-type-icon"><i class="fas fa-gavel"></i></div>
                    <label class="cases-doc-type-label"><?php echo _l('hearing'); ?></label>
                    <input type="radio" name="doc_owner_type" value="hearing">
                </div>
            </div>
        </div>

        <!-- Related Entity Selection -->
        <div class="cases-related-entities" id="related-entities" style="display:none;">
            <!-- Invoice -->
            <div class="cases-form-group entity-select" id="invoice_div" style="display:none;">
                <label class="cases-form-label"><?php echo _l('select_invoice'); ?></label>
                <select name="invoice_id" id="invoice_id" class="cases-form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_invoice'); ?></option>
                </select>
            </div>

            <!-- Consultation -->
            <div class="cases-form-group entity-select" id="consultation_div" style="display:none;">
                <label class="cases-form-label"><?php echo _l('select_consultation'); ?></label>
                <select name="consultation_id" id="consultation_id" class="cases-form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_consultation'); ?></option>
                </select>
            </div>

            <!-- Case -->
            <div class="cases-form-group entity-select" id="case_div" style="display:none;">
                <label class="cases-form-label"><?php echo _l('select_case'); ?></label>
                <select name="case_id" id="case_id" class="cases-form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_case'); ?></option>
                </select>
            </div>

            <!-- Hearing -->
            <div class="cases-form-group entity-select" id="hearing_div" style="display:none;">
                <label class="cases-form-label"><?php echo _l('select_hearing'); ?></label>
                <select name="hearing_id" id="hearing_id" class="cases-form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_hearing'); ?></option>
                </select>
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

<!-- Load the modular document upload wizard -->
<script src="<?php echo base_url('modules/cases/assets/js/document-upload-wizard.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the document upload wizard
    const wizard = new DocumentUploadWizard({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        adminUrl: '<?php echo admin_url(); ?>',
        clients: <?php echo json_encode($customers); ?>
    });
});
</script>

<?php init_tail(); ?>