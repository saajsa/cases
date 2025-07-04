<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Include Admin CSS Framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>">

<!-- Document Preview Content -->
<div class="cases-module">
    <div class="cases-content-section">
        <div class="cases-card-header">
            <div class="cases-card-title">
                <i class="fa fa-file-<?php echo $document['type'] == 'pdf' ? 'pdf-o cases-text-danger' : 'text-o cases-text-primary'; ?>"></i>
                <?php echo isset($document['name']) ? $document['name'] : 'Document'; ?>
            </div>
            <div class="cases-text-muted cases-font-size-sm">
                <?php if (isset($document['size'])): ?>
                    <i class="fa fa-hdd-o"></i> Size: <?php echo $document['size']; ?> • 
                <?php endif; ?>
                <i class="fa fa-file-o"></i> Type: <?php echo strtoupper($document['type']); ?>
            </div>
        </div>
        <div class="cases-card-actions">
            <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $document['id']); ?>" class="cases-btn cases-btn-primary cases-btn-sm">
                <i class="fa fa-download"></i> Download
            </a>
        </div>
    </div>
    
    <div class="cases-content-section">
        <?php if ($document['type'] == 'pdf'): ?>
            <div class="cases-text-center">
                <i class="fa fa-file-pdf-o fa-5x cases-text-danger"></i>
                <h5>PDF Document</h5>
                <p class="cases-text-muted">Click download to view the full PDF document.</p>
                <?php if (isset($document['file_path']) && file_exists($document['file_path'])): ?>
                    <iframe src="<?php echo site_url('cases/Cl_cases/view_pdf/' . $document['id']); ?>" 
                            class="cases-preview-iframe">
                        <p>Your browser does not support PDFs. 
                           <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $document['id']); ?>">Download the PDF</a>.
                        </p>
                    </iframe>
                <?php else: ?>
                    <div class="cases-alert cases-alert--warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Document file not found. Please contact support.
                    </div>
                <?php endif; ?>
                </div>
        <?php elseif (in_array($document['type'], ['jpg', 'jpeg', 'png', 'gif'])): ?>
            <div class="cases-text-center">
                <?php if (isset($document['file_path']) && file_exists($document['file_path'])): ?>
                    <img src="<?php echo site_url('cases/Cl_cases/view_image/' . $document['id']); ?>" 
                         class="cases-preview-image">
                <?php else: ?>
                    <div class="cases-alert cases-alert--warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Image file not found. Please contact support.
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (in_array($document['type'], ['txt', 'doc', 'docx'])): ?>
            <div class="cases-text-center">
                <i class="fa fa-file-text-o fa-5x cases-text-primary"></i>
                <h5><?php echo strtoupper($document['type']); ?> Document</h5>
                <p class="cases-text-muted">Click download to view the full document.</p>
                <?php if ($document['type'] == 'txt' && isset($document['file_path']) && file_exists($document['file_path'])): ?>
                    <div class="cases-text-content">
                        <pre><?php echo htmlspecialchars(file_get_contents($document['file_path'])); ?></pre>
                    </div>
                <?php else: ?>
                    <div class="cases-alert cases-alert--info">
                        <i class="fa fa-info-circle"></i>
                        Download the document to view its contents.
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="cases-text-center">
                <i class="fa fa-file-o fa-5x cases-text-muted"></i>
                <h5>Document Preview</h5>
                <p class="cases-text-muted">Preview not available for this file type. Click download to view the document.</p>
                <div class="cases-alert cases-alert--info">
                    <i class="fa fa-info-circle"></i>
                    This document type (<?php echo strtoupper($document['type']); ?>) requires downloading to view.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Document content styling handled by cases framework -->