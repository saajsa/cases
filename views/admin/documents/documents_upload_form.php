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

<script>
document.addEventListener('DOMContentLoaded', function() {
  // CSRF tokens and configuration
  const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
  const admin_url = "<?php echo admin_url(); ?>";
  
  // Wizard state management
  let currentStep = 1;
  let selectedFile = null;
  let selectedClient = null;
  let selectedDocType = null;
  let selectedEntity = null;
  
  // Initialize selectpicker
  if ($.fn.selectpicker) {
    $('.selectpicker').selectpicker();
  }
  
  // ==========================================
  // WIZARD NAVIGATION FUNCTIONS
  // ==========================================
  
  function showStep(stepNumber, direction = 'forward') {
    const previousStep = currentStep;
    
    // Update step indicators
    document.querySelectorAll('.cases-upload-step').forEach((step, index) => {
      if (index + 1 <= stepNumber) {
        step.classList.add('active');
      } else {
        step.classList.remove('active');
      }
    });
    
    // Animate content sections
    document.querySelectorAll('.cases-upload-content').forEach((content, index) => {
      const contentStep = index + 1;
      
      if (contentStep === stepNumber) {
        // Show new step
        content.style.display = 'block';
        setTimeout(() => {
          content.classList.add('active');
        }, 10);
      } else if (contentStep === previousStep) {
        // Hide previous step with animation
        content.classList.remove('active');
        if (direction === 'forward') {
          content.classList.add('slide-out-left');
        } else {
          content.classList.add('slide-out-right');
        }
        
        setTimeout(() => {
          content.style.display = 'none';
          content.classList.remove('slide-out-left', 'slide-out-right');
        }, 300);
      } else {
        // Hide other steps
        content.classList.remove('active');
        content.style.display = 'none';
      }
    });
    
    currentStep = stepNumber;
  }
  
  function enableNextButton(stepNumber) {
    const nextBtn = document.getElementById(`next-step-${stepNumber}`);
    if (nextBtn) {
      nextBtn.disabled = false;
    }
  }
  
  function disableNextButton(stepNumber) {
    const nextBtn = document.getElementById(`next-step-${stepNumber}`);
    if (nextBtn) {
      nextBtn.disabled = true;
    }
  }
  
  // ==========================================
  // STEP 1: FILE SELECTION
  // ==========================================
  
  const fileInput = document.getElementById('document');
  const fileDropZone = document.getElementById('file-drop-zone');
  const filePreview = document.getElementById('file-preview');
  
  // File upload handling
  fileDropZone.addEventListener('click', function() {
    fileInput.click();
  });
  
  fileInput.addEventListener('change', handleFileSelection);
  
  function handleFileSelection() {
    const file = fileInput.files[0];
    if (file) {
      selectedFile = file;
      showFilePreview(file);
      enableNextButton(1);
    }
  }
  
  function showFilePreview(file) {
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = formatFileSize(file.size);
    
    // Update file icon based on type
    const fileIcon = document.querySelector('.cases-file-icon i');
    if (file.type.includes('pdf')) {
      fileIcon.className = 'fas fa-file-pdf';
    } else if (file.type.includes('image')) {
      fileIcon.className = 'fas fa-file-image';
    } else if (file.type.includes('word')) {
      fileIcon.className = 'fas fa-file-word';
    } else {
      fileIcon.className = 'fas fa-file';
    }
    
    fileDropZone.style.display = 'none';
    filePreview.style.display = 'block';
  }
  
  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
  
  // Change file button
  document.getElementById('change-file').addEventListener('click', function() {
    filePreview.style.display = 'none';
    fileDropZone.style.display = 'block';
    selectedFile = null;
    fileInput.value = '';
    disableNextButton(1);
  });
  
  // Drag & drop functionality
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    fileDropZone.addEventListener(eventName, preventDefaults, false);
  });
  
  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }
  
  ['dragenter', 'dragover'].forEach(eventName => {
    fileDropZone.addEventListener(eventName, function() {
      fileDropZone.classList.add('cases-file-drag-over');
    }, false);
  });
  
  ['dragleave', 'drop'].forEach(eventName => {
    fileDropZone.addEventListener(eventName, function() {
      fileDropZone.classList.remove('cases-file-drag-over');
    }, false);
  });
  
  fileDropZone.addEventListener('drop', handleDrop, false);
  
  function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length > 0) {
      fileInput.files = files;
      handleFileSelection();
    }
  }
  
  // ==========================================
  // STEP 2: CLIENT SELECTION
  // ==========================================
  
  const customerSearch = document.getElementById('customer_search');
  const customerIdHidden = document.getElementById('customer_id');
  const searchResults = document.getElementById('search-results');
  const selectedClientDiv = document.getElementById('selected-client');
  let searchTimeout;
  let clients = <?php echo json_encode($customers); ?>;
  
  customerSearch.addEventListener('input', function() {
    const query = this.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
      hideSearchResults();
      return;
    }
    
    searchTimeout = setTimeout(() => {
      searchClients(query);
    }, 300);
  });
  
  customerSearch.addEventListener('blur', function() {
    // Delay hiding to allow clicking on results
    setTimeout(() => {
      hideSearchResults();
    }, 200);
  });
  
  function searchClients(query) {
    const results = clients.filter(client => 
      client.company.toLowerCase().includes(query.toLowerCase())
    );
    
    showSearchResults(results);
  }
  
  function showSearchResults(results) {
    searchResults.innerHTML = '';
    
    if (results.length === 0) {
      searchResults.innerHTML = '<div class="cases-search-no-results">No clients found</div>';
    } else {
      results.forEach(client => {
        const resultDiv = document.createElement('div');
        resultDiv.className = 'cases-search-result';
        resultDiv.innerHTML = `
          <div class="cases-search-result-name">${client.company}</div>
          <div class="cases-search-result-details">Client ID: ${client.userid}</div>
        `;
        
        resultDiv.addEventListener('click', function() {
          selectClient(client);
        });
        
        searchResults.appendChild(resultDiv);
      });
    }
    
    searchResults.style.display = 'block';
  }
  
  function hideSearchResults() {
    searchResults.style.display = 'none';
  }
  
  function selectClient(client) {
    selectedClient = {
      id: client.userid,
      name: client.company
    };
    
    customerSearch.value = client.company;
    customerIdHidden.value = client.userid;
    hideSearchResults();
    showSelectedClient(client.company);
    enableNextButton(2);
    
    // Load related data for step 3
    loadClientRelatedData(client.userid);
  }
  
  function showSelectedClient(clientName) {
    document.getElementById('client-name').textContent = clientName;
    document.getElementById('client-details').textContent = 'Client selected';
    customerSearch.style.display = 'none';
    selectedClientDiv.style.display = 'block';
  }
  
  function hideSelectedClient() {
    customerSearch.style.display = 'block';
    selectedClientDiv.style.display = 'none';
  }
  
  document.getElementById('change-client').addEventListener('click', function() {
    hideSelectedClient();
    customerSearch.value = '';
    customerIdHidden.value = '';
    selectedClient = null;
    disableNextButton(2);
  });
  
  // ==========================================
  // STEP 3: DOCUMENT ASSOCIATION
  // ==========================================
  
  const quickActions = document.querySelectorAll('.cases-quick-action');
  const documentTypes = document.getElementById('document-types');
  const relatedEntities = document.getElementById('related-entities');
  
  // Quick action handlers
  quickActions.forEach(action => {
    action.addEventListener('click', function() {
      const type = this.dataset.type;
      
      // Remove active state from all quick actions
      quickActions.forEach(qa => qa.classList.remove('active'));
      this.classList.add('active');
      
      if (type === 'general') {
        // General client document - no specific association needed
        selectedDocType = 'customer';
        const customerRadio = document.querySelector('input[name="doc_owner_type"][value="customer"]');
        if (customerRadio) customerRadio.checked = true;
        
        documentTypes.style.display = 'none';
        relatedEntities.style.display = 'none';
        enableNextButton(3);
        
      } else if (type === 'case') {
        // Show litigation-related options  
        selectedDocType = 'case';
        showDocumentTypeSelection(['case', 'hearing']);
        
      } else if (type === 'invoice') {
        // Show invoice-related options
        selectedDocType = 'invoice';
        showDocumentTypeSelection(['invoice', 'customer']);
        
      } else if (type === 'consultation') {
        // Direct consultation selection
        selectedDocType = 'consultation';
        const consultationRadio = document.querySelector('input[name="doc_owner_type"][value="consultation"]');
        if (consultationRadio) consultationRadio.checked = true;
        
        documentTypes.style.display = 'none';
        relatedEntities.style.display = 'block';
        document.getElementById('consultation_div').style.display = 'block';
        disableNextButton(3);
      }
    });
  });
  
  function showDocumentTypeSelection(allowedTypes) {
    documentTypes.style.display = 'block';
    
    // Hide all doc type options first
    document.querySelectorAll('.cases-doc-type-option').forEach(option => {
      option.style.display = 'none';
    });
    
    // Show only allowed types
    allowedTypes.forEach(type => {
      const option = document.querySelector(`.cases-doc-type-option[data-type="${type}"]`);
      if (option) {
        option.style.display = 'block';
      }
    });
  }
  
  // Document type selection handling
  document.querySelectorAll('.cases-doc-type-option').forEach(option => {
    const radio = option.querySelector('input[type="radio"]');
    
    option.addEventListener('click', function() {
      if (this.style.display === 'none') return; // Skip hidden options
      
      // Update UI
      document.querySelectorAll('.cases-doc-type-option').forEach(opt => opt.classList.remove('active'));
      this.classList.add('active');
      
      // Select the radio
      radio.checked = true;
      selectedDocType = radio.value;
      
      // Show related entity selection if needed
      showRelatedEntitySelection(selectedDocType);
    });
  });
  
  function showRelatedEntitySelection(docType) {
    // Hide all entity selects first
    document.querySelectorAll('.entity-select').forEach(select => {
      select.style.display = 'none';
    });
    
    if (docType === 'customer') {
      // No additional selection needed
      relatedEntities.style.display = 'none';
      enableNextButton(3);
    } else {
      relatedEntities.style.display = 'block';
      
      if (docType === 'invoice') {
        document.getElementById('invoice_div').style.display = 'block';
      } else if (docType === 'consultation') {
        document.getElementById('consultation_div').style.display = 'block';
      } else if (docType === 'case') {
        document.getElementById('case_div').style.display = 'block';
      } else if (docType === 'hearing') {
        document.getElementById('case_div').style.display = 'block';
        document.getElementById('hearing_div').style.display = 'block';
      }
      
      // Enable next button only after entity is selected
      disableNextButton(3);
    }
  }
  
  // Entity selection handlers
  ['invoice_id', 'consultation_id', 'case_id', 'hearing_id'].forEach(selectId => {
    const select = document.getElementById(selectId);
    if (select) {
      select.addEventListener('change', function() {
        if (this.value) {
          selectedEntity = {
            type: selectId.replace('_id', ''),
            id: this.value,
            name: this.options[this.selectedIndex].text
          };
          enableNextButton(3);
          
          // If case is selected and we need hearing, load hearings
          if (selectId === 'case_id' && selectedDocType === 'hearing') {
            updateHearingDropdown(this.value);
          }
        } else {
          selectedEntity = null;
          disableNextButton(3);
        }
      });
    }
  });
  
  // ==========================================
  // STEP 4: REVIEW AND UPLOAD
  // ==========================================
  
  function updateSummary() {
    // File details
    document.getElementById('summary-file').textContent = selectedFile ? selectedFile.name : '-';
    document.getElementById('summary-size').textContent = selectedFile ? formatFileSize(selectedFile.size) : '-';
    
    // Client info
    document.getElementById('summary-client').textContent = selectedClient ? selectedClient.name : '-';
    
    // Document type
    let typeText = '-';
    if (selectedDocType) {
      const typeLabels = {
        'customer': 'General Client Document',
        'invoice': 'Invoice Document',
        'contact': 'Contact Document',
        'consultation': 'Consultation Document',
        'case': 'Case Document',
        'hearing': 'Hearing Document'
      };
      typeText = typeLabels[selectedDocType] || selectedDocType;
    }
    document.getElementById('summary-type').textContent = typeText;
    
    // Related entity
    const relationItem = document.getElementById('summary-relation-item');
    if (selectedEntity) {
      document.getElementById('summary-relation').textContent = selectedEntity.name;
      relationItem.style.display = 'block';
    } else {
      relationItem.style.display = 'none';
    }
  }
  
  // ==========================================
  // NAVIGATION BUTTON HANDLERS
  // ==========================================
  
  // Next buttons
  document.getElementById('next-step-1').addEventListener('click', () => showStep(2, 'forward'));
  document.getElementById('next-step-2').addEventListener('click', () => showStep(3, 'forward'));
  document.getElementById('next-step-3').addEventListener('click', () => {
    updateSummary();
    showStep(4, 'forward');
  });
  
  // Previous buttons
  document.getElementById('prev-step-2').addEventListener('click', () => showStep(1, 'backward'));
  document.getElementById('prev-step-3').addEventListener('click', () => showStep(2, 'backward'));
  document.getElementById('prev-step-4').addEventListener('click', () => showStep(3, 'backward'));
  
  // ==========================================
  // API FUNCTIONS
  // ==========================================
  
  function loadClientRelatedData(customerId) {
    if (!customerId) return;
    
    // Load all related data for this client
    const requests = [
      { url: 'get_invoices_by_customer', targetId: 'invoice_id' },
      { url: 'get_contacts_by_customer', targetId: 'contact_id' },
      { url: 'get_consultations_by_client', targetId: 'consultation_id' },
      { url: 'get_cases_by_client', targetId: 'case_id' }
    ];
    
    requests.forEach(request => {
      fetchAndUpdate(
        admin_url + 'cases/documents/' + request.url,
        { customer_id: customerId },
        request.targetId,
        'Select option...'
      );
    });
  }
  
  function updateHearingDropdown(caseId) {
    if (caseId) {
      fetchAndUpdate(
        admin_url + 'cases/documents/get_hearings_by_case',
        { case_id: caseId },
        'hearing_id',
        '<?php echo _l('select_hearing'); ?>'
      );
    }
  }
  
  function fetchAndUpdate(url, params, targetId, defaultOptionText) {
    params[csrfName] = csrfHash;

    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(params)
    })
    .then(response => {
      const newToken = response.headers.get('X-CSRF-TOKEN');
      if (newToken) csrfHash = newToken;
      return response.text();
    })
    .then(data => {
      const select = document.getElementById(targetId);
      if (select) {
        select.innerHTML = data;
        if ($.fn.selectpicker) {
          $(select).selectpicker('refresh');
        }
      }
    })
    .catch(() => {
      const select = document.getElementById(targetId);
      if (select) {
        select.innerHTML = `<option value="">${defaultOptionText}</option>`;
        if ($.fn.selectpicker) {
          $(select).selectpicker('refresh');
        }
      }
    });
  }
  
  // ==========================================
  // FORM SUBMISSION
  // ==========================================
  
  document.getElementById('document-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('upload-document');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    submitBtn.disabled = true;
    
    // Submit the form
    const formData = new FormData(this);
    formData.append(csrfName, csrfHash);
    
    fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success message
        alert('Document uploaded successfully!');
        
        // Redirect or reset form
        window.location.href = admin_url + 'cases/documents';
      } else {
        alert('Upload failed: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Upload error:', error);
      alert('Network error occurred during upload');
    })
    .finally(() => {
      // Restore button
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  });
  
  // ==========================================
  // INITIALIZATION
  // ==========================================
  
  // Initialize first step
  showStep(1);
  
  // Handle pre-population from localStorage (if coming from other pages)
  try {
    const uploadData = JSON.parse(localStorage.getItem('document_upload_data'));
    if (uploadData) {
      localStorage.removeItem('document_upload_data');
      
      // Pre-populate data based on localStorage
      if (uploadData.customer_id) {
        setTimeout(() => {
          customerSelect.value = uploadData.customer_id;
          customerSelect.dispatchEvent(new Event('change'));
          if ($.fn.selectpicker) {
            $(customerSelect).selectpicker('refresh');
          }
          
          // Auto-advance to step 2 if we have client data
          if (uploadData.customer_id) {
            showStep(2);
          }
        }, 500);
      }
    }
  } catch (e) {
    console.error('Error processing upload data:', e);
  }
});
</script>

<?php init_tail(); ?>