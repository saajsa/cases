<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Load Cases CSS framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/status-badges.css'); ?>?v=<?php echo time(); ?>">

<div class="cases-module">

<!-- Page Header -->
<div class="cases-page-header">
    <div class="cases-flex cases-flex-between">
        <div>
            <h1>Consultation Details</h1>
            <div class="subtitle"><?php echo htmlspecialchars($consultation['tag'] ?: 'Consultation'); ?></div>
        </div>
        <div>
            <a href="<?php echo site_url('cases/c'); ?>" class="cases-btn">
                <i class="fa fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Consultation Information Card -->
<div class="cases-info-card" style="margin-bottom: var(--cases-spacing-lg);">
    <div class="cases-info-card-header">
        <h4 class="cases-info-card-title">
            <i class="fa fa-comments" style="color: var(--cases-info); margin-right: 8px;"></i>
            Consultation Information
        </h4>
    </div>
    <div class="cases-info-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--cases-spacing-md);">
            <div>
                <strong>Consultation ID:</strong><br>
                <span class="cases-status-badge cases-status-info">
                    #<?php echo $consultation['id']; ?>
                </span>
            </div>
            <div>
                <strong>Phase:</strong><br>
                <span class="cases-status-badge cases-status-<?php echo $consultation['phase'] == 'litigation' ? 'scheduled' : 'consultation'; ?>">
                    <?php echo ucfirst($consultation['phase'] ?: 'Consultation'); ?>
                </span>
            </div>
            <div>
                <strong>Date:</strong><br>
                <span style="color: var(--cases-text-light);">
                    <?php echo date('M j, Y g:i A', strtotime($consultation['date_added'])); ?>
                </span>
            </div>
            <div>
                <strong>Documents:</strong><br>
                <span style="color: var(--cases-text-light);">
                    <?php echo count($documents); ?> attached
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Consultation Notes -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-file-text-o" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Consultation Notes
        </h3>
    </div>

    <div class="cases-card">
        <div class="cases-card-body" style="padding: var(--cases-spacing-lg);">
            <?php if (!empty($consultation['note'])) { ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fa fa-file-text-o" style="font-size: 3rem; color: var(--cases-primary); margin-bottom: 20px;"></i>
                    <h4 style="margin-bottom: 15px; color: var(--cases-text);">Consultation Notes Available</h4>
                    <p style="color: var(--cases-text-muted); margin-bottom: 25px;">
                        Click below to view the complete consultation notes in a dedicated modal window.
                    </p>
                    <button onclick="showConsultationModal(<?php echo $consultation['id']; ?>)" 
                            class="cases-btn cases-btn-primary">
                        <i class="fa fa-eye"></i>
                        View Full Consultation Notes
                    </button>
                </div>
            <?php } else { ?>
                <div style="text-align: center; padding: 20px; color: var(--cases-text-muted);">
                    <i class="fa fa-file-text-o" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p>No consultation notes available.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Consultation Documents -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-paperclip" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Consultation Documents
        </h3>
    </div>

    <?php if (!empty($documents)) { ?>
        <div class="cases-grid cases-grid-responsive">
            <?php foreach ($documents as $document) { ?>
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div>
                            <h4 class="cases-card-title">
                                <?php echo htmlspecialchars($document['file_name'] ?? 'Untitled Document'); ?>
                            </h4>
                            <div style="margin-top: 8px;">
                                <span class="cases-status-badge cases-status-consultation">
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
                            $tags = ['Consultation Document'];
                            
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
                                echo '<span class="cases-status-badge cases-status-consultation" style="margin-right: 4px; margin-bottom: 4px;">';
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
                <i class="fa fa-paperclip" style="font-size: 4rem; color: #999999; opacity: 0.4;"></i>
            </div>
            <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                No Documents Attached
            </h4>
            <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                No documents have been attached to this consultation.
            </p>
        </div>
    <?php } ?>
</div>

<!-- Additional Actions -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-cogs" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Actions
        </h3>
    </div>
    
    <div class="cases-grid cases-grid-responsive">
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-print" style="color: var(--cases-success); margin-right: 8px;"></i>
                    Print Consultation
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Print this consultation details for your records.
                </p>
                <button onclick="window.print()" 
                        class="cases-btn cases-btn-success cases-btn-sm">
                    <i class="fa fa-print"></i>
                    Print Page
                </button>
            </div>
        </div>
        
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-share" style="color: var(--cases-info); margin-right: 8px;"></i>
                    Share Link
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Copy the consultation link to share or bookmark.
                </p>
                <button onclick="copyToClipboard('<?php echo current_url(); ?>')" 
                        class="cases-btn cases-btn-info cases-btn-sm">
                    <i class="fa fa-copy"></i>
                    Copy Link
                </button>
            </div>
        </div>
        
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-home" style="color: var(--cases-primary); margin-right: 8px;"></i>
                    Back to Dashboard
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Return to your main cases dashboard.
                </p>
                <a href="<?php echo site_url('cases/c'); ?>" 
                   class="cases-btn cases-btn-primary cases-btn-sm">
                    <i class="fa fa-home"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>
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

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        } catch (err) {
            alert('Failed to copy link');
        }
        document.body.removeChild(textArea);
    });
}

$(document).ready(function() {
    $('.cases-card, .cases-info-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
});
</script>

<style>
@media print {
    .cases-btn, .cases-section-header, .cases-page-header .col-md-4 {
        display: none !important;
    }
    
    .cases-card-footer {
        display: none !important;
    }
}

.cases-fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.cases-fade-in.active {
    opacity: 1;
    transform: translateY(0);
}

.cases-card:hover, .cases-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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