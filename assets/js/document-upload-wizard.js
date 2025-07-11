/**
 * Document Upload Wizard
 * Extracted from views/admin/documents/documents_upload_form.php
 * Modular JavaScript for the document upload process
 */

class DocumentUploadWizard {
    constructor(config) {
        this.config = {
            csrfName: config.csrfName,
            csrfHash: config.csrfHash,
            adminUrl: config.adminUrl,
            clients: config.clients || [],
            ...config
        };
        
        // Wizard state management
        this.currentStep = 1;
        this.selectedFile = null;
        this.selectedClient = null;
        this.selectedDocType = null;
        this.selectedEntity = null;
        this.searchTimeout = null;
        
        this.init();
    }
    
    init() {
        console.log('DocumentUploadWizard initializing...');
        
        // Make wizard globally accessible
        window.wizard = this;
        
        this.initializeElements();
        this.bindEvents();
        this.initializeSelectPickers();
        this.handlePrePopulation();
        this.showStep(1);
        
        // Add a global fallback for testing
        window.testCardClick = () => {
            console.log('Testing card click functionality...');
            const firstCard = document.querySelector('.cases-connection-card');
            if (firstCard) {
                console.log('Found first card, simulating click');
                this.handleConnectionSelection({ currentTarget: firstCard });
            } else {
                console.log('No cards found');
            }
        };
        
        console.log('DocumentUploadWizard initialized. Cards should now be clickable!');
        console.log('Wizard instance available at: window.wizard');
    }
    
    initializeElements() {
        // File elements
        this.fileInput = document.getElementById('document');
        this.fileDropZone = document.getElementById('file-drop-zone');
        this.filePreview = document.getElementById('file-preview');
        
        // Client elements
        this.customerSearch = document.getElementById('customer_search');
        this.customerIdHidden = document.getElementById('customer_id');
        this.searchResults = document.getElementById('search-results');
        this.selectedClientDiv = document.getElementById('selected-client');
        
        // Wizard navigation
        this.tabButtons = document.querySelectorAll('[data-tab]');
        this.tabContents = document.querySelectorAll('.tab-content');
        
        // Quick actions
        this.quickActions = document.querySelectorAll('.cases-quick-action');
        this.documentTypes = document.getElementById('document-types');
        this.relatedEntities = document.getElementById('related-entities');
        
        // Form
        this.form = document.getElementById('document-upload-form');
    }
    
    bindEvents() {
        // File upload events
        if (this.fileDropZone) {
            this.fileDropZone.addEventListener('click', () => this.fileInput.click());
        }
        
        if (this.fileInput) {
            this.fileInput.addEventListener('change', () => this.handleFileSelection());
        }
        
        // Drag and drop
        this.initializeDragAndDrop();
        
        // Client search
        if (this.customerSearch) {
            this.customerSearch.addEventListener('input', (e) => this.handleClientSearch(e));
            this.customerSearch.addEventListener('blur', () => {
                setTimeout(() => this.hideSearchResults(), 200);
            });
        }
        
        // Connection card selection using event delegation
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.cases-connection-card');
            if (card) {
                console.log('Connection card clicked:', card.dataset.type);
                e.preventDefault();
                e.stopPropagation();
                this.handleConnectionSelection(e, card);
            }
        });
        
        // Keyboard support for accessibility
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const card = e.target.closest('.cases-connection-card');
                if (card) {
                    e.preventDefault();
                    this.handleConnectionSelection(e, card);
                }
            }
        });
        
        // Legal matter sub-type selection
        document.querySelectorAll('.cases-legal-option').forEach(option => {
            option.addEventListener('click', (e) => this.handleLegalSubtypeSelection(e));
        });
        
        // Entity item selection
        document.addEventListener('click', (e) => {
            if (e.target.closest('.cases-entity-item')) {
                this.handleEntityItemSelection(e);
            }
        });
        
        // Search and filter functionality
        const entitySearch = document.getElementById('entity-search');
        if (entitySearch) {
            entitySearch.addEventListener('input', (e) => this.handleEntitySearch(e));
        }
        
        // Filter buttons
        document.querySelectorAll('.cases-filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleFilterToggle(e));
        });
        
        // Change selection button
        const changeSelectionBtn = document.getElementById('change-selection');
        if (changeSelectionBtn) {
            changeSelectionBtn.addEventListener('click', () => this.resetSelection());
        }
        
        // Legacy entity selection (for compatibility)
        ['invoice_id', 'consultation_id', 'case_id', 'hearing_id'].forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.addEventListener('change', (e) => this.handleEntitySelection(e, selectId));
            }
        });
        
        // Navigation buttons
        this.bindNavigationButtons();
        
        // Form submission
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmission(e));
        }
        
        // Change buttons
        const changeFileBtn = document.getElementById('change-file');
        if (changeFileBtn) {
            changeFileBtn.addEventListener('click', () => this.changeFile());
        }
        
        const changeClientBtn = document.getElementById('change-client');
        if (changeClientBtn) {
            changeClientBtn.addEventListener('click', () => this.changeClient());
        }
    }
    
    initializeSelectPickers() {
        if (window.$ && $.fn.selectpicker) {
            $('.selectpicker').selectpicker();
            
            // Fix dropdown overflow issues
            this.fixSelectPickerOverflow();
        }
    }
    
    fixSelectPickerOverflow() {
        if (!window.$) return;
        
        // Override SelectPicker's dropdown positioning
        $('.cases-related-entities .selectpicker').on('shown.bs.select', function() {
            const $dropdown = $(this).siblings('.dropdown-menu');
            
            // Remove problematic inline styles
            $dropdown.css({
                'overflow': 'visible',
                'max-height': '300px'
            });
            
            // Fix inner content
            $dropdown.find('.inner').css({
                'max-height': '250px',
                'overflow-y': 'auto'
            });
        });
    }
    
    // ==========================================
    // WIZARD NAVIGATION
    // ==========================================
    
    showStep(stepNumber, direction = 'forward') {
        const previousStep = this.currentStep;
        
        // Update step indicators
        document.querySelectorAll('.cases-upload-step').forEach((step, index) => {
            if (index + 1 <= stepNumber) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
        
        // Handle dropdown overflow for step 3 (entity selection)
        const wizardContainer = document.querySelector('.cases-upload-wizard');
        if (wizardContainer) {
            if (stepNumber === 3) {
                wizardContainer.classList.add('has-dropdowns');
            } else {
                wizardContainer.classList.remove('has-dropdowns');
            }
        }
        
        // Animate content sections
        document.querySelectorAll('.cases-upload-content').forEach((content, index) => {
            const contentStep = index + 1;
            
            if (contentStep === stepNumber) {
                content.style.display = 'block';
                setTimeout(() => content.classList.add('active'), 10);
            } else if (contentStep === previousStep) {
                content.classList.remove('active');
                content.classList.add(direction === 'forward' ? 'slide-out-left' : 'slide-out-right');
                
                setTimeout(() => {
                    content.style.display = 'none';
                    content.classList.remove('slide-out-left', 'slide-out-right');
                }, 300);
            } else {
                content.classList.remove('active');
                content.style.display = 'none';
            }
        });
        
        this.currentStep = stepNumber;
    }
    
    enableNextButton(stepNumber) {
        const nextBtn = document.getElementById(`next-step-${stepNumber}`);
        if (nextBtn) nextBtn.disabled = false;
    }
    
    disableNextButton(stepNumber) {
        const nextBtn = document.getElementById(`next-step-${stepNumber}`);
        if (nextBtn) nextBtn.disabled = true;
    }
    
    bindNavigationButtons() {
        // Next buttons
        for (let i = 1; i <= 3; i++) {
            const nextBtn = document.getElementById(`next-step-${i}`);
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    if (i === 3) {
                        this.updateSummary();
                    }
                    this.showStep(i + 1, 'forward');
                });
            }
        }
        
        // Previous buttons
        for (let i = 2; i <= 4; i++) {
            const prevBtn = document.getElementById(`prev-step-${i}`);
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    this.showStep(i - 1, 'backward');
                });
            }
        }
    }
    
    // ==========================================
    // FILE HANDLING
    // ==========================================
    
    handleFileSelection() {
        const file = this.fileInput.files[0];
        if (file) {
            this.selectedFile = file;
            this.showFilePreview(file);
            this.enableNextButton(1);
        }
    }
    
    showFilePreview(file) {
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const fileIcon = document.querySelector('.cases-file-icon i');
        
        if (fileName) fileName.textContent = file.name;
        if (fileSize) fileSize.textContent = this.formatFileSize(file.size);
        
        // Update file icon based on type
        if (fileIcon) {
            if (file.type.includes('pdf')) {
                fileIcon.className = 'fas fa-file-pdf';
            } else if (file.type.includes('image')) {
                fileIcon.className = 'fas fa-file-image';
            } else if (file.type.includes('word')) {
                fileIcon.className = 'fas fa-file-word';
            } else {
                fileIcon.className = 'fas fa-file';
            }
        }
        
        if (this.fileDropZone) this.fileDropZone.style.display = 'none';
        if (this.filePreview) this.filePreview.style.display = 'block';
    }
    
    changeFile() {
        if (this.filePreview) this.filePreview.style.display = 'none';
        if (this.fileDropZone) this.fileDropZone.style.display = 'block';
        this.selectedFile = null;
        this.fileInput.value = '';
        this.disableNextButton(1);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    initializeDragAndDrop() {
        if (!this.fileDropZone) return;
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.fileDropZone.addEventListener(eventName, this.preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            this.fileDropZone.addEventListener(eventName, () => {
                this.fileDropZone.classList.add('cases-file-drag-over');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            this.fileDropZone.addEventListener(eventName, () => {
                this.fileDropZone.classList.remove('cases-file-drag-over');
            }, false);
        });
        
        this.fileDropZone.addEventListener('drop', (e) => this.handleDrop(e), false);
    }
    
    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            this.fileInput.files = files;
            this.handleFileSelection();
        }
    }
    
    // ==========================================
    // CLIENT SEARCH
    // ==========================================
    
    handleClientSearch(e) {
        const query = e.target.value.trim();
        
        clearTimeout(this.searchTimeout);
        
        if (query.length < 2) {
            this.hideSearchResults();
            return;
        }
        
        this.searchTimeout = setTimeout(() => {
            this.searchClients(query);
        }, 300);
    }
    
    searchClients(query) {
        const results = this.config.clients.filter(client => 
            client.company.toLowerCase().includes(query.toLowerCase())
        );
        this.showSearchResults(results);
    }
    
    showSearchResults(results) {
        if (!this.searchResults) return;
        
        this.searchResults.innerHTML = '';
        
        if (results.length === 0) {
            this.searchResults.innerHTML = '<div class="cases-search-no-results">No clients found</div>';
        } else {
            results.forEach(client => {
                const resultDiv = document.createElement('div');
                resultDiv.className = 'cases-search-result';
                resultDiv.innerHTML = `
                    <div class="cases-search-result-name">${this.htmlEscape(client.company)}</div>
                    <div class="cases-search-result-details">Client ID: ${client.userid}</div>
                `;
                
                resultDiv.addEventListener('click', () => this.selectClient(client));
                this.searchResults.appendChild(resultDiv);
            });
        }
        
        this.searchResults.style.display = 'block';
    }
    
    hideSearchResults() {
        if (this.searchResults) {
            this.searchResults.style.display = 'none';
        }
    }
    
    selectClient(client) {
        this.selectedClient = {
            id: client.userid,
            name: client.company
        };
        
        if (this.customerSearch) this.customerSearch.value = client.company;
        if (this.customerIdHidden) this.customerIdHidden.value = client.userid;
        
        this.hideSearchResults();
        this.showSelectedClient(client.company);
        this.enableNextButton(2);
        this.loadClientRelatedData(client.userid);
    }
    
    showSelectedClient(clientName) {
        const clientNameEl = document.getElementById('client-name');
        const clientDetailsEl = document.getElementById('client-details');
        
        if (clientNameEl) clientNameEl.textContent = clientName;
        if (clientDetailsEl) clientDetailsEl.textContent = 'Client selected';
        
        if (this.customerSearch) this.customerSearch.style.display = 'none';
        if (this.selectedClientDiv) this.selectedClientDiv.style.display = 'block';
    }
    
    changeClient() {
        if (this.customerSearch) this.customerSearch.style.display = 'block';
        if (this.selectedClientDiv) this.selectedClientDiv.style.display = 'none';
        
        if (this.customerSearch) this.customerSearch.value = '';
        if (this.customerIdHidden) this.customerIdHidden.value = '';
        
        this.selectedClient = null;
        this.disableNextButton(2);
    }
    
    // ==========================================
    // DOCUMENT ASSOCIATION
    // ==========================================
    
    handleQuickAction(e) {
        const type = e.currentTarget.dataset.type;
        
        // Remove active state from all quick actions
        this.quickActions.forEach(qa => qa.classList.remove('active'));
        e.currentTarget.classList.add('active');
        
        if (type === 'general') {
            this.selectedDocType = 'customer';
            const customerRadio = document.querySelector('input[name="doc_owner_type"][value="customer"]');
            if (customerRadio) customerRadio.checked = true;
            
            if (this.documentTypes) this.documentTypes.style.display = 'none';
            if (this.relatedEntities) this.relatedEntities.style.display = 'none';
            this.enableNextButton(3);
            
        } else if (type === 'case') {
            this.selectedDocType = 'case';
            this.showDocumentTypeSelection(['case', 'hearing']);
            
        } else if (type === 'invoice') {
            this.selectedDocType = 'invoice';
            this.showDocumentTypeSelection(['invoice', 'customer']);
            
        } else if (type === 'consultation') {
            this.selectedDocType = 'consultation';
            const consultationRadio = document.querySelector('input[name="doc_owner_type"][value="consultation"]');
            if (consultationRadio) consultationRadio.checked = true;
            
            if (this.documentTypes) this.documentTypes.style.display = 'none';
            if (this.relatedEntities) this.relatedEntities.style.display = 'block';
            
            const consultationDiv = document.getElementById('consultation_div');
            if (consultationDiv) consultationDiv.style.display = 'block';
            
            this.disableNextButton(3);
        }
    }
    
    showDocumentTypeSelection(allowedTypes) {
        if (!this.documentTypes) return;
        
        this.documentTypes.style.display = 'block';
        
        // Hide all doc type options first
        document.querySelectorAll('.cases-doc-type-option').forEach(option => {
            option.style.display = 'none';
        });
        
        // Show only allowed types
        allowedTypes.forEach(type => {
            const option = document.querySelector(`.cases-doc-type-option[data-type="${type}"]`);
            if (option) option.style.display = 'block';
        });
    }
    
    handleDocTypeSelection(e) {
        const option = e.currentTarget;
        const radio = option.querySelector('input[type="radio"]');
        
        if (option.style.display === 'none') return;
        
        // Update UI
        document.querySelectorAll('.cases-doc-type-option').forEach(opt => opt.classList.remove('active'));
        option.classList.add('active');
        
        // Select the radio
        if (radio) {
            radio.checked = true;
            this.selectedDocType = radio.value;
        }
        
        this.showRelatedEntitySelection(this.selectedDocType);
    }
    
    showRelatedEntitySelection(docType) {
        // Hide all entity selects first
        document.querySelectorAll('.entity-select').forEach(select => {
            select.style.display = 'none';
        });
        
        if (docType === 'customer') {
            if (this.relatedEntities) this.relatedEntities.style.display = 'none';
            this.enableNextButton(3);
        } else {
            if (this.relatedEntities) this.relatedEntities.style.display = 'block';
            
            const divMap = {
                'invoice': 'invoice_div',
                'consultation': 'consultation_div',
                'case': 'case_div',
                'hearing': ['case_div', 'hearing_div']
            };
            
            const divsToShow = Array.isArray(divMap[docType]) ? divMap[docType] : [divMap[docType]];
            divsToShow.forEach(divId => {
                const div = document.getElementById(divId);
                if (div) div.style.display = 'block';
            });
            
            this.disableNextButton(3);
        }
    }
    
    handleEntitySelection(e, selectId) {
        if (e.target.value) {
            this.selectedEntity = {
                type: selectId.replace('_id', ''),
                id: e.target.value,
                name: e.target.options[e.target.selectedIndex].text
            };
            this.enableNextButton(3);
            
            // If case is selected and we need hearing, load hearings
            if (selectId === 'case_id' && this.selectedDocType === 'hearing') {
                this.updateHearingDropdown(e.target.value);
            }
        } else {
            this.selectedEntity = null;
            this.disableNextButton(3);
        }
    }
    
    // ==========================================
    // API FUNCTIONS
    // ==========================================
    
    loadClientRelatedData(customerId) {
        if (!customerId) return;
        
        const requests = [
            { url: 'get_invoices_by_customer', targetId: 'invoice_id' },
            { url: 'get_contacts_by_customer', targetId: 'contact_id' },
            { url: 'get_consultations_by_client', targetId: 'consultation_id' },
            { url: 'get_cases_by_client', targetId: 'case_id' }
        ];
        
        requests.forEach(request => {
            this.fetchAndUpdate(
                this.config.adminUrl + 'cases/documents/' + request.url,
                { customer_id: customerId },
                request.targetId,
                'Select option...'
            );
        });
    }
    
    updateHearingDropdown(caseId) {
        if (caseId) {
            this.fetchAndUpdate(
                this.config.adminUrl + 'cases/documents/get_hearings_by_case',
                { case_id: caseId },
                'hearing_id',
                'Select hearing...'
            );
        }
    }
    
    fetchAndUpdate(url, params, targetId, defaultOptionText) {
        params[this.config.csrfName] = this.config.csrfHash;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(params)
        })
        .then(response => {
            const newToken = response.headers.get('X-CSRF-TOKEN');
            if (newToken) this.config.csrfHash = newToken;
            return response.text();
        })
        .then(data => {
            const select = document.getElementById(targetId);
            if (select) {
                select.innerHTML = data;
                if (window.$ && $.fn.selectpicker) {
                    $(select).selectpicker('refresh');
                    // Apply overflow fix to newly refreshed SelectPicker
                    this.fixSelectPickerOverflow();
                }
            }
        })
        .catch(() => {
            const select = document.getElementById(targetId);
            if (select) {
                select.innerHTML = `<option value="">${defaultOptionText}</option>`;
                if (window.$ && $.fn.selectpicker) {
                    $(select).selectpicker('refresh');
                    // Apply overflow fix to newly refreshed SelectPicker
                    this.fixSelectPickerOverflow();
                }
            }
        });
    }
    
    // ==========================================
    // UTILITY METHODS FOR CONNECTION HANDLING
    // ==========================================
    
    // ==========================================
    // NEW REDESIGNED INTERFACE HANDLERS
    // ==========================================
    
    handleConnectionSelection(e, card = null) {
        console.log('handleConnectionSelection called');
        
        // Get the card element - either passed or from event
        const cardElement = card || e.currentTarget;
        if (!cardElement) {
            console.error('No card element found');
            return;
        }
        
        const type = cardElement.dataset.type;
        const radio = cardElement.querySelector('.cases-connection-radio');
        
        console.log('Card data:', { cardElement, type, radio });
        
        // Update UI - remove active state from all cards
        document.querySelectorAll('.cases-connection-card').forEach(c => c.classList.remove('active'));
        cardElement.classList.add('active');
        
        // Select radio button
        if (radio) {
            radio.checked = true;
            this.selectedDocType = radio.value;
        }
        
        // Handle progressive disclosure based on card type
        if (type === 'case') {
            this.showLegalMatterOptions();
        } else {
            this.hideLegalMatterOptions();
            this.showEntitySelection(type);
        }
        
        console.log('Connection selection completed for type:', type);
    }
    
    handleLegalSubtypeSelection(e) {
        const option = e.currentTarget;
        const subtype = option.dataset.subtype;
        
        // Update UI
        document.querySelectorAll('.cases-legal-option').forEach(opt => opt.classList.remove('active'));
        option.classList.add('active');
        
        // Update the radio button value
        const radio = document.querySelector('input[name="doc_owner_type"][value="case"]');
        if (radio && subtype === 'hearing') {
            radio.value = 'hearing';
            this.selectedDocType = 'hearing';
        } else if (radio) {
            radio.value = 'case';
            this.selectedDocType = 'case';
        }
        
        // Show entity selection
        this.showEntitySelection(subtype);
    }
    
    showLegalMatterOptions() {
        const legalOptions = document.getElementById('legal-matter-options');
        const entitySelection = document.getElementById('entity-selection');
        const selectionSummary = document.getElementById('selection-summary');
        
        if (legalOptions) {
            legalOptions.style.display = 'block';
            legalOptions.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (entitySelection) entitySelection.style.display = 'none';
        if (selectionSummary) selectionSummary.style.display = 'none';
        
        this.disableNextButton(3);
    }
    
    hideLegalMatterOptions() {
        const legalOptions = document.getElementById('legal-matter-options');
        if (legalOptions) legalOptions.style.display = 'none';
        
        // Reset legal options
        document.querySelectorAll('.cases-legal-option').forEach(opt => opt.classList.remove('active'));
    }
    
    showEntitySelection(type) {
        const entitySelection = document.getElementById('entity-selection');
        const entityList = document.getElementById('entity-list');
        const entityLoading = document.getElementById('entity-loading');
        const entityEmpty = document.getElementById('entity-empty');
        
        if (!entitySelection) return;
        
        entitySelection.style.display = 'block';
        entitySelection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Show loading state
        if (entityLoading) entityLoading.style.display = 'block';
        if (entityList) entityList.innerHTML = '';
        if (entityEmpty) entityEmpty.style.display = 'none';
        
        // Update search placeholder
        const searchInput = document.getElementById('entity-search');
        if (searchInput) {
            const placeholders = {
                'invoice': 'Search invoices...',
                'consultation': 'Search consultations...',
                'case': 'Search cases...',
                'hearing': 'Search hearings...',
                'customer': 'Search general documents...'
            };
            searchInput.placeholder = placeholders[type] || 'Search...';
            searchInput.value = '';
        }
        
        // Load entities
        this.loadEntities(type);
    }
    
    loadEntities(type) {
        const customerId = this.selectedClient ? this.selectedClient.id : null;
        
        if (!customerId && type !== 'customer') {
            this.showEntityEmpty('Please select a client first');
            return;
        }
        
        let endpoint = '';
        let params = {};
        
        switch (type) {
            case 'invoice':
                endpoint = 'get_invoices_by_customer';
                params = { customer_id: customerId };
                break;
            case 'consultation':
                endpoint = 'get_consultations_by_client';
                params = { customer_id: customerId };
                break;
            case 'case':
                endpoint = 'get_cases_by_client';
                params = { customer_id: customerId };
                break;
            case 'hearing':
                // For hearings, we need to first show cases, then hearings
                // Show a message to first select a case
                this.showHearingCaseSelection();
                return;
            case 'customer':
                this.hideEntityLoading();
                this.enableNextButton(3);
                this.showSelectionSummary('General client document');
                return;
            default:
                this.showEntityEmpty('Unknown document type');
                return;
        }
        
        const url = this.config.adminUrl + 'cases/documents/' + endpoint;
        this.fetchEntityData(url, params, type);
    }
    
    fetchEntityData(url, params, type) {
        params[this.config.csrfName] = this.config.csrfHash;
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(params)
        })
        .then(response => response.text())
        .then(html => {
            this.parseAndDisplayEntities(html, type);
        })
        .catch(error => {
            console.error('Error loading entities:', error);
            this.showEntityEmpty('Failed to load items');
        });
    }
    
    parseAndDisplayEntities(html, type) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const options = tempDiv.querySelectorAll('option[value]:not([value=""])');
        
        this.hideEntityLoading();
        
        if (options.length === 0) {
            this.showEntityEmpty(`No ${type}s found`);
            return;
        }
        
        const entityList = document.getElementById('entity-list');
        if (!entityList) return;
        
        entityList.innerHTML = '';
        
        options.forEach(option => {
            const item = this.createEntityItem(option, type);
            entityList.appendChild(item);
        });
    }
    
    createEntityItem(option, type) {
        const item = document.createElement('div');
        item.className = 'cases-entity-item';
        item.dataset.value = option.value;
        item.dataset.type = type;
        
        const icons = {
            'invoice': 'fas fa-file-invoice',
            'consultation': 'fas fa-user-tie',
            'case': 'fas fa-briefcase',
            'hearing': 'fas fa-gavel'
        };
        
        const icon = icons[type] || 'fas fa-file';
        
        item.innerHTML = `
            <div class="cases-entity-icon">
                <i class="${icon}"></i>
            </div>
            <div class="cases-entity-details">
                <div class="cases-entity-title">${this.escapeHtml(option.textContent)}</div>
                <div class="cases-entity-meta">ID: ${option.value}</div>
            </div>
        `;
        
        return item;
    }
    
    handleEntityItemSelection(e) {
        const item = e.target.closest('.cases-entity-item');
        if (!item) return;
        
        // Special handling for hearing case selection
        if (item.dataset.type === 'hearing-case') {
            this.handleHearingCaseSelection(item);
            return;
        }
        
        // Update UI
        document.querySelectorAll('.cases-entity-item').forEach(i => i.classList.remove('selected'));
        item.classList.add('selected');
        
        // Store selection
        this.selectedEntity = {
            type: item.dataset.type,
            id: item.dataset.value,
            name: item.querySelector('.cases-entity-title').textContent
        };
        
        // Update hidden select for form submission
        this.updateHiddenSelect(item.dataset.type, item.dataset.value);
        
        // Show summary and enable next button
        this.showSelectionSummary(`${this.selectedEntity.name} (${this.selectedEntity.type})`);
        this.enableNextButton(3);
    }
    
    handleHearingCaseSelection(caseItem) {
        const caseId = caseItem.dataset.value;
        const caseName = caseItem.querySelector('.cases-entity-title').textContent;
        
        // Store the selected case for reference
        this.selectedCase = {
            id: caseId,
            name: caseName
        };
        
        // Show loading state
        const entityLoading = document.getElementById('entity-loading');
        const entityList = document.getElementById('entity-list');
        
        if (entityLoading) entityLoading.style.display = 'block';
        if (entityList) entityList.innerHTML = '';
        
        // Load hearings for this case
        const url = this.config.adminUrl + 'cases/documents/get_hearings_by_case';
        const params = { 
            case_id: caseId,
            [this.config.csrfName]: this.config.csrfHash
        };
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(params)
        })
        .then(response => response.text())
        .then(html => {
            this.parseAndDisplayHearings(html, caseName);
        })
        .catch(error => {
            console.error('Error loading hearings:', error);
            this.showEntityEmpty('Failed to load hearings for this case');
        });
    }
    
    parseAndDisplayHearings(html, caseName) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const options = tempDiv.querySelectorAll('option[value]:not([value=""])');
        
        this.hideEntityLoading();
        
        if (options.length === 0) {
            this.showEntityEmpty(`No hearings found for this case. You can still link the document to the case itself.`);
            
            // Offer option to link to case instead
            const entityList = document.getElementById('entity-list');
            if (entityList) {
                const caseOption = document.createElement('div');
                caseOption.className = 'cases-entity-item cases-fallback-option';
                caseOption.dataset.value = this.selectedCase.id;
                caseOption.dataset.type = 'case';
                caseOption.innerHTML = `
                    <div class="cases-entity-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="cases-entity-details">
                        <div class="cases-entity-title">Link to Case: ${this.escapeHtml(caseName)}</div>
                        <div class="cases-entity-meta">No hearings available - link to case instead</div>
                    </div>
                `;
                entityList.appendChild(caseOption);
            }
            return;
        }
        
        const entityList = document.getElementById('entity-list');
        if (!entityList) return;
        
        entityList.innerHTML = '';
        
        // Add back button and header
        const header = document.createElement('div');
        header.className = 'cases-hearing-selection-header';
        header.innerHTML = `
            <div style="background: #e8f5e8; padding: 12px; border-radius: 4px; margin-bottom: 16px; border-left: 4px solid #4caf50;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h6 style="margin: 0 0 4px 0; color: #2e7d32;">
                            <i class="fas fa-gavel"></i> Select Hearing
                        </h6>
                        <p style="margin: 0; font-size: 13px; color: #555;">
                            Case: ${this.escapeHtml(caseName)}
                        </p>
                    </div>
                    <button type="button" class="cases-btn-sm cases-btn-secondary" onclick="window.wizard.showHearingCaseSelection()">
                        <i class="fas fa-arrow-left"></i> Back to Cases
                    </button>
                </div>
            </div>
        `;
        entityList.appendChild(header);
        
        // Add hearings as selectable items
        options.forEach(option => {
            const item = this.createEntityItem(option, 'hearing');
            entityList.appendChild(item);
        });
    }
    
    updateHiddenSelect(type, value) {
        if (type === 'hearing') {
            // For hearings, we need to set both case_id and hearing_id
            this.updateHiddenSelectField('case_id', this.selectedCase.id);
            this.updateHiddenSelectField('hearing_id', value);
        } else {
            this.updateHiddenSelectField(type + '_id', value);
        }
    }
    
    updateHiddenSelectField(selectId, value) {
        const select = document.getElementById(selectId);
        if (select) {
            // Add option if it doesn't exist
            let option = select.querySelector(`option[value="${value}"]`);
            if (!option) {
                option = document.createElement('option');
                option.value = value;
                option.textContent = this.selectedEntity ? this.selectedEntity.name : `ID: ${value}`;
                select.appendChild(option);
            }
            select.value = value;
            
            // Update SelectPicker if present
            if (window.$ && $.fn.selectpicker) {
                $(select).selectpicker('refresh');
            }
        }
    }
    
    handleEntitySearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.cases-entity-item');
        
        items.forEach(item => {
            const title = item.querySelector('.cases-entity-title').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    handleFilterToggle(e) {
        const button = e.currentTarget;
        
        // Toggle active state
        document.querySelectorAll('.cases-filter-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        
        // Apply filter logic here if needed
        // For now, just visual feedback
    }
    
    showEntityEmpty(message) {
        const entityLoading = document.getElementById('entity-loading');
        const entityEmpty = document.getElementById('entity-empty');
        
        if (entityLoading) entityLoading.style.display = 'none';
        if (entityEmpty) {
            entityEmpty.style.display = 'flex';
            const p = entityEmpty.querySelector('p');
            if (p) p.textContent = message;
        }
    }
    
    showHearingCaseSelection() {
        const customerId = this.selectedClient ? this.selectedClient.id : null;
        const entityList = document.getElementById('entity-list');
        const entityLoading = document.getElementById('entity-loading');
        const entityEmpty = document.getElementById('entity-empty');
        
        if (entityLoading) entityLoading.style.display = 'block';
        if (entityList) entityList.innerHTML = '';
        if (entityEmpty) entityEmpty.style.display = 'none';
        
        // Load cases first for hearing selection
        const url = this.config.adminUrl + 'cases/documents/get_cases_by_client';
        const params = { 
            customer_id: customerId,
            [this.config.csrfName]: this.config.csrfHash
        };
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(params)
        })
        .then(response => response.text())
        .then(html => {
            this.parseAndDisplayCasesForHearing(html);
        })
        .catch(error => {
            console.error('Error loading cases for hearing:', error);
            this.showEntityEmpty('Failed to load cases');
        });
    }
    
    parseAndDisplayCasesForHearing(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const options = tempDiv.querySelectorAll('option[value]:not([value=""])');
        
        this.hideEntityLoading();
        
        if (options.length === 0) {
            this.showEntityEmpty('No cases found. Please create a case first.');
            return;
        }
        
        const entityList = document.getElementById('entity-list');
        if (!entityList) return;
        
        entityList.innerHTML = '';
        
        // Add header to explain the two-step process
        const header = document.createElement('div');
        header.className = 'cases-hearing-selection-header';
        header.innerHTML = `
            <div style="background: #e3f2fd; padding: 12px; border-radius: 4px; margin-bottom: 16px; border-left: 4px solid #2196f3;">
                <h6 style="margin: 0 0 4px 0; color: #1976d2;">
                    <i class="fas fa-info-circle"></i> Hearing Document Selection
                </h6>
                <p style="margin: 0; font-size: 13px; color: #555;">
                    First select a case, then choose the specific hearing for this document.
                </p>
            </div>
        `;
        entityList.appendChild(header);
        
        // Add cases as selectable items
        options.forEach(option => {
            const item = this.createCaseItemForHearing(option);
            entityList.appendChild(item);
        });
    }
    
    createCaseItemForHearing(option) {
        const item = document.createElement('div');
        item.className = 'cases-entity-item cases-hearing-case-item';
        item.dataset.value = option.value;
        item.dataset.type = 'hearing-case';
        
        item.innerHTML = `
            <div class="cases-entity-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="cases-entity-details">
                <div class="cases-entity-title">${this.escapeHtml(option.textContent)}</div>
                <div class="cases-entity-meta">Case ID: ${option.value} → Click to view hearings</div>
            </div>
            <div class="cases-entity-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        `;
        
        return item;
    }
    
    hideEntityLoading() {
        const entityLoading = document.getElementById('entity-loading');
        if (entityLoading) entityLoading.style.display = 'none';
    }
    
    showSelectionSummary(text) {
        const selectionSummary = document.getElementById('selection-summary');
        const summaryText = document.getElementById('summary-text');
        
        if (selectionSummary) selectionSummary.style.display = 'block';
        if (summaryText) summaryText.textContent = text;
    }
    
    resetSelection() {
        // Reset all UI states
        document.querySelectorAll('.cases-connection-card').forEach(card => card.classList.remove('active'));
        document.querySelectorAll('.cases-legal-option').forEach(opt => opt.classList.remove('active'));
        document.querySelectorAll('.cases-entity-item').forEach(item => item.classList.remove('selected'));
        
        // Hide sections
        const legalOptions = document.getElementById('legal-matter-options');
        const entitySelection = document.getElementById('entity-selection');
        const selectionSummary = document.getElementById('selection-summary');
        
        if (legalOptions) legalOptions.style.display = 'none';
        if (entitySelection) entitySelection.style.display = 'none';
        if (selectionSummary) selectionSummary.style.display = 'none';
        
        // Reset form data
        this.selectedDocType = null;
        this.selectedEntity = null;
        
        // Clear form fields
        document.querySelectorAll('input[name="doc_owner_type"]').forEach(radio => radio.checked = false);
        ['invoice_id', 'consultation_id', 'case_id', 'hearing_id'].forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) select.value = '';
        });
        
        this.disableNextButton(3);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // ==========================================
    // SUMMARY AND SUBMISSION
    // ==========================================
    
    updateSummary() {
        // File details
        const summaryFile = document.getElementById('summary-file');
        const summarySize = document.getElementById('summary-size');
        
        if (summaryFile) summaryFile.textContent = this.selectedFile ? this.selectedFile.name : '-';
        if (summarySize) summarySize.textContent = this.selectedFile ? this.formatFileSize(this.selectedFile.size) : '-';
        
        // Client info
        const summaryClient = document.getElementById('summary-client');
        if (summaryClient) summaryClient.textContent = this.selectedClient ? this.selectedClient.name : '-';
        
        // Document type
        let typeText = '-';
        if (this.selectedDocType) {
            const typeLabels = {
                'customer': 'General Client Document',
                'invoice': 'Invoice Document',
                'contact': 'Contact Document',
                'consultation': 'Consultation Document',
                'case': 'Case Document',
                'hearing': 'Hearing Document'
            };
            typeText = typeLabels[this.selectedDocType] || this.selectedDocType;
        }
        
        const summaryType = document.getElementById('summary-type');
        if (summaryType) summaryType.textContent = typeText;
        
        // Related entity
        const relationItem = document.getElementById('summary-relation-item');
        const summaryRelation = document.getElementById('summary-relation');
        
        if (this.selectedEntity && summaryRelation && relationItem) {
            summaryRelation.textContent = this.selectedEntity.name;
            relationItem.style.display = 'block';
        } else if (relationItem) {
            relationItem.style.display = 'none';
        }
    }
    
    handleFormSubmission(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('upload-document');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            submitBtn.disabled = true;
        }
        
        const formData = new FormData(this.form);
        formData.append(this.config.csrfName, this.config.csrfHash);
        
        fetch(this.form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document uploaded successfully!');
                window.location.href = this.config.adminUrl + 'cases/documents';
            } else {
                alert('Upload failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Network error occurred during upload');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    
    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================
    
    htmlEscape(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    handlePrePopulation() {
        try {
            const uploadData = JSON.parse(localStorage.getItem('document_upload_data'));
            if (uploadData) {
                localStorage.removeItem('document_upload_data');
                
                if (uploadData.customer_id) {
                    setTimeout(() => {
                        const targetCustomer = this.config.clients.find(client => client.userid == uploadData.customer_id);
                        if (targetCustomer) {
                            this.selectClient(targetCustomer);
                            this.showStep(2);
                        }
                    }, 500);
                }
            }
        } catch (e) {
            console.error('Error processing upload data:', e);
        }
    }
}

// Export for use in other modules
window.DocumentUploadWizard = DocumentUploadWizard;