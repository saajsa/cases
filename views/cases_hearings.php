<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Load Cases CSS framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/status-badges.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/tables.css'); ?>?v=<?php echo time(); ?>">

<div class="cases-module">

<!-- Navigation Breadcrumb -->
<div class="cases-breadcrumb">
    <div class="cases-breadcrumb-container">
        <div class="cases-breadcrumb-items">
            <a href="<?php echo site_url('cases/c'); ?>" class="cases-breadcrumb-item">
                <i class="fa fa-tachometer"></i>
                Dashboard
            </a>
            <span class="cases-breadcrumb-separator">
                <i class="fa fa-chevron-right"></i>
            </span>
            <span class="cases-breadcrumb-item cases-breadcrumb-current">
                <i class="fa fa-balance-scale"></i>
                Cases & Hearings
            </span>
        </div>
        <div class="cases-breadcrumb-actions">
            <div class="cases-quick-nav">
                <a href="<?php echo site_url('cases/c'); ?>" class="cases-quick-nav-item" title="Back to Dashboard">
                    <i class="fa fa-tachometer"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo site_url('cases/c/consultations'); ?>" class="cases-quick-nav-item" title="View Consultations">
                    <i class="fa fa-comments"></i>
                    <span>Consultations</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page Header -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <div class="cases-flex cases-flex-between">
            <div>
                <h2 class="cases-section-title">
                    Your Cases & Hearings
                </h2>
                <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                    Detailed view of legal cases, court schedules, and related documents
                </div>
            </div>
            <div class="cases-header-stats">
                <div class="cases-stat-item">
                    <span class="cases-stat-number"><?php echo count($cases); ?></span>
                    <span class="cases-stat-label">Active Cases</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Search & Filter -->
    <div class="cases-search-section" style="margin-top: var(--cases-spacing-lg);">
        <div class="cases-flex cases-flex-between">
            <div style="flex: 1; max-width: 400px;">
                <div style="position: relative;">
                    <input type="text" 
                           id="case-search" 
                           placeholder="Search cases by title, number, or court..."
                           style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid var(--cases-border); border-radius: var(--cases-radius); font-size: var(--cases-font-size-sm);">
                    <i class="fa fa-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--cases-text-muted);"></i>
                </div>
            </div>
            <div class="cases-quick-actions">
                <button id="show-all" class="cases-quick-action-btn cases-active" onclick="filterCases('all')">
                    <i class="fa fa-list"></i> All Cases
                </button>
                <button id="show-with-hearings" class="cases-quick-action-btn" onclick="filterCases('hearings')">
                    <i class="fa fa-calendar"></i> With Hearings
                </button>
                <button id="show-with-documents" class="cases-quick-action-btn" onclick="filterCases('documents')">
                    <i class="fa fa-file-text-o"></i> With Documents
                </button>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($cases)) { ?>
    <!-- Cases with Details -->
    <?php foreach ($cases as $case) { ?>
        <div class="cases-content-section">
            <div class="cases-card cases-card-large">
                <!-- Case Header -->
                <div class="cases-card-header">
                    <div>
                        <h3 class="cases-card-title">
                            <?php echo htmlspecialchars($case['case_title']); ?>
                        </h3>
                        <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                            <span class="cases-status-badge cases-status-consultation">
                                Case #<?php echo htmlspecialchars($case['case_number'] ?: 'TBD'); ?>
                            </span>
                            <span class="cases-status-badge cases-status-info">
                                ID: <?php echo $case['id']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="cases-quick-actions">
                        <a href="<?php echo site_url('cases/c/documents/' . $case['id']); ?>" 
                           class="cases-quick-action-btn" title="View all documents for this case">
                            <i class="fa fa-file-text-o"></i> Documents
                        </a>
                        <a href="<?php echo site_url('cases/c/consultations'); ?>" 
                           class="cases-quick-action-btn" title="View related consultations">
                            <i class="fa fa-comments"></i> Consultations
                        </a>
                        <a href="<?php echo site_url('cases/c'); ?>" 
                           class="cases-quick-action-btn" title="Back to dashboard">
                            <i class="fa fa-tachometer"></i> Dashboard
                        </a>
                    </div>
                </div>

                <!-- Case Information -->
                <div class="cases-card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--cases-spacing-lg); margin-bottom: var(--cases-spacing-lg);">
                        <!-- Court Information -->
                        <div>
                            <h4 style="margin-bottom: var(--cases-spacing-sm); color: var(--cases-primary);">Court Information</h4>
                            <div style="color: var(--cases-text);">
                                <?php echo htmlspecialchars($case['court_display'] ?: 'Court not specified'); ?>
                            </div>
                        </div>
                        
                        <!-- Filing Date -->
                        <div>
                            <h4 style="margin-bottom: var(--cases-spacing-sm); color: var(--cases-primary);">Filing Date</h4>
                            <div style="color: var(--cases-text);">
                                <?php 
                                if ($case['date_filed']) {
                                    echo date('M j, Y', strtotime($case['date_filed']));
                                } else {
                                    echo 'Not filed yet';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Case Created -->
                        <div>
                            <h4 style="margin-bottom: var(--cases-spacing-sm); color: var(--cases-primary);">Case Created</h4>
                            <div style="color: var(--cases-text);">
                                <?php echo date('M j, Y', strtotime($case['date_created'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Hearings Section -->
                    <?php if (!empty($case_hearings[$case['id']])) { ?>
                        <div style="margin-bottom: var(--cases-spacing-lg);">
                            <h4 style="margin-bottom: var(--cases-spacing-md); color: var(--cases-primary);">Hearings</h4>
                            <div class="cases-table-wrapper">
                                <table class="cases-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($case_hearings[$case['id']] as $hearing) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo date('M j, Y', strtotime($hearing['date'])); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo !empty($hearing['time']) ? date('g:i A', strtotime($hearing['time'])) : 'Not specified'; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($hearing['purpose'] ?: 'General hearing'); ?>
                                                </td>
                                                <td>
                                                    <span class="cases-status-badge cases-status-<?php echo $hearing['status'] ?: 'scheduled'; ?>">
                                                        <?php echo ucfirst($hearing['status'] ?: 'Scheduled'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($hearing['notes'])) { ?>
                                                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                            <?php echo htmlspecialchars(substr($hearing['notes'], 0, 100)) . (strlen($hearing['notes']) > 100 ? '...' : ''); ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span style="color: var(--cases-text-muted);">No notes</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div style="margin-bottom: var(--cases-spacing-lg);">
                            <h4 style="margin-bottom: var(--cases-spacing-md); color: var(--cases-primary);">Hearings</h4>
                            <div style="text-align: center; padding: 20px; background: var(--cases-bg-secondary); border-radius: var(--cases-radius); color: var(--cases-text-muted);">
                                No hearings scheduled for this case
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Documents Section -->
                    <?php if (!empty($case_documents[$case['id']])) { ?>
                        <div style="margin-bottom: var(--cases-spacing-lg);">
                            <h4 style="margin-bottom: var(--cases-spacing-md); color: var(--cases-primary);">Case Documents</h4>
                            <div class="cases-grid cases-grid-responsive">
                                <?php foreach (array_slice($case_documents[$case['id']], 0, 3) as $document) { ?>
                                    <div class="cases-card cases-card-sm">
                                        <div class="cases-card-body">
                                            <div style="margin-bottom: var(--cases-spacing-sm);">
                                                <strong><?php echo htmlspecialchars($document['file_name']); ?></strong>
                                            </div>
                                            <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-sm);">
                                                <?php echo date('M j, Y', strtotime($document['dateadded'])); ?>
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="<?php echo site_url('cases/c/view_document/' . $document['id']); ?>" 
                                                   class="cases-btn cases-btn-info cases-btn-xs" target="_blank">
                                                    <i class="fa fa-eye"></i>
                                                    View
                                                </a>
                                                <a href="<?php echo site_url('cases/c/download/' . $document['id']); ?>" 
                                                   class="cases-btn cases-btn-primary cases-btn-xs">
                                                    <i class="fa fa-download"></i>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <?php if (count($case_documents[$case['id']]) > 3) { ?>
                                    <div class="cases-card cases-card-sm" style="display: flex; align-items: center; justify-content: center;">
                                        <div style="text-align: center;">
                                            <div style="margin-bottom: var(--cases-spacing-sm); color: var(--cases-text-muted);">
                                                +<?php echo count($case_documents[$case['id']]) - 3; ?> more documents
                                            </div>
                                            <a href="<?php echo site_url('cases/c/documents/' . $case['id']); ?>" 
                                               class="cases-btn cases-btn-sm">
                                                <i class="fa fa-th-list"></i>
                                                View All
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div style="margin-bottom: var(--cases-spacing-lg);">
                            <h4 style="margin-bottom: var(--cases-spacing-md); color: var(--cases-primary);">Case Documents</h4>
                            <div style="text-align: center; padding: 20px; background: var(--cases-bg-secondary); border-radius: var(--cases-radius); color: var(--cases-text-muted);">
                                No documents available for this case
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

<?php } else { ?>
    <!-- Empty State -->
    <div class="cases-content-section">
        <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 30px;">
            </div>
            <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                No Cases Assigned
            </h4>
            <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                You don't have any cases assigned to you yet.
            </p>
            <div style="padding: 20px; background: #eff8ff; border: 1px solid #1a6bcc; border-radius: 2px; color: #1a6bcc;">
                <strong>Need Help?</strong> Contact your legal representative to get started with your cases.
            </div>
        </div>
    </div>
<?php } ?>

</div>

<script>
$(document).ready(function() {
    // Add fade-in animation to cards
    $('.cases-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
    
    // Initialize DataTables for hearing tables
    $('.cases-table').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]], // Order by date
        "columnDefs": [
            {
                "targets": [3, 4], // Status and notes columns
                "orderable": false
            }
        ],
        "language": {
            "emptyTable": "No hearings found for this case",
            "info": "Showing _START_ to _END_ of _TOTAL_ hearings",
            "infoEmpty": "Showing 0 to 0 of 0 hearings",
            "lengthMenu": "Show _MENU_ hearings per page",
            "search": "Search hearings:",
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
});
</script>

<style>
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

/* Large card styling */
.cases-card-large {
    margin-bottom: var(--cases-spacing-xl);
}

.cases-card-large .cases-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: var(--cases-spacing-md);
}

.cases-card-large .cases-card-body {
    padding: var(--cases-spacing-lg);
}

/* Small card styling */
.cases-card-sm {
    border: 1px solid var(--cases-border-light);
    border-radius: var(--cases-radius);
    background: var(--cases-bg-primary);
}

.cases-card-sm .cases-card-body {
    padding: var(--cases-spacing-md);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .cases-card-large .cases-card-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cases-grid-responsive {
        grid-template-columns: 1fr;
    }
    
    .cases-table-wrapper {
        overflow-x: auto;
    }
}

/* Enhanced hover effects */
.cases-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border-color: var(--cases-border-dark);
}
</style>