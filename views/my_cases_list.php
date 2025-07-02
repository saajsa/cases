<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Load Cases CSS framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/status-badges.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/tables.css'); ?>?v=<?php echo time(); ?>">

<div class="cases-module">

<!-- Summary Cards Row -->
<div class="cases-grid cases-grid-responsive" style="margin-bottom: var(--cases-spacing-xl);">
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-briefcase" style="color: var(--cases-info); margin-right: 8px;"></i>
                Total Cases
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo count($cases); ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                Cases assigned to you
            </div>
        </div>
    </div>
    
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-file-text" style="color: var(--cases-success); margin-right: 8px;"></i>
                Documents
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php 
                $total_docs = 0;
                foreach ($cases as $case) {
                    $total_docs += (int)($case['document_count'] ?? 0);
                }
                echo $total_docs;
                ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                Total documents across all cases
            </div>
        </div>
    </div>
    
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-gavel" style="color: var(--cases-warning); margin-right: 8px;"></i>
                Hearings
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php 
                $total_hearings = 0;
                foreach ($cases as $case) {
                    $total_hearings += (int)($case['hearing_count'] ?? 0);
                }
                echo $total_hearings;
                ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                Scheduled hearings
            </div>
        </div>
    </div>
</div>

<!-- Cases Section -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-balance-scale" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Your Legal Cases
        </h3>
    </div>

    <?php if (!empty($cases)) { ?>
        <!-- Cases Grid Layout -->
        <div class="cases-grid cases-grid-responsive">
            <?php foreach ($cases as $case) { ?>
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div>
                            <h4 class="cases-card-title">
                                <?php echo htmlspecialchars($case['case_title']); ?>
                            </h4>
                            <div style="margin-top: 8px;">
                                <span class="cases-status-badge cases-status-consultation">
                                    <?php echo htmlspecialchars($case['case_number'] ?: 'No Number'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cases-card-body">
                        <!-- Court Information - Full Width -->
                        <div style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light); margin-bottom: var(--cases-spacing-md);">
                            <span class="cases-card-meta-label" style="display: block; margin-bottom: 4px;">
                                <i class="fa fa-university" style="margin-right: 6px; color: var(--cases-text-muted);"></i>
                                Court:
                            </span>
                            <span class="cases-card-meta-value" style="font-size: var(--cases-font-size-base); color: var(--cases-primary); font-weight: 500;">
                                <?php echo htmlspecialchars($case['court_display'] ?: 'Not specified'); ?>
                            </span>
                        </div>
                        
                        <!-- Next Date Information -->
                        <div style="padding: var(--cases-spacing-xs) 0;">
                            <span class="cases-card-meta-label">
                                <i class="fa fa-calendar" style="margin-right: 6px; color: var(--cases-text-muted);"></i>
                                Next Date:
                            </span>
                            <span class="cases-card-meta-value">
                                <?php 
                                if ($case['next_hearing_date']) {
                                    echo '<span style="color: var(--cases-success);">' . date('M j, Y', strtotime($case['next_hearing_date'])) . '</span>';
                                } else {
                                    echo '<span style="color: var(--cases-text-muted);">Not scheduled</span>';
                                }
                                ?>
                            </span>
                        </div>

                        <!-- Case Statistics -->
                        <div style="display: flex; gap: var(--cases-spacing-md); margin-top: var(--cases-spacing-md);">
                            <div style="flex: 1; text-align: center; padding: var(--cases-spacing-sm); background: var(--cases-bg-secondary); border-radius: var(--cases-radius); border: 1px solid var(--cases-border-light);">
                                <div style="font-size: 1.5rem; font-weight: 600; color: var(--cases-info); line-height: 1;">
                                    <?php echo (int)($case['document_count'] ?? 0); ?>
                                </div>
                                <div style="font-size: var(--cases-font-size-xs); color: var(--cases-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Documents
                                </div>
                            </div>
                            
                            <div style="flex: 1; text-align: center; padding: var(--cases-spacing-sm); background: var(--cases-bg-secondary); border-radius: var(--cases-radius); border: 1px solid var(--cases-border-light);">
                                <div style="font-size: 1.5rem; font-weight: 600; color: var(--cases-warning); line-height: 1;">
                                    <?php echo (int)($case['hearing_count'] ?? 0); ?>
                                </div>
                                <div style="font-size: var(--cases-font-size-xs); color: var(--cases-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Hearings
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cases-card-footer">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <small style="color: var(--cases-text-muted); font-size: var(--cases-font-size-xs);">
                                <i class="fa fa-calendar-check-o" style="margin-right: 6px;"></i>
                                Filed: <?php 
                                if ($case['date_filed']) {
                                    echo date('M j, Y', strtotime($case['date_filed']));
                                } else {
                                    echo 'Not filed yet';
                                }
                                ?>
                            </small>
                            
                            <a href="<?php echo site_url('cases/c/documents/' . $case['id']); ?>" 
                               class="cases-btn cases-btn-info cases-btn-sm">
                                <i class="fa fa-file-text"></i>
                                View Documents
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Alternative Table View Toggle -->
        <div style="margin-top: var(--cases-spacing-xl); padding-top: var(--cases-spacing-lg); border-top: 1px solid var(--cases-border-light);">
            <div style="display: flex; justify-content: center;">
                <button onclick="toggleView()" class="cases-btn cases-btn-sm" id="view-toggle-btn">
                    <i class="fa fa-table"></i>
                    Switch to Table View
                </button>
            </div>
        </div>

        <!-- Table View (Initially Hidden) -->
        <div id="table-view" style="display: none; margin-top: var(--cases-spacing-lg);">
            <div class="cases-table-wrapper">
                <div class="cases-table-responsive">
                    <table class="cases-table" id="client-cases-table">
                        <thead>
                            <tr>
                                <th>Case Title</th>
                                <th>Case Number</th>
                                <th>Court</th>
                                <th>Next Date</th>
                                <th class="cases-text-center">Documents</th>
                                <th class="cases-text-center">Hearings</th>
                                <th class="cases-text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cases as $case) { ?>
                                <tr>
                                    <td>
                                        <div class="cases-card-subtitle"><?php echo htmlspecialchars($case['case_title']); ?></div>
                                    </td>
                                    <td>
                                        <span class="cases-status-badge cases-status-consultation">
                                            <?php echo htmlspecialchars($case['case_number'] ?: 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="cases-text-light cases-font-size-sm">
                                            <?php echo htmlspecialchars($case['court_display'] ?: 'Court not specified'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($case['next_hearing_date']) {
                                            echo '<span class="cases-text-success">' . date('M j, Y', strtotime($case['next_hearing_date'])) . '</span>';
                                        } else {
                                            echo '<span class="cases-text-muted">Not scheduled</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="cases-text-center">
                                        <span class="cases-count-badge">
                                            <?php echo (int)($case['document_count'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="cases-text-center">
                                        <span class="cases-count-badge">
                                            <?php echo (int)($case['hearing_count'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="cases-text-center">
                                        <a href="<?php echo site_url('cases/c/documents/' . $case['id']); ?>" 
                                           class="cases-action-btn cases-btn-info">
                                            <i class="fa fa-file-text"></i> Documents
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php } ?>
</div>

<!-- Client Documents Section -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-folder-open" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Your Documents
        </h3>
        <?php if (!empty($client_documents)) { ?>
        <div style="display: flex; gap: var(--cases-spacing-sm);">
            <a href="<?php echo site_url('cases/c/all_documents'); ?>" class="cases-btn cases-btn-sm">
                <i class="fa fa-th-list"></i>
                View All Documents
            </a>
        </div>
        <?php } ?>
    </div>

    <?php if (!empty($client_documents)) { ?>
        <div class="cases-grid cases-grid-responsive">
            <?php foreach ($client_documents as $document) { ?>
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div>
                            <h4 class="cases-card-title">
                                <?php echo htmlspecialchars($document['file_name'] ?? 'Untitled Document'); ?>
                            </h4>
                            <div style="margin-top: 8px;">
                                <span class="cases-status-badge cases-status-info">
                                    <?php echo strtoupper(pathinfo($document['file_name'], PATHINFO_EXTENSION)); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cases-card-body">
                        <?php if (!empty($document['description'])) { ?>
                        <div style="color: var(--cases-text); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-md);">
                            <?php echo htmlspecialchars($document['description']); ?>
                        </div>
                        <?php } ?>
                        
                        <!-- Document Tags -->
                        <div style="margin-bottom: var(--cases-spacing-md);">
                            <?php
                            $tags = ['Client Document'];
                            
                            if (!empty($document['subject'])) {
                                $tags[] = $document['subject'];
                            }
                            
                            if (!empty($document['tags'])) {
                                $document_tags = explode(',', $document['tags']);
                                foreach ($document_tags as $tag) {
                                    $tags[] = trim($tag);
                                }
                            }
                            
                            foreach ($tags as $index => $tag) {
                                if ($index > 0) echo ' ';
                                echo '<span class="cases-status-badge cases-status-info" style="margin-right: 4px; margin-bottom: 4px;">';
                                echo htmlspecialchars($tag);
                                echo '</span>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="cases-card-footer">
                        <div style="display: flex; justify-content: flex-end; align-items: center;">
                            <div style="display: flex; gap: 8px;">
                                <a href="<?php echo site_url('cases/c/view_document/' . $document['id']); ?>" 
                                   class="cases-btn cases-btn-info cases-btn-sm"
                                   target="_blank">
                                    <i class="fa fa-eye"></i>
                                    View
                                </a>
                                <a href="<?php echo site_url('cases/c/download/' . $document['id']); ?>" 
                                   class="cases-btn cases-btn-primary cases-btn-sm">
                                    <i class="fa fa-download"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 30px;">
                <i class="fa fa-folder-open" style="font-size: 4rem; color: #999999; opacity: 0.4;"></i>
            </div>
            <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                No Documents Available
            </h4>
            <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                No general documents have been uploaded to your account yet.
            </p>
        </div>
    <?php } ?>
</div>

<!-- Consultations Section -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-comments" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Your Consultations
        </h3>
        <?php if (!empty($consultations)) { ?>
        <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
            Recent consultation records
        </div>
        <?php } ?>
    </div>

    <?php if (!empty($consultations)) { ?>
        <div class="cases-grid cases-grid-responsive">
            <?php foreach ($consultations as $consultation) { ?>
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div>
                            <h4 class="cases-card-title">
                                <?php echo htmlspecialchars($consultation['tag'] ?: 'Consultation'); ?>
                            </h4>
                            <div style="margin-top: 8px;">
                                <span class="cases-status-badge cases-status-<?php echo $consultation['phase'] == 'litigation' ? 'scheduled' : 'consultation'; ?>">
                                    <?php echo ucfirst($consultation['phase'] ?: 'Consultation'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cases-card-body">
                        <div style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-sm);">
                            <i class="fa fa-calendar" style="margin-right: 6px;"></i>
                            <?php echo date('M j, Y g:i A', strtotime($consultation['date_added'])); ?>
                        </div>
                        
                        <div style="color: var(--cases-text); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-md); max-height: 60px; overflow: hidden;">
                            <?php 
                            // Strip HTML tags and show plain text preview
                            $note = strip_tags($consultation['note']);
                            $note = htmlspecialchars($note);
                            echo strlen($note) > 150 ? substr($note, 0, 150) . '...' : $note;
                            ?>
                        </div>
                        
                        <!-- Notes Preview Link -->
                        <?php if (!empty($consultation['note'])) { ?>
                        <div style="margin-bottom: var(--cases-spacing-md);">
                            <button onclick="showConsultationModal(<?php echo $consultation['id']; ?>)" 
                                    class="cases-btn cases-btn-sm cases-btn-info" 
                                    style="font-size: var(--cases-font-size-xs);">
                                <i class="fa fa-eye"></i>
                                Read Full Notes
                            </button>
                        </div>
                        <?php } ?>

                        <!-- Consultation Statistics -->
                        <div style="display: flex; gap: var(--cases-spacing-md);">
                            <div style="flex: 1; text-align: center; padding: var(--cases-spacing-sm); background: var(--cases-bg-secondary); border-radius: var(--cases-radius); border: 1px solid var(--cases-border-light);">
                                <div style="font-size: 1.2rem; font-weight: 600; color: var(--cases-info); line-height: 1;">
                                    <?php echo (int)($consultation['document_count'] ?? 0); ?>
                                </div>
                                <div style="font-size: var(--cases-font-size-xs); color: var(--cases-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Documents
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cases-card-footer">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <small style="color: var(--cases-text-muted); font-size: var(--cases-font-size-xs);">
                                <i class="fa fa-user" style="margin-right: 6px;"></i>
                                ID: <?php echo $consultation['id']; ?>
                            </small>
                            
                            <a href="<?php echo site_url('cases/c/consultation/' . $consultation['id']); ?>" 
                               class="cases-btn cases-btn-info cases-btn-sm">
                                <i class="fa fa-eye"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 30px;">
                <i class="fa fa-comments" style="font-size: 4rem; color: #999999; opacity: 0.4;"></i>
            </div>
            <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                No Consultations Available
            </h4>
            <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                No consultation records have been created yet.
            </p>
        </div>
    <?php } ?>
</div>

    <?php if (empty($cases)) { ?>
        <!-- Enhanced Empty State -->
        <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 30px;">
                <i class="fa fa-balance-scale" style="font-size: 4rem; color: #999999; opacity: 0.4;"></i>
            </div>
            <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                No Cases Assigned
            </h4>
            <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                Contact your lawyer for case details
            </p>
            <div style="padding: 20px; background: #eff8ff; border: 1px solid #1a6bcc; border-radius: 2px; color: #1a6bcc;">
                <i class="fa fa-info-circle" style="margin-right: 8px;"></i>
                <strong>Need Help?</strong> Contact your legal representative to get started with your cases.
            </div>
        </div>
    <?php } ?>
</div>

</div>

<!-- Consultation Notes Modal -->
<div id="consultation-modal" class="consultation-modal" style="display: none;">
    <div class="consultation-modal-overlay" onclick="closeConsultationModal()"></div>
    <div class="consultation-modal-content">
        <div class="consultation-modal-header">
            <h3 id="consultation-modal-title">Consultation Notes</h3>
            <button onclick="closeConsultationModal()" class="consultation-modal-close">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="consultation-modal-meta">
            <span id="consultation-modal-date"></span>
        </div>
        <div class="consultation-modal-body" id="consultation-modal-body">
            <!-- Content will be loaded here -->
        </div>
        <div class="consultation-modal-footer">
            <button onclick="printConsultationNotes()" class="cases-btn cases-btn-sm">
                <i class="fa fa-print"></i>
                Print
            </button>
            <button onclick="closeConsultationModal()" class="cases-btn cases-btn-sm">
                <i class="fa fa-times"></i>
                Close
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable for table view
    $('#client-cases-table').DataTable({
        "pageLength": 25,
        "order": [[3, "desc"]], // Order by date filed
        "columnDefs": [
            {
                "targets": [4, 5, 6], // Document count, hearing count, and actions columns
                "orderable": false,
                "searchable": false,
                "className": "text-center"
            }
        ],
        "language": {
            "emptyTable": "<?php echo _l('no_cases_assigned'); ?>",
            "info": "Showing _START_ to _END_ of _TOTAL_ cases",
            "infoEmpty": "Showing 0 to 0 of 0 cases",
            "lengthMenu": "Show _MENU_ cases per page",
            "search": "Search cases:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-5"i><"col-sm-7"p>>',
        "responsive": true
    });
    
    // Add fade-in animation to cards
    $('.cases-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
});

// Toggle between card and table view
function toggleView() {
    const cardView = $('.cases-grid');
    const tableView = $('#table-view');
    const toggleBtn = $('#view-toggle-btn');
    
    if (tableView.is(':visible')) {
        // Switch to card view
        tableView.hide();
        cardView.show();
        toggleBtn.html('<i class="fa fa-table"></i> Switch to Table View');
    } else {
        // Switch to table view
        cardView.hide();
        tableView.show();
        toggleBtn.html('<i class="fa fa-th-large"></i> Switch to Card View');
        
        // Reinitialize DataTable if needed
        if (!$.fn.DataTable.isDataTable('#client-cases-table')) {
            $('#client-cases-table').DataTable();
        }
    }
}

// Consultation Modal Functions
function showConsultationModal(consultationId) {
    // Show loading state
    $('#consultation-modal-body').html('<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin"></i> Loading consultation notes...</div>');
    $('#consultation-modal').show();
    
    // Fetch consultation details via AJAX
    $.ajax({
        url: '<?php echo site_url("cases/c/get_consultation_notes"); ?>',
        type: 'POST',
        data: { consultation_id: consultationId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#consultation-modal-title').text(response.consultation.tag || 'Consultation Notes');
                $('#consultation-modal-date').text('Date: ' + response.consultation.formatted_date);
                $('#consultation-modal-body').html(response.consultation.formatted_note);
            } else {
                $('#consultation-modal-body').html('<div style="color: var(--cases-danger); text-align: center;">Error loading consultation notes.</div>');
            }
        },
        error: function() {
            $('#consultation-modal-body').html('<div style="color: var(--cases-danger); text-align: center;">Error loading consultation notes.</div>');
        }
    });
}

function closeConsultationModal() {
    $('#consultation-modal').hide();
}

function printConsultationNotes() {
    const title = $('#consultation-modal-title').text();
    const date = $('#consultation-modal-date').text();
    const content = $('#consultation-modal-body').html();
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>${title}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
                h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .date { color: #666; margin-bottom: 20px; }
                .content { margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>${title}</h1>
            <div class="date">${date}</div>
            <div class="content">${content}</div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>

<style>
/* Additional custom styling for enhanced visual appeal */
.cases-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: var(--cases-border-dark);
}

.cases-card-meta-grid {
    gap: var(--cases-spacing-md);
}

.cases-card-meta-item {
    padding: var(--cases-spacing-xs) 0;
    border-bottom: 1px solid var(--cases-border-light);
}

.cases-card-meta-item:last-child {
    border-bottom: none;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .cases-grid-responsive {
        grid-template-columns: 1fr;
    }
    
    .cases-card-meta-grid {
        grid-template-columns: 1fr;
    }
    
    .cases-card-actions {
        flex-direction: column;
        gap: var(--cases-spacing-xs);
    }
}

/* Enhanced loading animation */
.cases-fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.cases-fade-in.active {
    opacity: 1;
    transform: translateY(0);
}

/* Consultation Modal Styles */
.consultation-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.consultation-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(2px);
}

.consultation-modal-content {
    position: relative;
    background: white;
    border-radius: var(--cases-radius);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.consultation-modal-header {
    padding: var(--cases-spacing-lg);
    border-bottom: 1px solid var(--cases-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--cases-bg-primary);
    border-radius: var(--cases-radius) var(--cases-radius) 0 0;
}

.consultation-modal-header h3 {
    margin: 0;
    color: var(--cases-primary);
    font-size: 1.25rem;
    font-weight: 600;
}

.consultation-modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--cases-text-muted);
    cursor: pointer;
    padding: 8px;
    border-radius: var(--cases-radius);
    transition: all 0.2s ease;
}

.consultation-modal-close:hover {
    background: var(--cases-bg-secondary);
    color: var(--cases-text);
}

.consultation-modal-meta {
    padding: var(--cases-spacing-md) var(--cases-spacing-lg);
    background: var(--cases-bg-secondary);
    color: var(--cases-text-muted);
    font-size: var(--cases-font-size-sm);
    border-bottom: 1px solid var(--cases-border-light);
}

.consultation-modal-body {
    padding: var(--cases-spacing-lg);
    overflow-y: auto;
    flex: 1;
    line-height: 1.6;
    color: var(--cases-text);
}

.consultation-modal-body p {
    margin-bottom: var(--cases-spacing-md);
}

.consultation-modal-body h1,
.consultation-modal-body h2,
.consultation-modal-body h3 {
    color: var(--cases-primary);
    margin-top: var(--cases-spacing-lg);
    margin-bottom: var(--cases-spacing-md);
}

.consultation-modal-body ul,
.consultation-modal-body ol {
    margin-left: var(--cases-spacing-lg);
    margin-bottom: var(--cases-spacing-md);
}

.consultation-modal-footer {
    padding: var(--cases-spacing-md) var(--cases-spacing-lg);
    border-top: 1px solid var(--cases-border-light);
    background: var(--cases-bg-primary);
    display: flex;
    justify-content: flex-end;
    gap: var(--cases-spacing-sm);
    border-radius: 0 0 var(--cases-radius) var(--cases-radius);
}

/* Mobile responsiveness for modal */
@media (max-width: 768px) {
    .consultation-modal-content {
        width: 95%;
        max-height: 90vh;
        margin: 20px;
    }
    
    .consultation-modal-header,
    .consultation-modal-body,
    .consultation-modal-footer {
        padding: var(--cases-spacing-md);
    }
    
    .consultation-modal-footer {
        flex-direction: column;
    }
}
</style>