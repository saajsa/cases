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
            <a href="<?php echo site_url('cases/Cases_client'); ?>" class="cases-breadcrumb-item">
                <i class="fa fa-tachometer"></i>
                Dashboard
            </a>
            <span class="cases-breadcrumb-separator">
                <i class="fa fa-chevron-right"></i>
            </span>
            <a href="<?php echo site_url('cases/Cases_client/view/' . $parent_case['id']); ?>" class="cases-breadcrumb-item">
                <i class="fa fa-gavel"></i>
                <?php echo htmlspecialchars($parent_case['case_title']); ?>
            </a>
            <span class="cases-breadcrumb-separator">
                <i class="fa fa-chevron-right"></i>
            </span>
            <span class="cases-breadcrumb-item cases-breadcrumb-current">
                <i class="fa fa-calendar"></i>
                Hearing Details
            </span>
        </div>
        <div class="cases-breadcrumb-actions">
            <a href="<?php echo site_url('cases/Cases_client/view/' . $parent_case['id']); ?>" class="cases-btn cases-btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Case
            </a>
        </div>
    </div>
</div>

<!-- Page Header -->
<div class="cases-page-header">
    <div class="cases-flex cases-flex-between">
        <div>
            <h1>
                <i class="fa fa-calendar" style="margin-right: 12px; color: var(--cases-warning);"></i>
                Hearing Details
            </h1>
            <div class="subtitle">
                <?php echo _dt($hearing['date']); ?>
                <?php if (!empty($hearing['time'])) { ?>
                    at <?php echo $hearing['time']; ?>
                <?php } ?>
            </div>
        </div>
        <div class="cases-header-stats">
            <div class="cases-stat-item">
                <span class="cases-stat-number"><?php echo count($hearing_documents); ?></span>
                <span class="cases-stat-label">Documents</span>
            </div>
            <div class="cases-stat-item">
                <span class="cases-status-badge cases-status-<?php echo strtolower($hearing['status'] ?? 'scheduled'); ?>">
                    <?php echo htmlspecialchars($hearing['status'] ?? 'Scheduled'); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="cases-grid" style="grid-template-columns: 2fr 1fr; gap: var(--cases-spacing-lg);">
    
    <!-- Left Column: Hearing Information and Documents -->
    <div>
        <!-- Hearing Information Card -->
        <div class="cases-info-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-calendar" style="color: var(--cases-warning); margin-right: 8px;"></i>
                    Hearing Information
                </h4>
                <span class="cases-status-badge cases-status-<?php echo strtolower($hearing['status'] ?? 'scheduled'); ?>">
                    <?php echo htmlspecialchars($hearing['status'] ?? 'Scheduled'); ?>
                </span>
            </div>
            <div class="cases-info-card-body">
                <!-- Hearing Details -->
                <div class="cases-grid cases-grid-2" style="margin-bottom: var(--cases-spacing-md);">
                    <div>
                        <strong class="cases-text">Hearing Date:</strong><br>
                        <span class="cases-text-<?php echo (strtotime($hearing['date']) >= strtotime('today')) ? 'success' : 'muted'; ?> cases-font-weight-medium">
                            <?php echo _dt($hearing['date']); ?>
                        </span>
                    </div>
                    <div>
                        <strong class="cases-text">Time:</strong><br>
                        <span class="cases-text-primary cases-font-weight-medium">
                            <?php echo !empty($hearing['time']) ? $hearing['time'] : 'Not specified'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="cases-border-top" style="padding-top: var(--cases-spacing-md);"></div>
                
                <!-- Court Information -->
                <div class="cases-grid cases-grid-2" style="margin-top: var(--cases-spacing-md);">
                    <div>
                        <strong class="cases-text">Court:</strong><br>
                        <span class="cases-text-info cases-font-weight-medium">
                            <?php echo htmlspecialchars($hearing['court_name'] ?: 'Court not specified'); ?>
                        </span>
                    </div>
                    <div>
                        <strong class="cases-text">Judge:</strong><br>
                        <span class="cases-text-muted">
                            <?php echo htmlspecialchars($hearing['judge_name'] ?: 'Not assigned'); ?>
                        </span>
                    </div>
                </div>

                <!-- Hearing Purpose -->
                <?php if (!empty($hearing['hearing_purpose'])) { ?>
                <div class="cases-border-top" style="padding-top: var(--cases-spacing-md); margin-top: var(--cases-spacing-md);">
                    <strong class="cases-text">Purpose/Agenda:</strong>
                    <div style="margin-top: var(--cases-spacing-sm); padding: var(--cases-spacing-md); background: var(--cases-warning-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-warning);">
                        <div class="hearing-purpose-content">
                            <?php echo nl2br(htmlspecialchars($hearing['hearing_purpose'])); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Additional Notes -->
                <?php if (!empty($hearing['notes'])) { ?>
                <div class="cases-border-top" style="padding-top: var(--cases-spacing-md); margin-top: var(--cases-spacing-md);">
                    <strong class="cases-text">Additional Notes:</strong>
                    <div style="margin-top: var(--cases-spacing-sm); padding: var(--cases-spacing-md); background: var(--cases-bg-secondary); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-primary);">
                        <div class="hearing-notes-content">
                            <?php echo nl2br(htmlspecialchars($hearing['notes'])); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- Documents Card -->
        <div class="cases-card">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-file-text-o" style="margin-right: 8px; color: var(--cases-info);"></i>
                    Hearing Documents
                </h4>
                <span class="cases-count-badge"><?php echo count($hearing_documents); ?></span>
            </div>
            <div class="cases-card-body">
                <?php if (!empty($hearing_documents)) { ?>
                    <!-- Quick Actions Panel -->
                    <div class="cases-document-actions" style="margin-bottom: var(--cases-spacing-md); padding: var(--cases-spacing-sm); background: var(--cases-bg-secondary); border-radius: var(--cases-radius);">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--cases-spacing-sm);">
                            <div style="display: flex; align-items: center; gap: var(--cases-spacing-sm);">
                                <i class="fa fa-info-circle" style="color: var(--cases-info);"></i>
                                <span style="color: var(--cases-text); font-size: var(--cases-font-size-sm);">
                                    <?php echo count($hearing_documents); ?> documents specific to this hearing
                                </span>
                            </div>
                            <div style="display: flex; gap: var(--cases-spacing-xs);">
                                <button onclick="toggleDocumentSearch()" class="cases-btn cases-btn-sm cases-btn-info">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <button onclick="printDocumentList()" class="cases-btn cases-btn-sm">
                                    <i class="fa fa-print"></i> Print List
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search Box (Initially Hidden) -->
                        <div id="document-search-box" style="display: none; margin-top: var(--cases-spacing-sm);">
                            <input type="text" id="document-search-input" placeholder="Search hearing documents..." 
                                   style="width: 100%; padding: 8px; border: 1px solid var(--cases-border); border-radius: var(--cases-radius); font-size: var(--cases-font-size-sm);">
                        </div>
                    </div>
                    
                    <!-- Documents Grid -->
                    <div class="cases-grid cases-grid-responsive">
                        <?php foreach ($hearing_documents as $document) { ?>
                            <div class="cases-card cases-card-compact cases-hover-lift document-item" 
                                 data-filename="<?php echo strtolower($document['file_name']); ?>" 
                                 data-tag="<?php echo strtolower($document['tag'] ?? ''); ?>">
                                <div class="cases-card-header">
                                    <h5 class="cases-card-title">
                                        <i class="fa fa-file-<?php echo get_file_icon_class($document['filetype']); ?>" style="margin-right: 8px; color: var(--cases-warning);"></i>
                                        <?php echo htmlspecialchars($document['file_name']); ?>
                                    </h5>
                                </div>
                                <div class="cases-card-body">
                                    <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-sm);">
                                        <i class="fa fa-calendar"></i> <?php echo _dt($document['dateadded']); ?>
                                    </div>
                                    <?php if (!empty($document['tag'])) { ?>
                                        <div style="margin-bottom: var(--cases-spacing-sm);">
                                            <span class="cases-status-badge cases-status-warning"><?php echo htmlspecialchars($document['tag']); ?></span>
                                        </div>
                                    <?php } ?>
                                    <div style="font-size: var(--cases-font-size-xs); color: var(--cases-text-muted);">
                                        Size: <?php echo isset($document['file_size']) ? format_file_size($document['file_size']) : 'Unknown'; ?>
                                    </div>
                                </div>
                                <div class="cases-card-footer">
                                    <div style="display: flex; gap: var(--cases-spacing-xs); justify-content: flex-end;">
                                        <button onclick="previewDocument(<?php echo $document['id']; ?>, '<?php echo htmlspecialchars($document['file_name']); ?>')" 
                                                class="cases-btn cases-btn-info cases-btn-sm">
                                            <i class="fa fa-eye"></i> Preview
                                        </button>
                                        <a href="<?php echo site_url('cases/Cases_client/download_document/' . $document['id']); ?>" 
                                           class="cases-btn cases-btn-success cases-btn-sm">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <!-- Empty State -->
                    <div style="text-align: center; padding: var(--cases-spacing-xl) var(--cases-spacing-lg); background: var(--cases-bg-secondary); border: 1px solid var(--cases-border); border-radius: var(--cases-radius);">
                        <div style="margin-bottom: var(--cases-spacing-lg);">
                            <i class="fa fa-file-o" style="font-size: 3rem; color: var(--cases-warning); opacity: 0.4;"></i>
                        </div>
                        <h5 style="margin-bottom: var(--cases-spacing-sm); color: var(--cases-text); font-weight: 600;">
                            No Documents Attached
                        </h5>
                        <p style="color: var(--cases-text-muted); font-size: var(--cases-font-size-base);">
                            No documents have been attached to this hearing yet.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Sidebar -->
    <div>
        <!-- Parent Case Card -->
        <div class="cases-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-gavel" style="margin-right: 8px; color: var(--cases-primary);"></i>
                    Parent Case
                </h4>
            </div>
            <div class="cases-card-body">
                <div style="margin-bottom: var(--cases-spacing-md);">
                    <h5 style="color: var(--cases-primary); margin-bottom: var(--cases-spacing-xs);">
                        <?php echo htmlspecialchars($parent_case['case_title']); ?>
                    </h5>
                    <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-muted); margin-bottom: var(--cases-spacing-sm);">
                        Case #: <?php echo htmlspecialchars($parent_case['case_number'] ?: 'Not assigned'); ?>
                    </div>
                    <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-muted);">
                        Filed: <?php echo $parent_case['date_filed'] ? _dt($parent_case['date_filed']) : 'Not filed yet'; ?>
                    </div>
                </div>
                
                <div style="padding: var(--cases-spacing-sm); background: var(--cases-info-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-info); margin-bottom: var(--cases-spacing-md);">
                    <div style="color: var(--cases-info); font-size: var(--cases-font-size-sm);">
                        <i class="fa fa-link"></i> This hearing is part of the above case
                    </div>
                </div>
                
                <div style="display: flex; gap: var(--cases-spacing-xs);">
                    <a href="<?php echo site_url('cases/Cases_client/view/' . $parent_case['id']); ?>" 
                       class="cases-btn cases-btn-primary cases-btn-sm">
                        <i class="fa fa-eye"></i> View Case
                    </a>
                </div>
            </div>
        </div>

        <!-- Hearing Status Card -->
        <div class="cases-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-clock-o" style="margin-right: 8px; color: var(--cases-warning);"></i>
                    Hearing Status
                </h4>
            </div>
            <div class="cases-card-body">
                <?php
                $hearing_date = strtotime($hearing['date']);
                $today = strtotime('today');
                $is_upcoming = $hearing_date >= $today;
                $days_diff = ceil(($hearing_date - $today) / (60 * 60 * 24));
                ?>
                
                <div style="text-align: center; margin-bottom: var(--cases-spacing-md);">
                    <div class="cases-status-badge cases-status-<?php echo $is_upcoming ? 'active' : 'inactive'; ?>" 
                         style="font-size: var(--cases-font-size-base); padding: var(--cases-spacing-sm) var(--cases-spacing-md);">
                        <?php if ($is_upcoming) { ?>
                            <?php if ($days_diff == 0) { ?>
                                <i class="fa fa-exclamation-triangle"></i> Today
                            <?php elseif ($days_diff == 1) { ?>
                                <i class="fa fa-clock-o"></i> Tomorrow
                            <?php else { ?>
                                <i class="fa fa-calendar"></i> In <?php echo $days_diff; ?> days
                            <?php } ?>
                        <?php } else { ?>
                            <i class="fa fa-check-circle"></i> Completed
                        <?php } ?>
                    </div>
                </div>
                
                <?php if ($is_upcoming && $days_diff <= 7) { ?>
                    <div style="padding: var(--cases-spacing-sm); background: var(--cases-warning-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-warning); margin-bottom: var(--cases-spacing-md);">
                        <div style="color: var(--cases-warning); font-size: var(--cases-font-size-sm);">
                            <i class="fa fa-bell"></i> Upcoming hearing - please prepare required documents
                        </div>
                    </div>
                <?php } ?>
                
                <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-muted); text-align: center);">
                    Status: <?php echo htmlspecialchars($hearing['status'] ?? 'Scheduled'); ?>
                </div>
            </div>
        </div>
        
        <!-- Hearing Summary Card -->
        <div class="cases-stat-card">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-info-circle" style="margin-right: 8px; color: var(--cases-info);"></i>
                    Summary
                </h4>
            </div>
            <div class="cases-card-body">
                <div class="cases-grid" style="grid-template-columns: 1fr; gap: var(--cases-spacing-sm);">
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text">Documents:</strong>
                        <span class="cases-count-badge"><?php echo count($hearing_documents); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text">Status:</strong>
                        <span class="cases-status-badge cases-status-info"><?php echo htmlspecialchars($hearing['status'] ?? 'Scheduled'); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text">Date:</strong>
                        <span class="cases-text-muted cases-font-size-sm"><?php echo date('M j, Y', strtotime($hearing['date'])); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text">Time:</strong>
                        <span class="cases-text-muted cases-font-size-sm"><?php echo !empty($hearing['time']) ? $hearing['time'] : 'Not specified'; ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0;">
                        <strong class="cases-text">Purpose Available:</strong>
                        <span class="cases-text-<?php echo !empty($hearing['hearing_purpose']) ? 'success' : 'muted'; ?>">
                            <?php echo !empty($hearing['hearing_purpose']) ? 'Yes' : 'No'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Document Preview Modal -->
<div id="document-preview-modal" class="document-preview-modal" style="display: none;">
    <div class="document-preview-overlay" onclick="closeDocumentPreview()"></div>
    <div class="document-preview-content">
        <div class="document-preview-header">
            <h3 id="document-preview-title">Document Preview</h3>
            <button onclick="closeDocumentPreview()" class="document-preview-close">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="document-preview-body" id="document-preview-body">
            <!-- Preview content will be loaded here -->
        </div>
        <div class="document-preview-footer">
            <button onclick="downloadFromPreview()" class="cases-btn cases-btn-success cases-btn-sm" id="download-btn">
                <i class="fa fa-download"></i> Download
            </button>
            <button onclick="closeDocumentPreview()" class="cases-btn cases-btn-sm">
                <i class="fa fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<script>
// Global variables
let currentPreviewDocumentId = null;
let documentSearchTimeout = null;

$(document).ready(function() {
    // Add fade-in animation to cards
    $('.cases-card, .cases-info-card, .cases-stat-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
    
    // Setup document search functionality
    $('#document-search-input').on('input', function() {
        clearTimeout(documentSearchTimeout);
        const searchTerm = $(this).val().toLowerCase();
        
        documentSearchTimeout = setTimeout(function() {
            filterDocuments(searchTerm);
        }, 300);
    });
});

// Document Search Functions
function toggleDocumentSearch() {
    const searchBox = $('#document-search-box');
    if (searchBox.is(':visible')) {
        searchBox.hide();
        $('#document-search-input').val('');
        filterDocuments('');
    } else {
        searchBox.show();
        $('#document-search-input').focus();
    }
}

function filterDocuments(searchTerm) {
    $('.document-item').each(function() {
        const item = $(this);
        const filename = item.data('filename') || '';
        const tag = item.data('tag') || '';
        const text = (filename + ' ' + tag).toLowerCase();
        
        if (searchTerm === '' || text.includes(searchTerm)) {
            item.show();
        } else {
            item.hide();
        }
    });
    
    // Update visible count
    const visibleCount = $('.document-item:visible').length;
    const totalCount = $('.document-item').length;
    
    if (searchTerm !== '') {
        $('.cases-document-actions span').first().html(
            '<i class="fa fa-search"></i> ' + visibleCount + ' of ' + totalCount + ' documents match "' + searchTerm + '"'
        );
    } else {
        $('.cases-document-actions span').first().html(
            '<i class="fa fa-info-circle"></i> <?php echo count($hearing_documents); ?> documents specific to this hearing'
        );
    }
}

// Document Preview Functions
function previewDocument(documentId, fileName) {
    currentPreviewDocumentId = documentId;
    $('#document-preview-title').text('Preview: ' + fileName);
    $('#document-preview-body').html('<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin"></i> Loading preview...</div>');
    $('#document-preview-modal').show();
    
    // Show basic document info
    setTimeout(function() {
        $('#document-preview-body').html(
            '<div style="text-align: center; padding: 40px;">' +
            '<i class="fa fa-file-text-o" style="font-size: 4rem; color: var(--cases-warning); margin-bottom: 20px;"></i>' +
            '<h4>' + fileName + '</h4>' +
            '<p>Document is related to hearing on <?php echo _dt($hearing["date"]); ?></p>' +
            '<div style="margin-top: 20px;">' +
            '<a href="<?php echo site_url("cases/Cases_client/download_document/"); ?>/' + documentId + '" class="cases-btn cases-btn-primary">' +
            '<i class="fa fa-download"></i> Download to View' +
            '</a>' +
            '</div>' +
            '</div>'
        );
    }, 1000);
}

function closeDocumentPreview() {
    $('#document-preview-modal').hide();
    currentPreviewDocumentId = null;
}

function downloadFromPreview() {
    if (currentPreviewDocumentId) {
        window.location.href = '<?php echo site_url("cases/Cases_client/download_document/"); ?>/' + currentPreviewDocumentId;
    }
}

// Print Functions
function printDocumentList() {
    let content = '<h2>Hearing Documents</h2>';
    content += '<h3>Hearing on <?php echo _dt($hearing["date"]); ?><?php if (!empty($hearing["time"])) echo " at " . $hearing["time"]; ?></h3>';
    content += '<h4>Case: <?php echo htmlspecialchars($parent_case["case_title"]); ?></h4>';
    content += '<table border="1" style="width: 100%; border-collapse: collapse;">';
    content += '<tr><th>File Name</th><th>Upload Date</th><th>Tag</th></tr>';
    
    $('.document-item:visible').each(function() {
        const fileName = $(this).find('.cases-card-title').text().trim();
        const uploadDate = $(this).find('.fa-calendar').parent().text().trim();
        const tag = $(this).find('.cases-status-badge').text().trim() || 'N/A';
        content += '<tr><td>' + fileName + '</td><td>' + uploadDate + '</td><td>' + tag + '</td></tr>';
    });
    
    content += '</table>';
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(
        '<html><head><title>Hearing Document List</title>' +
        '<style>body { font-family: Arial, sans-serif; } table { width: 100%; } th, td { padding: 8px; text-align: left; }</style>' +
        '</head><body>' + content + '</body></html>'
    );
    printWindow.document.close();
    printWindow.print();
}

<?php if (!function_exists('get_file_icon_class')) { ?>
// Fallback function for file icons
function get_file_icon_class(filetype) {
    if (!filetype) return 'file-o';
    
    var type = filetype.toLowerCase();
    if (type.includes('pdf')) return 'file-pdf-o';
    if (type.includes('word') || type.includes('doc')) return 'file-word-o';
    if (type.includes('excel') || type.includes('sheet')) return 'file-excel-o';
    if (type.includes('image') || type.includes('jpg') || type.includes('png')) return 'file-image-o';
    if (type.includes('text')) return 'file-text-o';
    return 'file-o';
}
<?php } ?>
</script>

<style>
/* Document Preview Modal - Reusing from consultation view */
.document-preview-modal {
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

.document-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(2px);
}

.document-preview-content {
    position: relative;
    background: white;
    border-radius: var(--cases-radius);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    max-width: 900px;
    width: 90%;
    max-height: 85vh;
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

.document-preview-header {
    padding: var(--cases-spacing-lg);
    border-bottom: 1px solid var(--cases-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--cases-bg-primary);
    border-radius: var(--cases-radius) var(--cases-radius) 0 0;
}

.document-preview-header h3 {
    margin: 0;
    color: var(--cases-primary);
    font-size: 1.25rem;
    font-weight: 600;
}

.document-preview-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--cases-text-muted);
    cursor: pointer;
    padding: 8px;
    border-radius: var(--cases-radius);
    transition: all 0.2s ease;
}

.document-preview-close:hover {
    background: var(--cases-bg-secondary);
    color: var(--cases-text);
}

.document-preview-body {
    padding: var(--cases-spacing-lg);
    overflow-y: auto;
    flex: 1;
    min-height: 300px;
}

.document-preview-footer {
    padding: var(--cases-spacing-md) var(--cases-spacing-lg);
    border-top: 1px solid var(--cases-border-light);
    background: var(--cases-bg-primary);
    display: flex;
    justify-content: flex-end;
    gap: var(--cases-spacing-sm);
    border-radius: 0 0 var(--cases-radius) var(--cases-radius);
}

/* Search functionality styling */
#document-search-box {
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from { opacity: 0; max-height: 0; }
    to { opacity: 1; max-height: 50px; }
}

/* Enhanced fade-in animation */
.cases-fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.cases-fade-in.active {
    opacity: 1;
    transform: translateY(0);
}

/* Hearing status specific styling */
.cases-status-active {
    background: var(--cases-success-bg);
    color: var(--cases-success);
    border-color: var(--cases-success);
}

.cases-status-inactive {
    background: var(--cases-bg-secondary);
    color: var(--cases-text-muted);
    border-color: var(--cases-border);
}

.cases-status-scheduled {
    background: var(--cases-warning-bg);
    color: var(--cases-warning);
    border-color: var(--cases-warning);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .cases-grid {
        grid-template-columns: 1fr !important;
    }
    
    .cases-grid-2 {
        grid-template-columns: 1fr !important;
    }
    
    .cases-page-header .cases-flex {
        flex-direction: column;
        gap: var(--cases-spacing-md);
    }
    
    .cases-header-stats {
        display: flex;
        gap: var(--cases-spacing-md);
        align-items: center;
    }
    
    .document-preview-content {
        width: 95%;
        max-height: 90vh;
        margin: 20px;
    }
    
    .document-preview-header,
    .document-preview-body,
    .document-preview-footer {
        padding: var(--cases-spacing-md);
    }
    
    .document-preview-footer {
        flex-direction: column;
    }
}
</style>