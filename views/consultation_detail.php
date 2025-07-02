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
            <a href="<?php echo site_url('cases/Cases_client/consultations'); ?>" class="cases-breadcrumb-item">
                <i class="fa fa-comments"></i>
                Consultations
            </a>
            <span class="cases-breadcrumb-separator">
                <i class="fa fa-chevron-right"></i>
            </span>
            <span class="cases-breadcrumb-item cases-breadcrumb-current">
                <i class="fa fa-eye"></i>
                Consultation Details
            </span>
        </div>
        <div class="cases-breadcrumb-actions">
            <a href="<?php echo site_url('cases/Cases_client/consultations'); ?>" class="cases-btn cases-btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Consultations
            </a>
        </div>
    </div>
</div>

<!-- Page Header -->
<div class="cases-page-header">
    <div class="cases-flex cases-flex-between">
        <div>
            <h1>
                <i class="fa fa-comments" style="margin-right: 12px; color: var(--cases-primary);"></i>
                Consultation Details
            </h1>
            <div class="subtitle">
                <?php echo htmlspecialchars($consultation['tag'] ?: 'Consultation #' . $consultation['id']); ?>
            </div>
        </div>
        <div class="cases-header-stats">
            <div class="cases-stat-item">
                <span class="cases-stat-number"><?php echo count($consultation_documents); ?></span>
                <span class="cases-stat-label">Documents</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="cases-grid" style="grid-template-columns: 2fr 1fr; gap: var(--cases-spacing-lg);">
    
    <!-- Left Column: Consultation Information and Documents -->
    <div>
        <!-- Consultation Information Card -->
        <div class="cases-info-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-comments" style="color: var(--cases-primary); margin-right: 8px;"></i>
                    Consultation Information
                </h4>
                <span class="cases-status-badge cases-status-<?php echo $consultation['phase'] == 'litigation' ? 'scheduled' : 'consultation'; ?>">
                    <?php echo ucfirst($consultation['phase'] ?: 'Consultation'); ?>
                </span>
            </div>
            <div class="cases-info-card-body">
                <!-- Consultation Details -->
                <div class="cases-grid cases-grid-2" style="margin-bottom: var(--cases-spacing-md);">
                    <div>
                        <strong class="cases-text">Consultation Tag:</strong><br>
                        <span class="cases-text-primary cases-font-weight-medium">
                            <?php echo htmlspecialchars($consultation['tag'] ?: 'General Consultation'); ?>
                        </span>
                    </div>
                    <div>
                        <strong class="cases-text">Phase:</strong><br>
                        <span class="cases-status-badge cases-status-info">
                            <?php echo ucfirst($consultation['phase'] ?: 'Consultation'); ?>
                        </span>
                    </div>
                </div>
                
                <div class="cases-border-top" style="padding-top: var(--cases-spacing-md);"></div>
                
                <!-- Date Information -->
                <div class="cases-grid cases-grid-2" style="margin-top: var(--cases-spacing-md);">
                    <div>
                        <strong class="cases-text">Consultation Date:</strong><br>
                        <span class="cases-text-success cases-font-weight-medium">
                            <?php echo _dt($consultation['date_added']); ?>
                        </span>
                    </div>
                    <div>
                        <strong class="cases-text">Time:</strong><br>
                        <span class="cases-text-muted">
                            <?php echo date('g:i A', strtotime($consultation['date_added'])); ?>
                        </span>
                    </div>
                </div>

                <!-- Consultation Notes -->
                <?php if (!empty($consultation['note'])) { ?>
                <div class="cases-border-top" style="padding-top: var(--cases-spacing-md); margin-top: var(--cases-spacing-md);">
                    <strong class="cases-text">Consultation Notes:</strong>
                    <div style="margin-top: var(--cases-spacing-sm); padding: var(--cases-spacing-md); background: var(--cases-bg-secondary); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-primary);">
                        <div class="consultation-notes-content">
                            <?php echo nl2br(htmlspecialchars($consultation['note'])); ?>
                        </div>
                        <div style="margin-top: var(--cases-spacing-sm);">
                            <button onclick="printConsultationNotes()" class="cases-btn cases-btn-sm cases-btn-info">
                                <i class="fa fa-print"></i> Print Notes
                            </button>
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
                    Consultation Documents
                </h4>
                <span class="cases-count-badge"><?php echo count($consultation_documents); ?></span>
            </div>
            <div class="cases-card-body">
                <?php if (!empty($consultation_documents)) { ?>
                    <!-- Quick Actions Panel -->
                    <div class="cases-document-actions" style="margin-bottom: var(--cases-spacing-md); padding: var(--cases-spacing-sm); background: var(--cases-bg-secondary); border-radius: var(--cases-radius);">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--cases-spacing-sm);">
                            <div style="display: flex; align-items: center; gap: var(--cases-spacing-sm);">
                                <i class="fa fa-info-circle" style="color: var(--cases-info);"></i>
                                <span style="color: var(--cases-text); font-size: var(--cases-font-size-sm);">
                                    <?php echo count($consultation_documents); ?> documents attached to this consultation
                                </span>
                            </div>
                            <div style="display: flex; gap: var(--cases-spacing-xs);">
                                <button onclick="printDocumentList()" class="cases-btn cases-btn-sm">
                                    <i class="fa fa-print"></i> Print List
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents Grid -->
                    <div class="cases-grid cases-grid-responsive">
                        <?php foreach ($consultation_documents as $document) { ?>
                            <div class="cases-card cases-card-compact cases-hover-lift">
                                <div class="cases-card-header">
                                    <h5 class="cases-card-title">
                                        <i class="fa fa-file-<?php echo get_file_icon_class($document['filetype']); ?>" style="margin-right: 8px; color: var(--cases-info);"></i>
                                        <?php echo htmlspecialchars($document['file_name']); ?>
                                    </h5>
                                </div>
                                <div class="cases-card-body">
                                    <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-sm);">
                                        <i class="fa fa-calendar"></i> <?php echo _dt($document['dateadded']); ?>
                                    </div>
                                    <?php if (!empty($document['tag'])) { ?>
                                        <div style="margin-bottom: var(--cases-spacing-sm);">
                                            <span class="cases-status-badge cases-status-info"><?php echo htmlspecialchars($document['tag']); ?></span>
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
                                        <a href="<?php echo site_url('clients/cases/download_document/' . $document['id']); ?>" 
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
                            No documents have been attached to this consultation yet.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Sidebar -->
    <div>
        <!-- Case Relationship Card -->
        <?php if (isset($related_case) && !empty($related_case)) { ?>
        <div class="cases-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-link" style="margin-right: 8px; color: var(--cases-success);"></i>
                    Related Case
                </h4>
            </div>
            <div class="cases-card-body">
                <div style="margin-bottom: var(--cases-spacing-md);">
                    <h5 style="color: var(--cases-primary); margin-bottom: var(--cases-spacing-xs);">
                        <?php echo htmlspecialchars($related_case['case_title']); ?>
                    </h5>
                    <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-muted);">
                        Case #: <?php echo htmlspecialchars($related_case['case_number'] ?: 'Not assigned'); ?>
                    </div>
                </div>
                <div style="padding: var(--cases-spacing-sm); background: var(--cases-success-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-success); margin-bottom: var(--cases-spacing-md);">
                    <div style="color: var(--cases-success); font-size: var(--cases-font-size-sm);">
                        <i class="fa fa-check-circle"></i> This consultation was upgraded to a case
                    </div>
                </div>
                <div style="display: flex; gap: var(--cases-spacing-xs);">
                    <a href="<?php echo site_url('cases/Cases_client/view/' . $related_case['id']); ?>" 
                       class="cases-btn cases-btn-primary cases-btn-sm">
                        <i class="fa fa-eye"></i> View Case
                    </a>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <!-- Upgrade to Case Card -->
        <div class="cases-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-level-up" style="margin-right: 8px; color: var(--cases-warning);"></i>
                    Case Status
                </h4>
            </div>
            <div class="cases-card-body">
                <div style="padding: var(--cases-spacing-sm); background: var(--cases-warning-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-warning); margin-bottom: var(--cases-spacing-md);">
                    <div style="color: var(--cases-warning); font-size: var(--cases-font-size-sm);">
                        <i class="fa fa-info-circle"></i> This consultation has not been converted to a case yet
                    </div>
                </div>
                <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-md);">
                    Contact your legal representative if this consultation needs to be upgraded to a formal case.
                </div>
            </div>
        </div>
        <?php } ?>
        
        <!-- Consultation Summary Card -->
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
                        <span class="cases-count-badge"><?php echo count($consultation_documents); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text">Phase:</strong>
                        <span class="cases-status-badge cases-status-info"><?php echo ucfirst($consultation['phase'] ?: 'Consultation'); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text">Date Created:</strong>
                        <span class="cases-text-muted cases-font-size-sm"><?php echo date('M j, Y', strtotime($consultation['date_added'])); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0;">
                        <strong class="cases-text">Notes Available:</strong>
                        <span class="cases-text-<?php echo !empty($consultation['note']) ? 'success' : 'muted'; ?>">
                            <?php echo !empty($consultation['note']) ? 'Yes' : 'No'; ?>
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

$(document).ready(function() {
    // Add fade-in animation to cards
    $('.cases-card, .cases-info-card, .cases-stat-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
});

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
            '<i class="fa fa-file-text-o" style="font-size: 4rem; color: var(--cases-info); margin-bottom: 20px;"></i>' +
            '<h4>' + fileName + '</h4>' +
            '<p>Document preview functionality can be enhanced to show actual content based on file type.</p>' +
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
function printConsultationNotes() {
    const title = '<?php echo htmlspecialchars($consultation["tag"] ?: "Consultation #" . $consultation["id"]); ?>';
    const date = '<?php echo _dt($consultation["date_added"]); ?>';
    const content = $('.consultation-notes-content').html();
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>${title} - Notes</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
                h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .date { color: #666; margin-bottom: 20px; }
                .content { margin-top: 20px; white-space: pre-line; }
            </style>
        </head>
        <body>
            <h1>${title}</h1>
            <div class="date">Date: ${date}</div>
            <div class="content">${content}</div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function printDocumentList() {
    let content = '<h2>Consultation Documents</h2>';
    content += '<h3><?php echo htmlspecialchars($consultation["tag"] ?: "Consultation #" . $consultation["id"]); ?></h3>';
    content += '<table border="1" style="width: 100%; border-collapse: collapse;">';
    content += '<tr><th>File Name</th><th>Upload Date</th><th>Tag</th></tr>';
    
    <?php foreach ($consultation_documents as $document) { ?>
    content += '<tr>';
    content += '<td><?php echo htmlspecialchars($document["file_name"]); ?></td>';
    content += '<td><?php echo _dt($document["dateadded"]); ?></td>';
    content += '<td><?php echo htmlspecialchars($document["tag"] ?? ""); ?></td>';
    content += '</tr>';
    <?php } ?>
    
    content += '</table>';
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(
        '<html><head><title>Consultation Document List</title>' +
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
/* Document Preview Modal */
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