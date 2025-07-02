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
            <h1>Case Documents</h1>
            <div class="subtitle">Documents for: <?php echo htmlspecialchars($case['case_title']); ?></div>
        </div>
        <div>
            <a href="<?php echo site_url('cases/c'); ?>" class="cases-btn">
                <i class="fa fa-arrow-left"></i>
                Back to My Cases
            </a>
        </div>
    </div>
</div>

<!-- Case Information Card -->
<div class="cases-info-card" style="margin-bottom: var(--cases-spacing-lg);">
    <div class="cases-info-card-header">
        <h4 class="cases-info-card-title">
            <i class="fa fa-briefcase" style="color: var(--cases-info); margin-right: 8px;"></i>
            Case Information
        </h4>
    </div>
    <div class="cases-info-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--cases-spacing-md);">
            <div>
                <strong>Case Number:</strong><br>
                <span class="cases-status-badge cases-status-consultation">
                    <?php echo htmlspecialchars($case['case_number'] ?: 'No Number'); ?>
                </span>
            </div>
            <div>
                <strong>Court:</strong><br>
                <span style="color: var(--cases-text-light);">
                    <?php echo htmlspecialchars($case['court_display'] ?? 'Not specified'); ?>
                </span>
            </div>
            <div>
                <strong>Filed Date:</strong><br>
                <span style="color: var(--cases-text-light);">
                    <?php 
                    if ($case['date_filed']) {
                        echo date('M j, Y', strtotime($case['date_filed']));
                    } else {
                        echo 'Not filed yet';
                    }
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Documents Summary Cards -->
<div class="cases-grid cases-grid-responsive" style="margin-bottom: var(--cases-spacing-xl);">
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-files-o" style="color: var(--cases-info); margin-right: 8px;"></i>
                Total Documents
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo count($case_documents) + count($hearing_documents); ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                All documents for this case
            </div>
        </div>
    </div>
    
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-file-text" style="color: var(--cases-info); margin-right: 8px;"></i>
                Case Documents
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo count($case_documents); ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                General case documents
            </div>
        </div>
    </div>
    
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-gavel" style="color: var(--cases-warning); margin-right: 8px;"></i>
                Hearing Documents
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo count($hearing_documents); ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                Hearing-related documents
            </div>
        </div>
    </div>
</div>

<!-- Tabbed Documents Section -->
<div class="cases-content-section">
    <!-- Tab Navigation -->
    <div class="cases-tab-navigation" style="margin-bottom: var(--cases-spacing-lg);">
        <div class="cases-tab-buttons" style="display: flex; border-bottom: 2px solid var(--cases-border-light);">
            <button class="cases-tab-btn active" onclick="switchTab('case-docs')" id="case-docs-tab">
                <i class="fa fa-file-text" style="margin-right: 8px;"></i>
                Case Documents
                <span class="cases-tab-count"><?php echo count($case_documents); ?></span>
            </button>
            <button class="cases-tab-btn" onclick="switchTab('hearing-docs')" id="hearing-docs-tab">
                <i class="fa fa-gavel" style="margin-right: 8px;"></i>
                Hearing Documents
                <span class="cases-tab-count"><?php echo count($hearing_documents); ?></span>
            </button>
        </div>
    </div>

    <!-- Case Documents Tab Content -->
    <div id="case-docs-content" class="cases-tab-content active">
        <?php if (!empty($case_documents)) { ?>
            <div class="cases-grid cases-grid-responsive">
                <?php foreach ($case_documents as $document) { ?>
                    <div class="cases-card cases-hover-lift">
                        <div class="cases-card-header">
                            <div>
                                <h4 class="cases-card-title">
                                    <?php echo htmlspecialchars($document['file_name'] ?? $document['display_name'] ?? 'Untitled Document'); ?>
                                </h4>
                                <div style="margin-top: 8px;">
                                    <span class="cases-status-badge cases-status-active">
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
                                $tags = [];
                                
                                // Add document type tag
                                $tags[] = 'Case Document';
                                
                                // Add category tags if available
                                if (!empty($document['subject'])) {
                                    $tags[] = $document['subject'];
                                }
                                
                                // Add custom tags from tags field if available
                                if (!empty($document['tags'])) {
                                    $document_tags = explode(',', $document['tags']);
                                    foreach ($document_tags as $tag) {
                                        $tags[] = trim($tag);
                                    }
                                }
                                
                                // Display tags
                                foreach ($tags as $index => $tag) {
                                    if ($index > 0) echo ' ';
                                    echo '<span class="cases-status-badge cases-status-active" style="margin-right: 4px; margin-bottom: 4px;">';
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
            <div class="cases-empty-state">
                <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="margin-bottom: 30px;">
                        <i class="fa fa-file-text" style="font-size: 4rem; color: #999999; opacity: 0.4;"></i>
                    </div>
                    <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                        No Case Documents
                    </h4>
                    <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                        No general case documents have been uploaded yet.
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Hearing Documents Tab Content -->
    <div id="hearing-docs-content" class="cases-tab-content">
        <?php if (!empty($hearing_documents)) { ?>
            <div class="cases-grid cases-grid-responsive">
                <?php foreach ($hearing_documents as $document) { ?>
                    <div class="cases-card cases-hover-lift">
                        <div class="cases-card-header">
                            <div>
                                <h4 class="cases-card-title">
                                    <?php echo htmlspecialchars($document['file_name'] ?? $document['display_name'] ?? 'Untitled Document'); ?>
                                </h4>
                                <div style="margin-top: 8px;">
                                    <span class="cases-status-badge cases-status-scheduled">
                                        <?php echo strtoupper(pathinfo($document['file_name'], PATHINFO_EXTENSION)); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="cases-card-body">
                            <div style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-sm);">
                                <i class="fa fa-gavel" style="margin-right: 6px;"></i>
                                Hearing: <?php echo $document['hearing_date'] ? date('M j, Y', strtotime($document['hearing_date'])) : 'Date not specified'; ?>
                            </div>
                            
                            <?php if (!empty($document['description'])) { ?>
                            <div style="color: var(--cases-text); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-md);">
                                <?php echo htmlspecialchars($document['description']); ?>
                            </div>
                            <?php } ?>
                            
                            <!-- Document Tags -->
                            <div style="margin-bottom: var(--cases-spacing-md);">
                                <?php
                                $tags = [];
                                
                                // Add document type tag
                                $tags[] = 'Hearing Document';
                                
                                // Add hearing date as tag if available
                                if (!empty($document['hearing_date'])) {
                                    $tags[] = date('M Y', strtotime($document['hearing_date']));
                                }
                                
                                // Add category tags if available
                                if (!empty($document['subject'])) {
                                    $tags[] = $document['subject'];
                                }
                                
                                // Add custom tags from tags field if available
                                if (!empty($document['tags'])) {
                                    $document_tags = explode(',', $document['tags']);
                                    foreach ($document_tags as $tag) {
                                        $tags[] = trim($tag);
                                    }
                                }
                                
                                // Display tags
                                foreach ($tags as $index => $tag) {
                                    if ($index > 0) echo ' ';
                                    echo '<span class="cases-status-badge cases-status-scheduled" style="margin-right: 4px; margin-bottom: 4px;">';
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
            <div class="cases-empty-state">
                <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="margin-bottom: 30px;">
                        <i class="fa fa-gavel" style="font-size: 4rem; color: #999999; opacity: 0.4;"></i>
                    </div>
                    <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
                        No Hearing Documents
                    </h4>
                    <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
                        No hearing-related documents have been uploaded yet.
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>


</div>

<script>
// Tab switching functionality
function switchTab(tabName) {
    // Remove active class from all tabs and content
    document.querySelectorAll('.cases-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.cases-tab-content').forEach(content => content.classList.remove('active'));
    
    // Add active class to clicked tab and its content
    document.getElementById(tabName + '-tab').classList.add('active');
    document.getElementById(tabName + '-content').classList.add('active');
}

$(document).ready(function() {
    // Add fade-in animation to cards
    $('.cases-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
    });
});
</script>

<style>
/* Tab Navigation Styling */
.cases-tab-navigation {
    margin-bottom: var(--cases-spacing-lg);
}

.cases-tab-buttons {
    display: flex;
    border-bottom: 2px solid var(--cases-border-light);
    background: var(--cases-bg-primary);
    border-radius: var(--cases-radius) var(--cases-radius) 0 0;
    overflow: hidden;
}

.cases-tab-btn {
    background: transparent;
    border: none;
    padding: var(--cases-spacing-md) var(--cases-spacing-lg);
    cursor: pointer;
    font-size: var(--cases-font-size-base);
    font-weight: 500;
    color: var(--cases-text-muted);
    transition: all 0.3s ease;
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--cases-spacing-xs);
}

.cases-tab-btn:hover {
    background: var(--cases-bg-secondary);
    color: var(--cases-text);
}

.cases-tab-btn.active {
    background: var(--cases-bg-primary);
    color: var(--cases-primary);
    border-bottom: 3px solid var(--cases-primary);
}

.cases-tab-count {
    background: var(--cases-bg-secondary);
    color: var(--cases-text-muted);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: var(--cases-font-size-xs);
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.cases-tab-btn.active .cases-tab-count {
    background: var(--cases-primary);
    color: white;
}

/* Tab Content Styling */
.cases-tab-content {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.cases-tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Document Card Enhancements */
.cases-card {
    transition: all 0.3s ease;
}

.cases-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: var(--cases-border-dark);
}

.cases-hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.cases-hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

/* Mobile responsiveness */
@media (max-width: 768px) {
    .cases-grid-responsive {
        grid-template-columns: 1fr;
    }
    
    .cases-tab-btn {
        padding: var(--cases-spacing-sm) var(--cases-spacing-md);
        font-size: var(--cases-font-size-sm);
    }
    
    .cases-tab-buttons {
        flex-direction: column;
    }
    
    .cases-tab-btn {
        text-align: left;
        justify-content: flex-start;
    }
}

@media (max-width: 480px) {
    .cases-tab-buttons {
        flex-direction: column;
    }
}
</style>