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
        this.initializeElements();
        this.bindEvents();
        this.initializeSelectPickers();
        this.handlePrePopulation();
        this.showStep(1);
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
        
        // Quick actions
        this.quickActions.forEach(action => {
            action.addEventListener('click', (e) => this.handleQuickAction(e));
        });
        
        // Document type selection
        document.querySelectorAll('.cases-doc-type-option').forEach(option => {
            option.addEventListener('click', (e) => this.handleDocTypeSelection(e));
        });
        
        // Entity selection
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
        }
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
                }
            }
        })
        .catch(() => {
            const select = document.getElementById(targetId);
            if (select) {
                select.innerHTML = `<option value="">${defaultOptionText}</option>`;
                if (window.$ && $.fn.selectpicker) {
                    $(select).selectpicker('refresh');
                }
            }
        });
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