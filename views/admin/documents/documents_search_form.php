<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'forms', 'status', 'modals', 'tables', 'wizard']);
echo cases_page_wrapper_start(
    'Document Search',
    'Search and filter legal documents',
    [
        [
            'text' => 'Upload Document',
            'href' => admin_url('cases/documents/upload'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-upload'
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

<!-- Search Container with Enhanced Styling -->
<div class="cases-upload-wizard cases-search-container">
    
    <!-- Search Header -->
    <div class="cases-upload-step-header">
        <h3><i class="fas fa-search"></i> Document Search</h3>
        <p>Find documents by type, client, or specific criteria</p>
    </div>

    <?php echo form_open(admin_url('cases/documents/search'), ['id' => 'document-search-form']); ?>

    <!-- Document Type Filter Section -->
    <div class="cases-form-group">
        <label class="cases-form-label"><i class="fas fa-filter"></i> <?php echo _l('filter_by_type'); ?></label>
        <div class="cases-doc-type-grid">
        <div class="cases-doc-type-option active" data-type="all">
            <div class="cases-doc-type-icon"><i class="fas fa-globe"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('all'); ?></label>
            <input type="radio" name="search_type" value="all" checked>
        </div>
        <div class="cases-doc-type-option" data-type="customer">
            <div class="cases-doc-type-icon"><i class="fas fa-building"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('customer'); ?></label>
            <input type="radio" name="search_type" value="customer">
        </div>
        <div class="cases-doc-type-option" data-type="invoice">
            <div class="cases-doc-type-icon"><i class="fas fa-file-invoice"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('invoice'); ?></label>
            <input type="radio" name="search_type" value="invoice">
        </div>
        <div class="cases-doc-type-option" data-type="contact">
            <div class="cases-doc-type-icon"><i class="fas fa-user"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('contact'); ?></label>
            <input type="radio" name="search_type" value="contact">
        </div>
        <div class="cases-doc-type-option" data-type="consultation">
            <div class="cases-doc-type-icon"><i class="fas fa-comments"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('consultation'); ?></label>
            <input type="radio" name="search_type" value="consultation">
        </div>
        <div class="cases-doc-type-option" data-type="case">
            <div class="cases-doc-type-icon"><i class="fas fa-briefcase"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('case'); ?></label>
            <input type="radio" name="search_type" value="case">
        </div>
        <div class="cases-doc-type-option" data-type="hearing">
            <div class="cases-doc-type-icon"><i class="fas fa-gavel"></i></div>
            <label class="cases-doc-type-label"><?php echo _l('hearing'); ?></label>
            <input type="radio" name="search_type" value="hearing">
        </div>
    </div>
</div>

    <!-- Customer Selection with Enhanced Search -->
    <div class="cases-form-group">
        <label class="cases-form-label"><i class="fas fa-building"></i> <?php echo _l('select_customer'); ?></label>
        <div class="cases-search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="customer_search" class="cases-form-control" placeholder="Start typing to search for a client..." autocomplete="off">
            <input type="hidden" name="customer_id" id="customer_id">
            <div class="cases-search-results" id="search-results" style="display: none;"></div>
        </div>
        
        <!-- Selected Client Display -->
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
    </div>

    <!-- Dynamic Relationship Options -->
    <div id="relationship-options" class="cases-relationship-section">
    <!-- Invoice -->
    <div class="cases-form-group search-option" id="invoice_div">
        <label class="cases-form-label" for="invoice_id"><?php echo _l('select_invoice'); ?></label>
        <select name="invoice_id" id="invoice_id" class="cases-form-control selectpicker" data-live-search="true">
            <option value=""><?php echo _l('select_invoice'); ?></option>
        </select>
    </div>

    <!-- Contact -->
    <div class="cases-form-group search-option" id="contact_div" style="display:none;">
        <label class="cases-form-label" for="contact_id"><?php echo _l('select_contact'); ?></label>
        <select name="contact_id" id="contact_id" class="cases-form-control selectpicker" data-live-search="true">
            <option value=""><?php echo _l('select_contact'); ?></option>
        </select>
    </div>

    <!-- Consultation -->
    <div class="cases-form-group search-option" id="consultation_div" style="display:none;">
        <label class="cases-form-label" for="consultation_id"><?php echo _l('select_consultation'); ?></label>
        <select name="consultation_id" id="consultation_id" class="cases-form-control selectpicker" data-live-search="true">
            <option value=""><?php echo _l('select_consultation'); ?></option>
        </select>
    </div>

    <!-- Case -->
    <div class="cases-form-group search-option" id="case_div" style="display:none;">
        <label class="cases-form-label" for="case_id"><?php echo _l('select_case'); ?></label>
        <select name="case_id" id="case_id" class="cases-form-control selectpicker" data-live-search="true">
            <option value=""><?php echo _l('select_case'); ?></option>
        </select>
    </div>

    <!-- Hearing -->
    <div class="cases-form-group search-option" id="hearing_div" style="display:none;">
        <label class="cases-form-label" for="hearing_id"><?php echo _l('select_hearing'); ?></label>
        <select name="hearing_id" id="hearing_id" class="cases-form-control selectpicker" data-live-search="true">
            <option value=""><?php echo _l('select_hearing'); ?></option>
        </select>
    </div>
</div>

    <!-- Additional Filters Section -->
    <div class="cases-additional-filters">
        <h4><i class="fas fa-sliders-h"></i> Additional Filters</h4>
        
        <!-- Document Tag -->
        <div class="cases-form-group">
            <label class="cases-form-label" for="document_tag"><i class="fas fa-tag"></i> <?php echo _l('document_tag'); ?></label>
            <input type="text" name="document_tag" id="document_tag" class="cases-form-control" placeholder="<?php echo _l('enter_document_tag'); ?>">
        </div>
        
        <!-- Date Range Filter -->
        <div class="cases-form-grid cases-form-grid-2">
            <div class="cases-form-group">
                <label class="cases-form-label" for="date_from"><i class="fas fa-calendar"></i> Date From</label>
                <input type="date" name="date_from" id="date_from" class="cases-form-control">
            </div>
            <div class="cases-form-group">
                <label class="cases-form-label" for="date_to"><i class="fas fa-calendar"></i> Date To</label>
                <input type="date" name="date_to" id="date_to" class="cases-form-control">
            </div>
        </div>
    </div>
    
    <!-- Search Actions -->
    <div class="cases-upload-actions">
        <button type="button" class="cases-btn cases-btn-secondary" id="reset-search">
            <i class="fas fa-undo"></i> Reset Filters
        </button>
        <button type="submit" id="search-btn" class="cases-btn cases-btn-success">
            <i class="fas fa-search"></i> Search Documents
        </button>
    </div>

    <?php echo form_close(); ?>
    
</div>
<?php echo cases_page_wrapper_end(); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
  const admin_url = "<?php echo admin_url(); ?>";

  if ($.fn.selectpicker) {
    $('.selectpicker').selectpicker({ showSubtext: true });
  }

  // Enhanced client search elements
  const customerSearch = document.getElementById('customer_search');
  const customerIdHidden = document.getElementById('customer_id');
  const searchResults = document.getElementById('search-results');
  const selectedClientDiv = document.getElementById('selected-client');
  let searchTimeout;
  let clients = <?php echo json_encode($customers); ?>;
  
  const invoiceSelect = document.getElementById('invoice_id');
  const contactSelect = document.getElementById('contact_id');
  const consultationSelect = document.getElementById('consultation_id');
  const caseSelect = document.getElementById('case_id');
  const hearingSelect = document.getElementById('hearing_id');

  const radioButtons = document.querySelectorAll('input[name="search_type"]');
  const docTypeOptions = document.querySelectorAll('.cases-doc-type-option');
  const divs = {
    invoice: document.getElementById('invoice_div'),
    contact: document.getElementById('contact_div'),
    consultation: document.getElementById('consultation_div'),
    case: document.getElementById('case_div'),
    hearing: document.getElementById('hearing_div')
  };

  function toggleDivs(type) {
    Object.keys(divs).forEach(k => divs[k].style.display = 'none');
    if (type === 'invoice') divs.invoice.style.display = 'block';
    else if (type === 'contact') divs.contact.style.display = 'block';
    else if (type === 'consultation') divs.consultation.style.display = 'block';
    else if (type === 'case') divs.case.style.display = 'block';
    else if (type === 'hearing') { divs.case.style.display = 'block'; divs.hearing.style.display = 'block'; }
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
      
      // Toggle divs
      toggleDivs(radio.value);
    });
  });

  radioButtons.forEach(r => r.addEventListener('change', function() { toggleDivs(this.value); }));

  toggleDivs(document.querySelector('input[name="search_type"]:checked').value);

  // Enhanced client search functionality
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
    customerSearch.value = client.company;
    customerIdHidden.value = client.userid;
    hideSearchResults();
    showSelectedClient(client.company);
    
    // Load related data
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
  });
  
  function loadClientRelatedData(customerId) {
    if (!customerId) return;

    function post(url, body, selectEl) {
      fetch(url, { 
        method: "POST", 
        headers: { "Content-Type": "application/x-www-form-urlencoded" }, 
        body: new URLSearchParams(body).toString() 
      })
      .then(r => r.text())
      .then(html => { 
        selectEl.innerHTML = html; 
        if ($.fn.selectpicker) $(selectEl).selectpicker('refresh'); 
      })
      .catch(err => console.error(url, err));
    }
    
    const body = id => ({ [id]: customerId, [csrfName]: csrfHash });
    post(admin_url+"cases/documents/get_invoices_by_customer", body('customer_id'), invoiceSelect);
    post(admin_url+"cases/documents/get_contacts_by_customer", body('customer_id'), contactSelect);
    post(admin_url+"cases/documents/get_consultations_by_client", body('customer_id'), consultationSelect);
    post(admin_url+"cases/documents/get_cases_by_client", body('customer_id'), caseSelect);
  }

  if (caseSelect) {
    caseSelect.addEventListener('change', function() {
      if (document.querySelector('input[name="search_type"]:checked').value !== 'hearing') return;
      fetch(admin_url+"documents/get_hearings_by_case", { 
        method: "POST", 
        headers: { "Content-Type": "application/x-www-form-urlencoded" }, 
        body: new URLSearchParams({ case_id: this.value, [csrfName]: csrfHash }).toString() 
      })
      .then(r => r.text())
      .then(html => { 
        hearingSelect.innerHTML = html; 
        if ($.fn.selectpicker) $(hearingSelect).selectpicker('refresh'); 
      })
      .catch(err => console.error("hearings", err));
    });
  }
  
  // Reset functionality
  document.getElementById('reset-search').addEventListener('click', function() {
    // Reset form
    document.getElementById('document-search-form').reset();
    
    // Reset client search
    hideSelectedClient();
    customerSearch.value = '';
    customerIdHidden.value = '';
    
    // Reset doc type to 'all'
    document.querySelectorAll('.cases-doc-type-option').forEach(opt => opt.classList.remove('active'));
    document.querySelector('.cases-doc-type-option[data-type="all"]').classList.add('active');
    document.querySelector('input[name="search_type"][value="all"]').checked = true;
    
    // Hide all relationship divs
    toggleDivs('all');
    
    // Reset selectpickers
    if ($.fn.selectpicker) {
      $('.selectpicker').selectpicker('refresh');
    }
  });
});
</script>

<?php init_tail(); ?>
