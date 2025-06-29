<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- Mobile-friendly styles -->
<style>
  /* Core UI improvements */
  .panel_s {
    border: 0;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
  }
  .panel_s > .panel-body {
    padding: 1.5rem;
  }
  
  /* Document loader */
  .document-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.95);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  .document-loader-spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #0d6efd;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
  }
  .document-loader-text {
    font-size: 18px;
    color: #333;
    font-weight: 500;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* Form elements */
  .form-group {
    margin-bottom: 20px;
  }
  .form-label {
    font-weight: 600;
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    color: #333;
  }
  .form-control {
    border-radius: 6px;
    padding: 10px 12px;
    border: 1px solid #dce0e6;
    width: 100%;
    transition: border-color 0.15s ease-in-out;
  }
  .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
  }
  
  /* File upload zone */
  .file-upload-zone {
    border: 2px dashed #dce0e6;
    border-radius: 6px;
    padding: 25px;
    text-align: center;
    margin-bottom: 20px;
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .file-upload-zone:hover {
    border-color: #0d6efd;
    background-color: #f1f7ff;
  }
  .file-upload-icon {
    font-size: 40px;
    color: #6c757d;
    margin-bottom: 10px;
  }
  .file-upload-text {
    color: #495057;
    font-size: 15px;
    font-weight: 500;
  }
  .file-upload-zone input[type="file"] {
    position: absolute;
    width: 0;
    height: 0;
    opacity: 0;
  }
  .file-name-display {
    margin-top: 10px;
    font-weight: 500;
    color: #0d6efd;
    display: none;
  }
  
  /* Radio options with icons */
  .doc-type-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
  }
  .doc-type-option {
    flex: 1 0 150px;
    padding: 15px;
    border: 1px solid #dce0e6;
    border-radius: 6px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .doc-type-option:hover {
    background-color: #f8f9fa;
  }
  .doc-type-option.active {
    border-color: #0d6efd;
    background-color: #f1f7ff;
  }
  .doc-type-icon {
    font-size: 24px;
    margin-bottom: 8px;
    color: #6c757d;
  }
  .doc-type-option.active .doc-type-icon {
    color: #0d6efd;
  }
  .doc-type-option input[type="radio"] {
    position: absolute;
    width: 0;
    height: 0;
    opacity: 0;
  }
  .doc-type-label {
    display: block;
    font-weight: 500;
    font-size: 14px;
  }
  
  /* Submit button */
  .btn-submit {
    background-color: #0d6efd;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    width: 100%;
  }
  .btn-submit:hover {
    background-color: #0b5ed7;
  }
  
  /* Responsive adjustments */
  @media (min-width: 768px) {
    .btn-submit {
      width: auto;
    }
    .doc-type-option {
      flex: 0 0 140px;
    }
  }
</style>

<!-- Loader overlay -->
<div id="document-loader" class="document-loader" style="display: none;">
  <div class="document-loader-spinner"></div>
  <div class="document-loader-text">Loading document details...</div>
</div>

<div id="wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="panel_s">
            <div class="panel-body">
              <!-- Page header and tabs -->
              <div class="page-title-actions mb-4">
                <ul class="nav nav-tabs border-0">
                  <li class="nav-item">
                    <a class="nav-link <?php echo $this->uri->segment(3) == 'upload' ? 'active' : ''; ?>" href="<?php echo admin_url('cases/documents/upload'); ?>">
                      <i class="fa fa-upload mr-1"></i> <?php echo _l('upload_document'); ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php echo $this->uri->segment(3) == 'search' ? 'active' : ''; ?>" href="<?php echo admin_url('cases/documents/search'); ?>">
                      <i class="fa fa-search mr-1"></i> <?php echo _l('search_documents'); ?>
                    </a>
                  </li>
                </ul>
              </div>

              <!-- Form start -->
              <?php echo form_open_multipart(admin_url('cases/documents/upload'), ['id' => 'document-upload-form']); ?>
                
                <!-- Step 1: File Upload -->
                <div class="file-upload-zone" id="file-drop-zone">
                  <div class="file-upload-icon">
                    <i class="fa fa-file-upload"></i>
                  </div>
                  <div class="file-upload-text">
                    <?php echo _l('drop_files_here_or_click_to_upload'); ?>
                  </div>
                  <input type="file" name="document" id="document" class="form-control">
                  <div class="file-name-display" id="file-name-display"></div>
                </div>
                
                <!-- Step 2: Customer Selection -->
                <div class="form-group">
                  <label class="form-label"><?php echo _l('select_customer'); ?></label>
                  <select name="customer_id" id="customer_id" class="form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_customer'); ?></option>
                    <?php foreach($customers as $customer) { ?>
                      <option value="<?php echo htmlspecialchars($customer->userid); ?>">
                        <?php echo htmlspecialchars($customer->company); ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                
                <!-- Step 3: Document Type Selection -->
                <div class="form-group">
                  <label class="form-label"><?php echo _l('document_belongs_to'); ?></label>
                  <div class="doc-type-options">
                    <div class="doc-type-option" data-type="invoice">
                      <div class="doc-type-icon"><i class="fa fa-file-invoice"></i></div>
                      <label class="doc-type-label"><?php echo _l('invoice'); ?></label>
                      <input type="radio" name="doc_owner_type" value="invoice" checked>
                    </div>
                    <div class="doc-type-option" data-type="customer">
                      <div class="doc-type-icon"><i class="fa fa-building"></i></div>
                      <label class="doc-type-label"><?php echo _l('customer'); ?></label>
                      <input type="radio" name="doc_owner_type" value="customer">
                    </div>
                    <div class="doc-type-option" data-type="contact">
                      <div class="doc-type-icon"><i class="fa fa-user"></i></div>
                      <label class="doc-type-label"><?php echo _l('contact'); ?></label>
                      <input type="radio" name="doc_owner_type" value="contact">
                    </div>
                    <div class="doc-type-option" data-type="consultation">
                      <div class="doc-type-icon"><i class="fa fa-comments"></i></div>
                      <label class="doc-type-label"><?php echo _l('consultation'); ?></label>
                      <input type="radio" name="doc_owner_type" value="consultation">
                    </div>
                    <div class="doc-type-option" data-type="case">
                      <div class="doc-type-icon"><i class="fa fa-briefcase"></i></div>
                      <label class="doc-type-label"><?php echo _l('case'); ?></label>
                      <input type="radio" name="doc_owner_type" value="case">
                    </div>
                    <div class="doc-type-option" data-type="hearing">
                      <div class="doc-type-icon"><i class="fa fa-gavel"></i></div>
                      <label class="doc-type-label"><?php echo _l('hearing'); ?></label>
                      <input type="radio" name="doc_owner_type" value="hearing">
                    </div>
                  </div>
                </div>
                
                <!-- Step 4: Related Entity Selection -->
                <!-- Invoice -->
                <div class="form-group entity-select" id="invoice_div">
                  <label class="form-label"><?php echo _l('select_invoice'); ?></label>
                  <select name="invoice_id" id="invoice_id" class="form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_invoice'); ?></option>
                  </select>
                </div>

                <!-- Contact -->
                <div class="form-group entity-select" id="contact_div" style="display:none;">
                  <label class="form-label"><?php echo _l('select_contact'); ?></label>
                  <select name="contact_id" id="contact_id" class="form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_contact'); ?></option>
                  </select>
                </div>

                <!-- Consultation -->
                <div class="form-group entity-select" id="consultation_div" style="display:none;">
                  <label class="form-label"><?php echo _l('select_consultation'); ?></label>
                  <select name="consultation_id" id="consultation_id" class="form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_consultation'); ?></option>
                  </select>
                </div>

                <!-- Case -->
                <div class="form-group entity-select" id="case_div" style="display:none;">
                  <label class="form-label"><?php echo _l('select_case'); ?></label>
                  <select name="case_id" id="case_id" class="form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_case'); ?></option>
                  </select>
                </div>

                <!-- Hearing -->
                <div class="form-group entity-select" id="hearing_div" style="display:none;">
                  <label class="form-label"><?php echo _l('select_hearing'); ?></label>
                  <select name="hearing_id" id="hearing_id" class="form-control selectpicker" data-live-search="true">
                    <option value=""><?php echo _l('select_hearing'); ?></option>
                  </select>
                </div>
                
                <!-- Step 5: Document Tag -->
                <div class="form-group">
                  <label class="form-label"><?php echo _l('document_tag'); ?></label>
                  <input type="text" name="document_tag" id="document_tag" class="form-control" placeholder="<?php echo _l('enter_document_tag'); ?>">
                </div>

                <!-- Submit Button -->
                <div class="form-group text-center text-md-left">
                  <button type="submit" class="btn-submit">
                    <i class="fa fa-upload mr-2"></i> <?php echo _l('upload'); ?>
                  </button>
                </div>
              <?php echo form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced JavaScript -->
<script>
(function() {
  // CSRF tokens from backend (CodeIgniter)
  const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
  const admin_url = "<?php echo admin_url(); ?>";
  
  // Elements
  const fileInput = document.getElementById('document');
  const fileDropZone = document.getElementById('file-drop-zone');
  const fileNameDisplay = document.getElementById('file-name-display');
  const docTypeOptions = document.querySelectorAll('.doc-type-option');
  
  // File upload handling
  fileDropZone.addEventListener('click', function() {
    fileInput.click();
  });
  
  fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      fileNameDisplay.textContent = this.files[0].name;
      fileNameDisplay.style.display = 'block';
      fileDropZone.style.borderColor = '#0d6efd';
    }
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
    fileDropZone.addEventListener(eventName, highlight, false);
  });
  
  ['dragleave', 'drop'].forEach(eventName => {
    fileDropZone.addEventListener(eventName, unhighlight, false);
  });
  
  function highlight() {
    fileDropZone.style.borderColor = '#0d6efd';
    fileDropZone.style.backgroundColor = '#f1f7ff';
  }
  
  function unhighlight() {
    fileDropZone.style.borderColor = '#dce0e6';
    fileDropZone.style.backgroundColor = '#f8f9fa';
  }
  
  fileDropZone.addEventListener('drop', handleDrop, false);
  
  function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    
    if (files && files[0]) {
      fileNameDisplay.textContent = files[0].name;
      fileNameDisplay.style.display = 'block';
    }
  }
  
  // Document type selection handling
  docTypeOptions.forEach(option => {
    const radio = option.querySelector('input[type="radio"]');
    
    option.addEventListener('click', function() {
      // Update UI
      docTypeOptions.forEach(opt => opt.classList.remove('active'));
      this.classList.add('active');
      
      // Select the radio
      radio.checked = true;
      
      // Trigger change event
      const event = new Event('change');
      radio.dispatchEvent(event);
    });
    
    // Initialize active state
    if (radio.checked) {
      option.classList.add('active');
    }
  });
  
  // API calls
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
      document.getElementById(targetId).innerHTML = data;
      // Refresh selectpicker if it's initialized
      if ($.fn.selectpicker) {
        $('#' + targetId).selectpicker('refresh');
      }
    })
    .catch(() => {
      document.getElementById(targetId).innerHTML = `<option value="">${defaultOptionText}</option>`;
      if ($.fn.selectpicker) {
        $('#' + targetId).selectpicker('refresh');
      }
    });
  }

  function updateInvoiceDropdown(customerId) {
    if (customerId) {
      fetchAndUpdate(admin_url + 'documents/get_invoices_by_customer', {customer_id: customerId}, 'invoice_id', '<?php echo _l('select_invoice'); ?>');
    } else {
      document.getElementById('invoice_id').innerHTML = '<option value=""><?php echo _l('select_invoice'); ?></option>';
      if ($.fn.selectpicker) {
        $('#invoice_id').selectpicker('refresh');
      }
    }
  }
  function updateContactDropdown(customerId) {
    if (customerId) {
      fetchAndUpdate(admin_url + 'documents/get_contacts_by_customer', {customer_id: customerId}, 'contact_id', '<?php echo _l('select_contact'); ?>');
    } else {
      document.getElementById('contact_id').innerHTML = '<option value=""><?php echo _l('select_contact'); ?></option>';
      if ($.fn.selectpicker) {
        $('#contact_id').selectpicker('refresh');
      }
    }
  }
  function updateConsultationDropdown(customerId) {
    if (customerId) {
      fetchAndUpdate(admin_url + 'documents/get_consultations_by_client', {customer_id: customerId}, 'consultation_id', '<?php echo _l('select_consultation'); ?>');
    } else {
      document.getElementById('consultation_id').innerHTML = '<option value=""><?php echo _l('select_consultation'); ?></option>';
      if ($.fn.selectpicker) {
        $('#consultation_id').selectpicker('refresh');
      }
    }
  }
  function updateCaseDropdown(customerId) {
    if (customerId) {
      fetchAndUpdate(admin_url + 'documents/get_cases_by_client', {customer_id: customerId}, 'case_id', '<?php echo _l('select_case'); ?>');
    } else {
      document.getElementById('case_id').innerHTML = '<option value=""><?php echo _l('select_case'); ?></option>';
      if ($.fn.selectpicker) {
        $('#case_id').selectpicker('refresh');
      }
    }
  }
  function updateHearingDropdown(caseId) {
    if (caseId) {
      fetchAndUpdate(admin_url + 'documents/get_hearings_by_case', {case_id: caseId}, 'hearing_id', '<?php echo _l('select_hearing'); ?>');
    } else {
      document.getElementById('hearing_id').innerHTML = '<option value=""><?php echo _l('select_hearing'); ?></option>';
      if ($.fn.selectpicker) {
        $('#hearing_id').selectpicker('refresh');
      }
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const customerSelect = document.getElementById('customer_id');
    const docTypeRadios = document.getElementsByName('doc_owner_type');
    const caseSelect = document.getElementById('case_id');
    const entitySelects = document.querySelectorAll('.entity-select');

    customerSelect.addEventListener('change', function () {
      const customerId = this.value;
      const selectedType = document.querySelector('input[name="doc_owner_type"]:checked').value;

      if (selectedType === 'invoice') {
        updateInvoiceDropdown(customerId);
      } else if (selectedType === 'contact') {
        updateContactDropdown(customerId);
      } else if (selectedType === 'consultation') {
        updateConsultationDropdown(customerId);
      } else if (selectedType === 'case' || selectedType === 'hearing') {
        updateCaseDropdown(customerId);
      }
    });

    docTypeRadios.forEach(radio => {
      radio.addEventListener('change', function () {
        const selectedType = this.value;
        const customerId = customerSelect.value;

        // Hide all entity selection divs
        entitySelects.forEach(select => {
          select.style.display = 'none';
        });

        if (selectedType === 'invoice') {
          document.getElementById('invoice_div').style.display = 'block';
          updateInvoiceDropdown(customerId);
        } else if (selectedType === 'contact') {
          document.getElementById('contact_div').style.display = 'block';
          updateContactDropdown(customerId);
        } else if (selectedType === 'consultation') {
          document.getElementById('consultation_div').style.display = 'block';
          updateConsultationDropdown(customerId);
        } else if (selectedType === 'case') {
          document.getElementById('case_div').style.display = 'block';
          updateCaseDropdown(customerId);
        } else if (selectedType === 'hearing') {
          document.getElementById('case_div').style.display = 'block';
          document.getElementById('hearing_div').style.display = 'block';
          updateCaseDropdown(customerId);
        }
      });
    });

    if (caseSelect) {
      caseSelect.addEventListener('change', function () {
        if (document.querySelector('input[name="doc_owner_type"]:checked').value === 'hearing') {
          updateHearingDropdown(this.value);
        }
      });
    }
    
    // Initialize selectpicker for enhanced dropdowns
    if ($.fn.selectpicker) {
      $('.selectpicker').selectpicker();
    }
  });
  
  // Handle localStorage data
  document.addEventListener('DOMContentLoaded', function() {
    try {
      const uploadData = JSON.parse(localStorage.getItem('document_upload_data'));
      if (uploadData) {
        console.log('Found upload data:', uploadData);
        
        // Show the loader
        const loader = document.getElementById('document-loader');
        if (loader) {
          loader.style.display = 'flex';
        }
        
        // Clear the storage immediately to prevent reuse
        localStorage.removeItem('document_upload_data');
        
        // Pre-select customer
        if (uploadData.customer_id) {
          const customerSelect = document.getElementById('customer_id');
          if (customerSelect) {
            customerSelect.value = uploadData.customer_id;
            // Trigger change event to load dependent dropdowns
            customerSelect.dispatchEvent(new Event('change'));
            // Update selectpicker
            if ($.fn.selectpicker) {
              $(customerSelect).selectpicker('refresh');
            }
          }
        }
        
        // Pre-select document type after a short delay
        setTimeout(function() {
          // Pre-select document type
          if (uploadData.doc_type) {
            // Find the radio option
            const docTypeRadio = document.querySelector('input[name="doc_owner_type"][value="' + uploadData.doc_type + '"]');
            if (docTypeRadio) {
              docTypeRadio.checked = true;
              
              // Update the visual state
              docTypeOptions.forEach(opt => opt.classList.remove('active'));
              const activeOption = document.querySelector(`.doc-type-option[data-type="${uploadData.doc_type}"]`);
              if (activeOption) {
                activeOption.classList.add('active');
              }
              
              // Trigger change event to show relevant fields
              docTypeRadio.dispatchEvent(new Event('change'));
            }
          }
          
          // Wait for dropdowns to be populated
          setTimeout(function() {
            // Pre-select case
            if (uploadData.case_id) {
              const caseSelect = document.getElementById('case_id');
              if (caseSelect) {
                caseSelect.value = uploadData.case_id;
                caseSelect.dispatchEvent(new Event('change'));
                // Update selectpicker
                if ($.fn.selectpicker) {
                  $(caseSelect).selectpicker('refresh');
                }
              }
            }
            
            // Wait for hearing dropdown to populate
            setTimeout(function() {
              // Pre-select hearing
              if (uploadData.hearing_id) {
                const hearingSelect = document.getElementById('hearing_id');
                if (hearingSelect) {
                  hearingSelect.value = uploadData.hearing_id;
                  // Update selectpicker
                  if ($.fn.selectpicker) {
                    $(hearingSelect).selectpicker('refresh');
                  }
                }
              }
              
              // Hide the loader after everything is done
              setTimeout(function() {
                if (loader) {
                  loader.style.display = 'none';
                }
              }, 500);
            }, 1000);
          }, 1000);
        }, 500);
      }
    } catch (e) {
      console.error('Error processing upload data:', e);
      // Hide loader in case of error
      const loader = document.getElementById('document-loader');
      if (loader) {
        loader.style.display = 'none';
      }
    }
  });
})();
</script>

<?php init_tail(); ?>