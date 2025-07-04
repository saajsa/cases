<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Include Admin CSS Framework -->
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/cases-framework.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/buttons.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('modules/cases/assets/css/components/cards.css'); ?>">

<!-- Enhanced Document Preview with better modal support -->
<style>
.document-preview-container {
    padding: 0;
    margin: 0;
}

.document-preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.document-preview-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.document-preview-meta {
    font-size: 12px;
    color: #7f8c8d;
    margin: 0;
}

.document-preview-actions {
    display: flex;
    gap: 8px;
}

.preview-btn {
    padding: 6px 12px;
    font-size: 12px;
    border: 1px solid #ddd;
    background: #fff;
    color: #2c3e50;
    text-decoration: none;
    border-radius: 3px;
    transition: all 0.2s;
}

.preview-btn:hover {
    background: #f8f9fa;
    text-decoration: none;
    color: #2c3e50;
}

.preview-btn.primary {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.preview-btn.primary:hover {
    background: #2980b9;
    border-color: #2980b9;
    color: white;
}

.document-preview-content {
    padding: 20px;
    min-height: 400px;
    max-height: 60vh;
    overflow-y: auto;
}

.preview-iframe {
    width: 100%;
    height: 500px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.preview-image {
    max-width: 100%;
    max-height: 500px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-text-content {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
    text-align: left;
}

.preview-text-content pre {
    margin: 0;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.preview-placeholder {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.preview-placeholder i {
    margin-bottom: 15px;
    opacity: 0.6;
}

.preview-alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin: 15px 0;
    border: 1px solid transparent;
}

.preview-alert.info {
    background: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.preview-alert.warning {
    background: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}

.file-icon {
    width: 48px;
    height: 48px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    border: 2px solid #e9ecef;
}

.file-icon.pdf { border-color: #e74c3c; background: #fee; }
.file-icon.image { border-color: #9b59b6; background: #f8f4ff; }
.file-icon.text { border-color: #3498db; background: #e8f4fd; }
.file-icon.document { border-color: #f39c12; background: #fff8e1; }
</style>

<div class="document-preview-container">
    <!-- Document Header -->
    <div class="document-preview-header">
        <div>
            <h4 class="document-preview-title">
                <i class="fa fa-file-<?php echo $document['type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i>
                <?php echo htmlspecialchars($document['name']); ?>
            </h4>
            <p class="document-preview-meta">
                Type: <?php echo strtoupper($document['type']); ?>
                <?php if (isset($document['size']) && $document['size'] != 'N/A'): ?>
                    • Size: <?php echo $document['size']; ?>
                <?php endif; ?>
                • Uploaded: <?php echo date('M d, Y', strtotime($document['dateadded'])); ?>
            </p>
        </div>
        <div class="document-preview-actions">
            <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $document['id']); ?>" 
               class="preview-btn primary" target="_blank">
                <i class="fa fa-download"></i> Download
            </a>
        </div>
    </div>
    
    <!-- Document Content -->
    <div class="document-preview-content">
        <?php 
        // Get file extension from multiple sources
        $file_extension = '';
        
        // Try different sources for file type
        if (isset($document['file_extension'])) {
            $file_extension = $document['file_extension'];
        } elseif (isset($document['filetype'])) {
            $file_extension = strtolower($document['filetype']);
        } elseif (isset($document['type'])) {
            $file_extension = strtolower($document['type']);
        } else {
            // Extract from filename as fallback
            $file_extension = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
        }
        
        // Handle MIME type formats like 'application/pdf'
        if (strpos($file_extension, '/') !== false) {
            $parts = explode('/', $file_extension);
            $file_extension = end($parts);
        }
        
        // Additional cleanup for common cases
        $file_extension = str_replace(['application/', 'text/', 'image/'], '', $file_extension);
        
        $upload_path = FCPATH . 'uploads/';
        $possible_paths = [];
        
        // Build multiple possible file paths based on Perfex CRM patterns
        
        // Standard Perfex CRM document storage
        $possible_paths[] = $upload_path . 'documents/' . $document['file_name'];
        
        // Type-based subdirectories
        if ($document['rel_type'] == 'client') {
            $possible_paths[] = $upload_path . 'client/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'clients/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        } elseif ($document['rel_type'] == 'case') {
            $possible_paths[] = $upload_path . 'case/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'cases/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        }
        
        $possible_paths[] = $upload_path . $document['file_name'];
        
        $file_path = null;
        $file_exists = false;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $file_path = $path;
                $file_exists = true;
                break;
            }
        }
        
        // Debug information (remove this in production)
        // echo "<!-- Debug: Original type: " . (isset($document['type']) ? $document['type'] : 'N/A') . ", Final extension: " . $file_extension . " -->";
        ?>
        
        <?php if ($file_extension == 'pdf'): ?>
            <div class="preview-placeholder">
                <div class="file-icon pdf">
                    <i class="fa fa-file-pdf-o fa-2x" style="color: #e74c3c;"></i>
                </div>
                <h5>PDF Document</h5>
                <?php if ($file_exists): ?>
                    <div class="preview-iframe-container">
                        <iframe src="<?php echo site_url('cases/Cl_cases/view_pdf/' . $document['id']); ?>" 
                                class="preview-iframe"
                                title="PDF Preview">
                            <p>Your browser doesn't support PDF viewing. 
                               <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $document['id']); ?>">Download the PDF</a>
                            </p>
                        </iframe>
                    </div>
                <?php else: ?>
                    <div class="preview-alert warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        PDF file not found. Please download to access the document.
                    </div>
                <?php endif; ?>
            </div>
            
        <?php elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
            <div class="preview-placeholder">
                <div class="file-icon image">
                    <i class="fa fa-image fa-2x" style="color: #9b59b6;"></i>
                </div>
                <?php if ($file_exists): ?>
                    <img src="<?php echo site_url('cases/Cl_cases/view_image/' . $document['id']); ?>" 
                         alt="<?php echo htmlspecialchars($document['name']); ?>"
                         class="preview-image">
                <?php else: ?>
                    <h5>Image Preview</h5>
                    <div class="preview-alert warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Image file not found. Please download to view the image.
                    </div>
                <?php endif; ?>
            </div>
            
        <?php elseif ($file_extension == 'txt'): ?>
            <div class="preview-placeholder">
                <div class="file-icon text">
                    <i class="fa fa-file-text-o fa-2x" style="color: #3498db;"></i>
                </div>
                <?php if ($file_exists && filesize($file_path) < 50000): // Limit to 50KB ?>
                    <h5>Text File Content</h5>
                    <div class="preview-text-content">
                        <pre><?php echo htmlspecialchars(file_get_contents($file_path)); ?></pre>
                    </div>
                <?php else: ?>
                    <h5>Text Document</h5>
                    <div class="preview-alert info">
                        <i class="fa fa-info-circle"></i>
                        <?php echo $file_exists ? 'File too large for preview.' : 'Text file not found.'; ?> 
                        Download to view the content.
                    </div>
                <?php endif; ?>
            </div>
            
        <?php elseif (in_array($file_extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
            <div class="preview-placeholder">
                <div class="file-icon document">
                    <i class="fa fa-file-<?php echo in_array($file_extension, ['xls', 'xlsx']) ? 'excel' : (in_array($file_extension, ['ppt', 'pptx']) ? 'powerpoint' : 'word'); ?>-o fa-2x" 
                       style="color: #f39c12;"></i>
                </div>
                <h5><?php echo strtoupper($file_extension); ?> Document</h5>
                <p>Office documents require downloading to view the full content.</p>
                <div class="preview-alert info">
                    <i class="fa fa-info-circle"></i>
                    This document type requires Microsoft Office or compatible software to view.
                </div>
            </div>
            
        <?php else: ?>
            <div class="preview-placeholder">
                <div class="file-icon">
                    <i class="fa fa-file-o fa-2x" style="color: #95a5a6;"></i>
                </div>
                <h5>Document Preview</h5>
                <p>Preview not available for this file type (<?php echo strtoupper($file_extension); ?>)</p>
                <div class="preview-alert info">
                    <i class="fa fa-info-circle"></i>
                    Download the document to view its contents.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Document content styling handled by cases framework -->