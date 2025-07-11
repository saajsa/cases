<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['forms', 'buttons', 'tables', 'status']);
echo cases_page_wrapper_start(
    'Manage Hearings',
    'Schedule and track all court hearings',
    [
        [
            'text' => '← Back to Cases',
            'href' => admin_url('cases'),
            'class' => 'cases-btn'
        ],
        [
            'text' => 'View Cause List',
            'href' => admin_url('cases/hearings/causelist'),
            'class' => 'cases-btn cases-btn-primary'
        ]
    ]
);
?>

<!-- Quick Stats -->
<div class="cases-grid cases-grid-4 cases-mb-lg">
    <div class="cases-card cases-text-center">
        <div style="font-size: 2rem; font-weight: 600; color: var(--cases-primary);" id="total-hearings">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase;">Total Hearings</div>
    </div>
    <div class="cases-card cases-text-center">
        <div style="font-size: 2rem; font-weight: 600; color: var(--cases-info);" id="upcoming-hearings">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase;">Upcoming</div>
    </div>
    <div class="cases-card cases-text-center">
        <div style="font-size: 2rem; font-weight: 600; color: var(--cases-warning);" id="today-hearings">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase;">Today</div>
    </div>
    <div class="cases-card cases-text-center">
        <div style="font-size: 2rem; font-weight: 600; color: var(--cases-success);" id="completed-hearings">-</div>
        <div class="cases-text-muted cases-font-size-sm" style="text-transform: uppercase;">Completed</div>
    </div>
</div>

<!-- Main Content with Tabs -->
<?php echo cases_section_start(''); ?>

<!-- Tab Navigation -->
<div style="background: var(--cases-bg-tertiary); border-bottom: 1px solid var(--cases-border); padding: 0; margin: -30px -30px 30px -30px; display: flex;">
    <button class="cases-btn active" data-tab="add-hearing" style="background: none; border: none; padding: 20px 30px; font-size: 0.875rem; font-weight: 500; color: var(--cases-text-light); cursor: pointer; transition: var(--cases-transition); border-bottom: 2px solid transparent; border-radius: 0;">
        Add New Hearing
    </button>
    <button class="cases-btn" data-tab="upcoming" style="background: none; border: none; padding: 20px 30px; font-size: 0.875rem; font-weight: 500; color: var(--cases-text-light); cursor: pointer; transition: var(--cases-transition); border-bottom: 2px solid transparent; border-radius: 0;">
        Upcoming Hearings <span id="upcoming-badge" class="cases-count-badge">0</span>
    </button>
    <button class="cases-btn" data-tab="past" style="background: none; border: none; padding: 20px 30px; font-size: 0.875rem; font-weight: 500; color: var(--cases-text-light); cursor: pointer; transition: var(--cases-transition); border-bottom: 2px solid transparent; border-radius: 0;">
        Past Hearings <span id="past-badge" class="cases-count-badge">0</span>
    </button>
    <button class="cases-btn" data-tab="all" style="background: none; border: none; padding: 20px 30px; font-size: 0.875rem; font-weight: 500; color: var(--cases-text-light); cursor: pointer; transition: var(--cases-transition); border-bottom: 2px solid transparent; border-radius: 0;">
        All Hearings <span id="all-badge" class="cases-count-badge">0</span>
    </button>
</div>

<!-- Add New Hearing Tab -->
<div class="tab-content" id="add-hearing-tab">
    <h3 class="cases-section-title" style="margin-bottom: var(--cases-spacing-lg);">Schedule New Hearing</h3>
    
    <form id="hearingForm" method="POST" action="<?php echo isset($hearing) ? admin_url('cases/hearings/edit/' . $hearing['id']) : admin_url('cases/hearings/add'); ?>">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        
        <!-- Case Selection with Search -->
        <div class="cases-form-group">
            <label class="cases-form-label cases-label-required">Select Case</label>
            <select name="case_id" id="case_id" class="cases-form-select" required>
                <option value="">Choose a case...</option>
            </select>
            <div class="cases-form-help">Search and select the case for which you want to schedule a hearing</div>
        </div>
        
        <!-- Case Details Preview -->
        <div id="case-preview" class="cases-case-info-box" style="display: none;">
            <div id="case-preview-content"></div>
        </div>
        
        <!-- Hearing Details -->
        <div class="cases-form-grid cases-form-grid-2">
            <div class="cases-form-group">
                <label class="cases-form-label cases-label-required">Hearing Date</label>
                <input type="date" name="date" id="date" class="cases-form-control" 
                    value="<?php echo isset($hearing['date']) ? $hearing['date'] : date('Y-m-d', strtotime('+7 days')); ?>" required>
                <div class="cases-form-help">Select the date for this hearing</div>
            </div>
            <div class="cases-form-group">
                <label class="cases-form-label cases-label-required">Hearing Time</label>
                <input type="time" name="time" id="time" class="cases-form-control" 
                    value="<?php echo isset($hearing['time']) ? $hearing['time'] : '10:00'; ?>" required>
                <div class="cases-form-help">Set the scheduled time</div>
            </div>
        </div>
        
        <div class="cases-form-group">
            <label class="cases-form-label">Purpose of Hearing</label>
            <select name="hearing_purpose" id="hearing_purpose" class="cases-form-select">
                <option value="">Select purpose or enter custom</option>
                <option value="Arguments">Arguments</option>
                <option value="Evidence">Evidence Presentation</option>
                <option value="Witness Examination">Witness Examination</option>
                <option value="Motion Hearing">Motion Hearing</option>
                <option value="Case Management">Case Management</option>
                <option value="Judgment">Judgment</option>
                <option value="Final Arguments">Final Arguments</option>
                <option value="Interim Orders">Interim Orders</option>
            </select>
            <input type="text" name="custom_purpose" id="custom_purpose" class="cases-form-control" 
                style="margin-top: 10px; display: none;" placeholder="Enter custom purpose">
        </div>
        
        <div class="cases-form-group">
            <label class="cases-form-label">Additional Notes</label>
            <textarea name="description" id="description" class="cases-form-control cases-textarea" rows="3" 
                placeholder="Any additional details or preparation notes for this hearing"><?php echo isset($hearing['description']) ? htmlspecialchars($hearing['description']) : ''; ?></textarea>
        </div>
        
        <div class="cases-form-actions">
            <button type="submit" class="cases-btn cases-btn-primary" id="submit-hearing-btn">
                <i class="fas fa-calendar-plus"></i> <?php echo isset($hearing) ? 'Update Hearing' : 'Schedule Hearing'; ?>
            </button>
            <button type="reset" class="cases-btn cases-btn-default">
                <i class="fas fa-undo"></i> Reset Form
            </button>
        </div>
    </form>
</div>

<!-- Upcoming Hearings Tab -->
<div class="tab-content" id="upcoming-tab" style="display: none;">
    <div class="cases-flex cases-flex-between cases-mb-md">
        <h3 class="cases-section-title">Upcoming Hearings</h3>
        <div class="cases-flex" style="gap: 10px;">
            <input type="text" id="upcoming-search" class="cases-form-control" 
                placeholder="Search hearings..." style="width: 250px;">
            <button class="cases-btn cases-btn-default" onclick="loadHearings()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>
    <div id="upcoming-container">
        <?php echo cases_loading_state('Loading upcoming hearings...'); ?>
    </div>
</div>

<!-- Past Hearings Tab -->
<div class="tab-content" id="past-tab" style="display: none;">
    <div class="cases-flex cases-flex-between cases-mb-md">
        <h3 class="cases-section-title">Past Hearings</h3>
        <div class="cases-flex" style="gap: 10px;">
            <input type="text" id="past-search" class="cases-form-control" 
                placeholder="Search hearings..." style="width: 250px;">
            <select id="past-status-filter" class="cases-form-select" style="width: 150px;">
                <option value="">All Status</option>
                <option value="Completed">Completed</option>
                <option value="Adjourned">Adjourned</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div id="past-container">
        <?php echo cases_loading_state('Loading past hearings...'); ?>
    </div>
</div>

<!-- All Hearings Tab -->
<div class="tab-content" id="all-tab" style="display: none;">
    <div class="cases-flex cases-flex-between cases-mb-md">
        <h3 class="cases-section-title">All Hearings</h3>
        <div class="cases-flex" style="gap: 10px;">
            <input type="text" id="all-search" class="cases-form-control" 
                placeholder="Search all hearings..." style="width: 250px;">
            <select id="all-status-filter" class="cases-form-select" style="width: 150px;">
                <option value="">All Status</option>
                <option value="Scheduled">Scheduled</option>
                <option value="Completed">Completed</option>
                <option value="Adjourned">Adjourned</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div id="all-container">
        <?php echo cases_loading_state('Loading all hearings...'); ?>
    </div>
</div>

<?php echo cases_section_end(); ?>
<?php echo cases_page_wrapper_end(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let hearingsData = [];
    let casesData = [];
    const today = new Date().toISOString().split('T')[0];
    
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
            document.getElementById(targetTab + '-tab').style.display = 'block';
        });
    });
    
    // Load initial data
    loadCases();
    loadHearings();
    
    // If editing existing hearing, populate the form
    <?php if (isset($hearing)): ?>
    window.addEventListener('load', function() {
        const caseId = '<?php echo $hearing['case_id']; ?>';
        const hearingPurpose = '<?php echo addslashes($hearing['hearing_purpose']); ?>';
        
        // Set case selection after cases are loaded
        setTimeout(function() {
            const caseSelect = document.getElementById('case_id');
            if (caseSelect) {
                caseSelect.value = caseId;
                caseSelect.dispatchEvent(new Event('change'));
            }
        }, 1000);
        
        // Set custom purpose if not in predefined options
        const purposeSelect = document.getElementById('hearing_purpose');
        const customPurpose = document.getElementById('custom_purpose');
        const predefinedOptions = ['Arguments', 'Evidence', 'Witness Examination', 'Motion Hearing', 'Case Management', 'Judgment', 'Final Arguments', 'Interim Orders'];
        
        if (purposeSelect && customPurpose) {
            if (hearingPurpose && !predefinedOptions.includes(hearingPurpose)) {
                purposeSelect.value = '';
                customPurpose.style.display = 'block';
                customPurpose.required = true;
                customPurpose.value = hearingPurpose;
            }
        }
    });
    <?php endif; ?>
    
    // Load cases for dropdown
    function loadCases() {
        fetch(admin_url + 'cases/cases_list')
            .then(response => response.json())
            .then(data => {
                casesData = data.data || [];
                populateCasesDropdown();
            })
            .catch(error => {
                console.error('Error loading cases:', error);
            });
    }
    
    function populateCasesDropdown() {
        const select = document.getElementById('case_id');
        select.innerHTML = '<option value="">Choose a case...</option>';
        
        if (casesData.length === 0) {
            select.innerHTML += '<option value="">No cases found - Create a case first</option>';
            return;
        }
        
        casesData.forEach(caseItem => {
            const optionText = `${caseItem.case_title} (#${caseItem.case_number}) - ${caseItem.client_name}`;
            select.innerHTML += `<option value="${caseItem.id}">${htmlEscape(optionText)}</option>`;
        });
    }
    
    // Case selection handler
    document.getElementById('case_id').addEventListener('change', function() {
        const caseId = this.value;
        const preview = document.getElementById('case-preview');
        const content = document.getElementById('case-preview-content');
        
        if (caseId && casesData.length > 0) {
            const selectedCase = casesData.find(c => c.id == caseId);
            if (selectedCase) {
                content.innerHTML = `
                    <div class="cases-case-title">${htmlEscape(selectedCase.case_title)}</div>
                    <div class="cases-case-number">#${htmlEscape(selectedCase.case_number)}</div>
                    <div class="cases-card-meta-grid">
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Client:</span>
                            <span class="cases-card-meta-value">${htmlEscape(selectedCase.client_name || 'N/A')}</span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Court:</span>
                            <span class="cases-card-meta-value">${htmlEscape(selectedCase.court_display || 'N/A')}</span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Filed:</span>
                            <span class="cases-card-meta-value">${selectedCase.date_filed ? formatDate(selectedCase.date_filed) : 'N/A'}</span>
                        </div>
                    </div>
                `;
                preview.style.display = 'block';
            }
        } else {
            preview.style.display = 'none';
        }
    });
    
    // Purpose dropdown handler
    document.getElementById('hearing_purpose').addEventListener('change', function() {
        const customInput = document.getElementById('custom_purpose');
        if (this.value === '') {
            customInput.style.display = 'block';
            customInput.required = true;
        } else {
            customInput.style.display = 'none';
            customInput.required = false;
            customInput.value = '';
        }
    });
    
    // Load hearings
    function loadHearings() {
        // Show loading states
        document.getElementById('upcoming-container').innerHTML = '<?php echo cases_loading_state("Loading upcoming hearings..."); ?>';
        document.getElementById('past-container').innerHTML = '<?php echo cases_loading_state("Loading past hearings..."); ?>';
        document.getElementById('all-container').innerHTML = '<?php echo cases_loading_state("Loading all hearings..."); ?>';
        
        // Fetch fresh hearings data
        fetch(admin_url + 'cases/hearings/get_hearings_data')
            .then(response => response.json())
            .then(data => {
                hearingsData = data.hearings || [];
                renderHearings();
                updateStats();
            })
            .catch(error => {
                console.error('Error loading hearings:', error);
                // Fallback to static data if AJAX fails
                const hearingsFromPHP = <?php echo json_encode(isset($hearings) ? $hearings : []); ?>;
                hearingsData = hearingsFromPHP;
                renderHearings();
                updateStats();
            });
    }
    
    function renderHearings() {
        const upcomingHearings = hearingsData.filter(h => h.date >= today);
        const pastHearings = hearingsData.filter(h => h.date < today);
        
        renderHearingsList(upcomingHearings, 'upcoming-container', true);
        renderHearingsList(pastHearings, 'past-container', false);
        renderHearingsList(hearingsData, 'all-container', null);
    }
    
    function renderHearingsList(hearings, containerId, isUpcoming) {
        const container = document.getElementById(containerId);
        
        if (!hearings || hearings.length === 0) {
            container.innerHTML = `
                <div class="cases-empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5>No hearings found</h5>
                    <p>${isUpcoming === true ? 'No upcoming hearings scheduled' : isUpcoming === false ? 'No past hearings recorded' : 'No hearings in the system'}</p>
                </div>
            `;
            return;
        }
        
        let html = '<div class="cases-table-wrapper"><table class="cases-table">';
        html += `
            <thead>
                <tr>
                    <th>Case</th>
                    <th>Date & Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        `;
        
        hearings.forEach(hearing => {
            const statusClass = getStatusClass(hearing.status);
            const isToday = hearing.date === today;
            const rowClass = isToday ? 'style="background: var(--cases-warning-bg);"' : '';
            
            html += `
                <tr ${rowClass}>
                    <td>
                        <div style="font-weight: 600;">${htmlEscape(hearing.case_title || 'Case #' + hearing.case_id)}</div>
                        ${isToday ? '<small style="color: var(--cases-warning); font-weight: 600;">TODAY</small>' : ''}
                    </td>
                    <td>
                        <div style="font-weight: 600;">${formatDate(hearing.date)}</div>
                        <small style="color: var(--cases-text-light);">${formatTime(hearing.time)}</small>
                    </td>
                    <td>${htmlEscape(hearing.hearing_purpose || hearing.description || 'Not specified')}</td>
                    <td><span class="cases-status-badge cases-status-${statusClass}">${hearing.status}</span></td>
                    <td>
                        <div class="cases-table-actions">
                            ${isUpcoming ? `
                                <a href="${admin_url}cases/hearings/quick_update/${hearing.id}" class="cases-action-btn cases-btn-success">Update</a>
                            ` : ''}
                            <a href="${admin_url}cases/hearings/edit/${hearing.id}" class="cases-action-btn cases-btn-primary">Edit</a>
                            <a href="${admin_url}cases/details?id=${hearing.case_id}" class="cases-action-btn cases-btn-default">View Case</a>
                            <a href="javascript:void(0)" class="cases-action-btn cases-btn-danger" onclick="confirmHearingDelete(${hearing.id}, '${htmlEscape(hearing.case_title || 'Case #' + hearing.case_id)}')">Delete</a>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        container.innerHTML = html;
    }
    
    function updateStats() {
        const upcoming = hearingsData.filter(h => h.date >= today).length;
        const todayCount = hearingsData.filter(h => h.date === today).length;
        const completed = hearingsData.filter(h => h.status === 'Completed').length;
        
        document.getElementById('total-hearings').textContent = hearingsData.length;
        document.getElementById('upcoming-hearings').textContent = upcoming;
        document.getElementById('today-hearings').textContent = todayCount;
        document.getElementById('completed-hearings').textContent = completed;
        
        document.getElementById('upcoming-badge').textContent = upcoming;
        document.getElementById('past-badge').textContent = hearingsData.filter(h => h.date < today).length;
        document.getElementById('all-badge').textContent = hearingsData.length;
    }
    
    function getStatusClass(status) {
        switch(status?.toLowerCase()) {
            case 'completed': return 'completed';
            case 'adjourned': return 'adjourned'; 
            case 'cancelled': return 'cancelled';
            default: return 'scheduled';
        }
    }
    
    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        try {
            return new Date(dateStr).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        } catch (e) {
            return dateStr;
        }
    }
    
    function formatTime(timeStr) {
        if (!timeStr) return 'N/A';
        try {
            return new Date('2000-01-01 ' + timeStr).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        } catch (e) {
            return timeStr;
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
    
    function showError(message) {
        console.error(message);
    }
    
    // Delete confirmation function
    function confirmHearingDelete(hearingId, caseTitle) {
        if (confirm(`Are you sure you want to delete this hearing for "${caseTitle}"?\n\nThis action cannot be undone and will remove all associated data.`)) {
            // Show loading state
            const deleteBtn = event.target;
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            deleteBtn.classList.add('disabled');
            
            // Make delete request
            fetch(`${admin_url}cases/hearings/delete/${hearingId}`, {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok) {
                    // Refresh the hearings data
                    loadHearings();
                    alert('Hearing deleted successfully!');
                } else {
                    throw new Error('Failed to delete hearing');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete hearing. Please try again.');
                
                // Restore button state
                deleteBtn.innerHTML = originalText;
                deleteBtn.classList.remove('disabled');
            });
        }
    }
    
    // Make function globally accessible
    window.confirmHearingDelete = confirmHearingDelete;
    
    // Form submission
    document.getElementById('hearingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-hearing-btn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scheduling...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        // Use custom purpose if selected
        const purposeSelect = document.getElementById('hearing_purpose');
        const customPurpose = document.getElementById('custom_purpose');
        if (purposeSelect.value === '' && customPurpose.value) {
            formData.set('hearing_purpose', customPurpose.value);
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                alert('Hearing scheduled successfully!');
                this.reset();
                document.getElementById('case-preview').style.display = 'none';
                loadHearings(); // Refresh the hearings data
                
                // Switch to upcoming tab
                document.querySelector('[data-tab="upcoming"]').click();
            } else {
                throw new Error('Failed to schedule hearing');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to schedule hearing. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Search functionality
    document.getElementById('upcoming-search').addEventListener('input', function() {
        // Implement search for upcoming hearings
    });
    
    document.getElementById('past-search').addEventListener('input', function() {
        // Implement search for past hearings
    });
    
    document.getElementById('all-search').addEventListener('input', function() {
        // Implement search for all hearings
    });
    
    // Global function for refresh button
    window.loadHearings = loadHearings;
});
</script>

<?php init_tail(); ?>