<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Load Cases CSS framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/status-badges.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/tables.css'); ?>?v=<?php echo time(); ?>">

<div class="cases-module">

<!-- Page Header -->
<div class="cases-page-header">
    <h1>
        <i class="fa fa-folder-open" style="margin-right: 12px; color: var(--cases-primary);"></i>
        <?php echo _l('my_cases'); ?>
    </h1>
    <div class="subtitle">Your assigned legal cases</div>
</div>

<!-- Cases Content -->
<div class="cases-content-section">
    <?php if (!empty($cases)) { ?>
        <!-- Help Information -->
        <div class="cases-info-card" style="background: var(--cases-info-bg); border-left: 3px solid var(--cases-info); margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-info-card-body" style="padding: var(--cases-spacing-md);">
                <div style="display: flex; align-items: center; gap: var(--cases-spacing-sm);">
                    <i class="fa fa-info-circle" style="color: var(--cases-info); font-size: var(--cases-font-size-lg);"></i>
                    <span style="color: var(--cases-info); font-size: var(--cases-font-size-base);">
                        <?php echo _l('client_cases_help'); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Cases Table -->
        <div class="cases-table-wrapper">
            <div class="cases-table-responsive">
                <table class="cases-table cases-table-striped" id="client-cases-table">
                    <thead>
                        <tr>
                            <th><?php echo _l('case_title'); ?></th>
                            <th><?php echo _l('case_number'); ?></th>
                            <th><?php echo _l('court'); ?></th>
                            <th><?php echo _l('date_filed'); ?></th>
                            <th class="cases-text-center"><?php echo _l('documents'); ?></th>
                            <th class="cases-text-center"><?php echo _l('hearings'); ?></th>
                            <th class="cases-text-center"><?php echo _l('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cases as $case) { ?>
                            <tr>
                                <td>
                                    <div class="cases-card-subtitle">
                                        <?php echo htmlspecialchars($case['case_title']); ?>
                                    </div>
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
                                    if ($case['date_filed']) {
                                        echo '<span class="cases-text-success">' . _dt($case['date_filed']) . '</span>';
                                    } else {
                                        echo '<span class="cases-text-muted">Not filed</span>';
                                    }
                                    ?>
                                </td>
                                <td class="cases-text-center">
                                    <span class="cases-count-badge">
                                        <?php echo (int)$case['document_count']; ?>
                                    </span>
                                </td>
                                <td class="cases-text-center">
                                    <span class="cases-count-badge">
                                        <?php echo (int)$case['hearing_count']; ?>
                                    </span>
                                </td>
                                <td class="cases-text-center">
                                    <a href="<?php echo site_url('cases/Cases_client/view/' . $case['id']); ?>" 
                                       class="cases-btn cases-btn-info cases-btn-sm">
                                        <i class="fa fa-eye"></i>
                                        <?php echo _l('view_case_details'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <!-- Empty State -->
        <div style="text-align: center; padding: var(--cases-spacing-xl) var(--cases-spacing-lg); background: var(--cases-bg-primary); border: 1px solid var(--cases-border); border-radius: var(--cases-radius); box-shadow: var(--cases-shadow-sm);">
            <div style="margin-bottom: var(--cases-spacing-lg);">
                <i class="fa fa-exclamation-triangle" style="font-size: 4rem; color: var(--cases-warning); opacity: 0.4;"></i>
            </div>
            <h4 style="margin-bottom: var(--cases-spacing-md); color: var(--cases-text); font-weight: 600;">
                <?php echo _l('no_cases_assigned'); ?>
            </h4>
            <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-lg); font-size: var(--cases-font-size-base);">
                <?php echo _l('contact_lawyer_for_details'); ?>
            </p>
            <div style="padding: var(--cases-spacing-md); background: var(--cases-info-bg); border: 1px solid var(--cases-info); border-radius: var(--cases-radius); color: var(--cases-info);">
                <i class="fa fa-info-circle" style="margin-right: var(--cases-spacing-xs);"></i>
                <strong>Need Help?</strong> Contact your legal representative to get started with your cases.
            </div>
        </div>
    <?php } ?>
</div>

</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with Cases framework styling
    $('#client-cases-table').DataTable({
        "pageLength": 25,
        "order": [[3, "desc"]], // Order by date filed
        "columnDefs": [
            {
                "targets": [4, 5, 6], // Document count, hearing count, and actions columns
                "orderable": false,
                "searchable": false,
                "className": "cases-text-center"
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
        "dom": '<"cases-datatable-header"<"cases-datatable-search"f><"cases-datatable-controls"l>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"cases-datatable-footer"<"cases-datatable-info"i><"cases-datatable-pagination"p>>',
        "responsive": true
    });
    
    // Add fade-in animation to table
    $('.cases-table-wrapper').addClass('cases-fade-in');
    setTimeout(() => {
        $('.cases-table-wrapper').addClass('active');
    }, 200);
});
</script>

<style>
/* DataTable integration with Cases framework */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: var(--cases-text);
    font-size: var(--cases-font-size-sm);
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--cases-border);
    border-radius: var(--cases-radius);
    padding: 6px var(--cases-spacing-sm);
    font-size: var(--cases-font-size-sm);
    background: var(--cases-bg-primary);
    color: var(--cases-text);
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin: 0 2px;
    border: 1px solid var(--cases-border);
    background: var(--cases-bg-primary);
    color: var(--cases-text) !important;
    border-radius: var(--cases-radius);
    font-size: var(--cases-font-size-sm);
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--cases-bg-hover) !important;
    border-color: var(--cases-border-dark);
    color: var(--cases-text) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--cases-primary) !important;
    color: #ffffff !important;
    border-color: var(--cases-primary);
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

/* Mobile responsive enhancements */
@media (max-width: 768px) {
    .cases-page-header h1 {
        font-size: 1.8rem;
    }
    
    .cases-datatable-header {
        flex-direction: column;
        gap: var(--cases-spacing-sm);
    }
    
    .cases-datatable-footer {
        flex-direction: column;
        gap: var(--cases-spacing-sm);
        text-align: center;
    }
}
</style>