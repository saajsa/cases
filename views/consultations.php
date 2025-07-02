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
            <span class="cases-breadcrumb-item cases-breadcrumb-current">
                <i class="fa fa-comments"></i>
                Consultations
            </span>
        </div>
        <div class="cases-breadcrumb-actions">
            <div class="cases-quick-nav">
                <a href="<?php echo site_url('cases/Cases_client'); ?>" class="cases-quick-nav-item" title="Back to Dashboard">
                    <i class="fa fa-tachometer"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo site_url('cases/Cases_client'); ?>" class="cases-quick-nav-item" title="View Cases & Hearings">
                    <i class="fa fa-balance-scale"></i>
                    <span>Cases</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page Header -->
<div class="cases-page-header">
    <div class="cases-flex cases-flex-between">
        <div>
            <h1>Your Consultations</h1>
            <div class="subtitle">All legal consultations, notes, and related documents</div>
        </div>
        <div class="cases-header-stats">
            <div class="cases-stat-item">
                <span class="cases-stat-number"><?php echo count($consultations); ?></span>
                <span class="cases-stat-label">Total Consultations</span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="cases-grid cases-grid-responsive" style="margin-bottom: var(--cases-spacing-lg);">
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-comments" style="color: var(--cases-primary); margin-right: 8px;"></i>
                Total Consultations
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2rem; font-weight: 600; color: var(--cases-primary); margin-bottom: 8px;">
                <?php echo count($consultations); ?>
            </div>
            <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                All consultation records
            </div>
        </div>
    </div>

    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-paperclip" style="color: var(--cases-success); margin-right: 8px;"></i>
                Total Documents
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2rem; font-weight: 600; color: var(--cases-success); margin-bottom: 8px;">
                <?php 
                $total_docs = 0;
                foreach ($consultations as $consultation) {
                    $total_docs += (int)($consultation['document_count'] ?? 0);
                }
                echo $total_docs;
                ?>
            </div>
            <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                Attached to consultations
            </div>
        </div>
    </div>

    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-calendar" style="color: var(--cases-info); margin-right: 8px;"></i>
                Recent Activity
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2rem; font-weight: 600; color: var(--cases-info); margin-bottom: 8px;">
                <?php 
                $recent_count = 0;
                $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
                foreach ($consultations as $consultation) {
                    if (date('Y-m-d', strtotime($consultation['date_added'])) >= $thirty_days_ago) {
                        $recent_count++;
                    }
                }
                echo $recent_count;
                ?>
            </div>
            <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                Last 30 days
            </div>
        </div>
    </div>
</div>

<!-- Consultations List -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-list" style="margin-right: 12px; color: var(--cases-primary);"></i>
            All Consultations
        </h3>
    </div>

    <?php if (!empty($consultations)) { ?>
        <div class="cases-grid cases-grid-responsive">
            <?php foreach ($consultations as $consultation) { ?>
                <div class="cases-card cases-hover-lift">
                    <div class="cases-card-header">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <h4 class="cases-card-title">
                                    <?php echo htmlspecialchars($consultation['tag'] ?: 'Consultation #' . $consultation['id']); ?>
                                </h4>
                                <div style="margin-top: 8px;">
                                    <span class="cases-status-badge cases-status-<?php echo $consultation['phase'] == 'litigation' ? 'scheduled' : 'consultation'; ?>">
                                        <?php echo ucfirst($consultation['phase'] ?: 'Consultation'); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right; color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                                <div style="margin-bottom: 4px;">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo date('M j, Y', strtotime($consultation['date_added'])); ?>
                                </div>
                                <div>
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('g:i A', strtotime($consultation['date_added'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cases-card-body">
                        <!-- Consultation Summary -->
                        <?php if (!empty($consultation['note'])) { ?>
                            <div style="color: var(--cases-text); margin-bottom: var(--cases-spacing-md);">
                                <?php 
                                $note_preview = strip_tags($consultation['note']);
                                $note_preview = substr($note_preview, 0, 150);
                                if (strlen($consultation['note']) > 150) {
                                    $note_preview .= '...';
                                }
                                echo htmlspecialchars($note_preview);
                                ?>
                            </div>
                        <?php } else { ?>
                            <div style="color: var(--cases-text-muted); font-style: italic; margin-bottom: var(--cases-spacing-md);">
                                No consultation notes available
                            </div>
                        <?php } ?>

                        <!-- Document Count -->
                        <div style="display: flex; align-items: center; margin-bottom: var(--cases-spacing-md);">
                            <i class="fa fa-paperclip" style="color: var(--cases-text-muted); margin-right: 8px;"></i>
                            <span style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                                <?php echo (int)($consultation['document_count'] ?? 0); ?> document<?php echo ($consultation['document_count'] != 1) ? 's' : ''; ?> attached
                            </span>
                        </div>

                        <!-- Quick Access Tags -->
                        <div style="margin-bottom: var(--cases-spacing-md);">
                            <span class="cases-status-badge cases-status-info" style="margin-right: 4px;">
                                ID: #<?php echo $consultation['id']; ?>
                            </span>
                            <?php if (!empty($consultation['note'])) { ?>
                                <span class="cases-status-badge cases-status-success" style="margin-right: 4px;">
                                    <i class="fa fa-file-text-o"></i> Notes Available
                                </span>
                            <?php } ?>
                            <?php if ((int)($consultation['document_count'] ?? 0) > 0) { ?>
                                <span class="cases-status-badge cases-status-consultation">
                                    <i class="fa fa-paperclip"></i> Documents
                                </span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="cases-card-footer">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                                <i class="fa fa-clock-o"></i>
                                <?php echo time_ago($consultation['date_added']); ?>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <?php if (!empty($consultation['note'])) { ?>
                                    <button onclick="showConsultationModal(<?php echo $consultation['id']; ?>)" 
                                            class="cases-btn cases-btn-info cases-btn-sm">
                                        <i class="fa fa-eye"></i>
                                        View Notes
                                    </button>
                                <?php } ?>
                                <a href="<?php echo site_url('cases/Cases_client/consultation/' . $consultation['id']); ?>" 
                                   class="cases-btn cases-btn-primary cases-btn-sm">
                                    <i class="fa fa-arrow-right"></i>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <!-- Empty State -->
        <div style="text-align: center; padding: 60px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 30px;">
                <i class="fa fa-comments" style="font-size: 5rem; color: #999999; opacity: 0.4;"></i>
            </div>
            <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                No Consultations Found
            </h4>
            <p style="color: #666666; margin-bottom: 30px; font-size: 1.1rem; max-width: 400px; margin-left: auto; margin-right: auto;">
                You don't have any consultation records yet. Consultations will appear here once they are created by your legal team.
            </p>
            <div style="margin-top: 30px;">
                <a href="<?php echo site_url('cases/Cases_client'); ?>" class="cases-btn cases-btn-primary">
                    <i class="fa fa-home"></i>
                    Return to Dashboard
                </a>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Quick Actions Section -->
<?php if (!empty($consultations)) { ?>
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-cogs" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Quick Actions
        </h3>
    </div>
    
    <div class="cases-grid cases-grid-responsive">
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-search" style="color: var(--cases-info); margin-right: 8px;"></i>
                    Search & Filter
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Use your browser's search (Ctrl+F) to find specific consultations by tag, phase, or date.
                </p>
                <button onclick="document.querySelector('body').style.outline = '2px solid var(--cases-primary)'; setTimeout(() => document.querySelector('body').style.outline = 'none', 2000);" 
                        class="cases-btn cases-btn-info cases-btn-sm">
                    <i class="fa fa-search"></i>
                    Search Page
                </button>
            </div>
        </div>
        
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-print" style="color: var(--cases-success); margin-right: 8px;"></i>
                    Print Summary
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Print a summary of all your consultations for your records.
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
                    <i class="fa fa-home" style="color: var(--cases-primary); margin-right: 8px;"></i>
                    Return to Dashboard
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Go back to your main legal dashboard to access cases and other features.
                </p>
                <a href="<?php echo site_url('cases/Cases_client'); ?>" 
                   class="cases-btn cases-btn-primary cases-btn-sm">
                    <i class="fa fa-home"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

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
// Time ago function helper
function time_ago(date_string) {
    const date = new Date(date_string);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' mins ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    if (diff < 2592000) return Math.floor(diff / 86400) + ' days ago';
    return Math.floor(diff / 2592000) + ' months ago';
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

$(document).ready(function() {
    // Animate cards on load
    $('.cases-card, .cases-info-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
    
    // Update time ago dynamically
    setInterval(function() {
        $('.time-ago').each(function() {
            const date = $(this).data('date');
            $(this).text(time_ago(date));
        });
    }, 60000); // Update every minute
});
</script>

<style>
@media print {
    .cases-btn, .cases-section-header, .cases-page-header .cases-flex > div:last-child,
    .cases-card-footer, .consultation-modal {
        display: none !important;
    }
    
    .cases-card {
        break-inside: avoid;
        margin-bottom: 20px;
    }
    
    body { color: #000; }
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
    
    .cases-grid-responsive {
        grid-template-columns: 1fr;
    }
    
    .cases-card-footer > div {
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
    }
    
    .cases-card-footer .cases-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>