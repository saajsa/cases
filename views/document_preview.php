<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Load Cases CSS framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/status-badges.css'); ?>?v=<?php echo time(); ?>">

<div class="cases-module">

<!-- Page Header -->
<div class="cases-page-header">
    <div class="row">
        <div class="col-md-8">
            <h1>Document Preview</h1>
            <div class="subtitle"><?php echo htmlspecialchars($file['file_name']); ?></div>
        </div>
        <div class="col-md-4">
            <div class="cases-flex cases-flex-end cases-flex-wrap">
                <a href="javascript:history.back()" class="cases-btn">
                    <i class="fa fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Document Information Card -->
<div class="cases-info-card" style="margin-bottom: var(--cases-spacing-lg);">
    <div class="cases-info-card-header">
        <h4 class="cases-info-card-title">
            <i class="fa fa-file-o" style="color: var(--cases-info); margin-right: 8px;"></i>
            Document Information
        </h4>
    </div>
    <div class="cases-info-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--cases-spacing-md);">
            <div>
                <strong>File Name:</strong><br>
                <span style="color: var(--cases-text-light); word-break: break-all;">
                    <?php echo htmlspecialchars($file['file_name']); ?>
                </span>
            </div>
            <div>
                <strong>File Type:</strong><br>
                <span class="cases-status-badge cases-status-active">
                    <?php echo strtoupper($file_extension); ?>
                </span>
            </div>
            <div>
                <strong>File Size:</strong><br>
                <span style="color: var(--cases-text-light);">
                    <?php echo $file_size; ?>
                </span>
            </div>
            <div>
                <strong>Uploaded:</strong><br>
                <span style="color: var(--cases-text-light);">
                    <?php echo date('M j, Y g:i A', strtotime($file['dateadded'])); ?>
                </span>
            </div>
        </div>
        
        <?php if (!empty($context_info)) { ?>
        <div style="margin-top: var(--cases-spacing-md); padding-top: var(--cases-spacing-md); border-top: 1px solid var(--cases-border-light);">
            <strong>Context:</strong><br>
            <span style="color: var(--cases-text-light);">
                <?php echo htmlspecialchars($context_info); ?>
            </span>
        </div>
        <?php } ?>
        
        <?php if (!empty($file['description'])) { ?>
        <div style="margin-top: var(--cases-spacing-md); padding-top: var(--cases-spacing-md); border-top: 1px solid var(--cases-border-light);">
            <strong>Description:</strong><br>
            <span style="color: var(--cases-text);">
                <?php echo htmlspecialchars($file['description']); ?>
            </span>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Document Preview Section -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-eye" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Document Preview
        </h3>
    </div>

    <!-- Preview Content -->
    <div class="cases-card">
        <div class="cases-card-body" style="text-align: center; padding: var(--cases-spacing-xl);">
            
            <?php if (in_array($file_extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
                <!-- Office Documents -->
                <div style="margin-bottom: 30px;">
                    <i class="fa fa-file-word-o" style="font-size: 4rem; color: #2b579a; opacity: 0.8;"></i>
                </div>
                <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                    Microsoft Office Document
                </h4>
                <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                    This <?php echo strtoupper($file_extension); ?> file cannot be previewed directly in the browser.
                </p>
                <div style="padding: 20px; background: #eff8ff; border: 1px solid #1a6bcc; border-radius: 2px; color: #1a6bcc; margin-bottom: 30px;">
                    <i class="fa fa-info-circle" style="margin-right: 8px;"></i>
                    <strong>Tip:</strong> Download the file to view it in Microsoft Office or a compatible application.
                </div>
                
            <?php elseif (in_array($file_extension, ['zip', 'rar', '7z'])): ?>
                <!-- Archive Files -->
                <div style="margin-bottom: 30px;">
                    <i class="fa fa-file-archive-o" style="font-size: 4rem; color: #f39c12; opacity: 0.8;"></i>
                </div>
                <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                    Archive File
                </h4>
                <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                    This is a compressed archive file containing multiple files.
                </p>
                <div style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 2px; color: #856404; margin-bottom: 30px;">
                    <i class="fa fa-exclamation-triangle" style="margin-right: 8px;"></i>
                    <strong>Note:</strong> Download and extract the archive to access the contained files.
                </div>
                
            <?php else: ?>
                <!-- Other File Types -->
                <div style="margin-bottom: 30px;">
                    <i class="fa fa-file-o" style="font-size: 4rem; color: #999999; opacity: 0.6;"></i>
                </div>
                <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                    Document Preview Not Available
                </h4>
                <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                    This file type (<?php echo strtoupper($file_extension); ?>) cannot be previewed in the browser.
                </p>
                <div style="padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 2px; color: #6c757d; margin-bottom: 30px;">
                    <i class="fa fa-download" style="margin-right: 8px;"></i>
                    <strong>Download Required:</strong> Please download the file to view its contents.
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: center; gap: var(--cases-spacing-md); flex-wrap: wrap;">
                <a href="<?php echo site_url('cases/c/download/' . $file['id']); ?>" 
                   class="cases-btn cases-btn-primary">
                    <i class="fa fa-download"></i>
                    Download Document
                </a>
                
                <button onclick="window.close()" class="cases-btn">
                    <i class="fa fa-times"></i>
                    Close Preview
                </button>
            </div>
        </div>
    </div>
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
                    <i class="fa fa-download" style="color: var(--cases-primary); margin-right: 8px;"></i>
                    Download
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Download this document to your device for offline viewing.
                </p>
                <a href="<?php echo site_url('cases/c/download/' . $file['id']); ?>" 
                   class="cases-btn cases-btn-primary cases-btn-sm">
                    <i class="fa fa-download"></i>
                    Download Now
                </a>
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
                    Copy the document link to share or bookmark.
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
                    <i class="fa fa-print" style="color: var(--cases-success); margin-right: 8px;"></i>
                    Print Info
                </h4>
            </div>
            <div class="cases-info-card-body">
                <p style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Print this document information page.
                </p>
                <button onclick="window.print()" 
                        class="cases-btn cases-btn-success cases-btn-sm">
                    <i class="fa fa-print"></i>
                    Print Page
                </button>
            </div>
        </div>
    </div>
</div>

</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        alert('Link copied to clipboard!');
    }, function(err) {
        // Fallback for older browsers
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

// Add fade-in animation
$(document).ready(function() {
    $('.cases-card, .cases-info-card').each(function(index) {
        $(this).css('opacity', '0').css('transform', 'translateY(20px)');
        setTimeout(() => {
            $(this).animate({
                opacity: 1,
                transform: 'translateY(0)'
            }, 300);
        }, index * 100);
    });
});
</script>

<style>
/* Print styles */
@media print {
    .cases-btn, .cases-section-header {
        display: none !important;
    }
    
    .cases-page-header .col-md-4 {
        display: none !important;
    }
}

/* Enhanced card animations */
.cases-card, .cases-info-card {
    transition: all 0.3s ease;
}

.cases-card:hover, .cases-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>