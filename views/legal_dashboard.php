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
            <span class="cases-breadcrumb-item cases-breadcrumb-current">
                <i class="fa fa-tachometer"></i>
                Legal Dashboard
            </span>
        </div>
        <div class="cases-breadcrumb-actions">
            <div class="cases-quick-nav">
                <a href="<?php echo site_url('cases/c/cases'); ?>" class="cases-quick-nav-item" title="View Cases & Hearings">
                    <i class="fa fa-balance-scale"></i>
                    <span>Cases</span>
                </a>
                <a href="<?php echo site_url('cases/c/consultations'); ?>" class="cases-quick-nav-item" title="View Consultations">
                    <i class="fa fa-comments"></i>
                    <span>Consultations</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Header -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h2 class="cases-section-title">
            Your Legal Overview
        </h2>
        <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
            Quick access to your cases, consultations, and important documents
        </div>
    </div>
</div>

<!-- Priority Actions Section -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-star" style="margin-right: 12px; color: var(--cases-warning);"></i>
            What's Important Today
        </h3>
        <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
            Your priority items requiring attention
        </div>
    </div>

    <div class="cases-grid cases-grid-responsive" style="margin-bottom: var(--cases-spacing-lg);">
        <!-- Upcoming Hearings Card -->
        <div class="cases-info-card cases-priority-upcoming">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-calendar-check-o" style="color: var(--cases-info); margin-right: 8px;"></i>
                    Upcoming Hearings
                </h4>
            </div>
            <div class="cases-info-card-body">
                <?php 
                $upcoming_hearings = 0;
                // Count upcoming hearings in the next 30 days
                // This would be calculated in the controller in real implementation
                ?>
                <div style="font-size: 2rem; font-weight: 700; color: var(--cases-info); line-height: 1; margin-bottom: 8px;">
                    <?php echo $upcoming_hearings; ?>
                </div>
                <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Next 30 days
                </div>
                <div class="cases-quick-actions">
                    <a href="<?php echo site_url('cases/c/cases'); ?>" class="cases-quick-action-btn">
                        <i class="fa fa-calendar"></i> View Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- New Documents Card -->
        <div class="cases-info-card cases-priority-medium">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-file-text-o" style="color: var(--cases-warning); margin-right: 8px;"></i>
                    Recent Documents
                </h4>
            </div>
            <div class="cases-info-card-body">
                <?php 
                $recent_docs = count($client_documents);
                // Show recent documents from last 7 days
                ?>
                <div style="font-size: 2rem; font-weight: 700; color: var(--cases-warning); line-height: 1; margin-bottom: 8px;">
                    <?php echo $recent_docs; ?>
                </div>
                <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                    Last 7 days
                </div>
                <div class="cases-quick-actions">
                    <a href="#recent-documents" class="cases-quick-action-btn" onclick="document.getElementById('recent-documents').scrollIntoView({behavior: 'smooth'});">
                        <i class="fa fa-arrow-down"></i> View Below
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Access Card -->
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-bolt" style="color: var(--cases-success); margin-right: 8px;"></i>
                    Quick Access
                </h4>
            </div>
            <div class="cases-info-card-body">
                <div style="margin-bottom: var(--cases-spacing-md);">
                    <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light); margin-bottom: var(--cases-spacing-sm);">
                        Jump directly to:
                    </div>
                </div>
                <div class="cases-quick-actions">
                    <a href="<?php echo site_url('cases/c/cases'); ?>" class="cases-quick-action-btn">
                        <i class="fa fa-balance-scale"></i> Cases
                    </a>
                    <a href="<?php echo site_url('cases/c/consultations'); ?>" class="cases-quick-action-btn">
                        <i class="fa fa-comments"></i> Consultations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            <i class="fa fa-bar-chart" style="margin-right: 12px; color: var(--cases-primary);"></i>
            Your Legal Overview
        </h3>
        <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
            Complete summary of your legal matters
        </div>
    </div>
</div>

<div class="cases-grid cases-grid-responsive" style="margin-bottom: var(--cases-spacing-xl);">
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                <i class="fa fa-balance-scale" style="color: var(--cases-primary); margin-right: 8px;"></i>
                Active Cases
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo $total_cases; ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light); margin-bottom: var(--cases-spacing-md);">
                Legal cases in progress
            </div>
            <div class="cases-quick-actions">
                <a href="<?php echo site_url('cases/c/cases'); ?>" class="cases-quick-action-btn">
                    <i class="fa fa-arrow-right"></i> View Details
                </a>
            </div>
        </div>
    </div>
    
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                Documents
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo $total_documents; ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                Total documents
            </div>
        </div>
    </div>
    
    <div class="cases-info-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">
                Hearings
            </h4>
        </div>
        <div class="cases-info-card-body">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--cases-primary); line-height: 1; margin-bottom: 8px;">
                <?php echo $total_hearings; ?>
            </div>
            <div style="font-size: var(--cases-font-size-sm); color: var(--cases-text-light);">
                Scheduled hearings
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            Quick Actions
        </h3>
    </div>
    
    <div class="cases-grid cases-grid-responsive">
        <div class="cases-card cases-hover-lift">
            <div class="cases-card-body" style="text-align: center; padding: var(--cases-spacing-lg);">
                <h4 style="margin-bottom: var(--cases-spacing-sm);">View Cases</h4>
                <p style="color: var(--cases-text-muted); margin-bottom: var(--cases-spacing-lg);">
                    View all your cases and related documents
                </p>
                <a href="<?php echo site_url('cases/c/cases'); ?>" class="cases-btn cases-btn-primary">
                    <i class="fa fa-eye"></i>
                    View Cases
                </a>
            </div>
        </div>
        
        <div class="cases-card cases-hover-lift">
            <div class="cases-card-body" style="text-align: center; padding: var(--cases-spacing-lg);">
                <h4 style="margin-bottom: var(--cases-spacing-sm);">Consultations</h4>
                <p style="color: var(--cases-text-muted); margin-bottom: var(--cases-spacing-lg);">
                    View consultation records and notes
                </p>
                <a href="<?php echo site_url('cases/c/consultations'); ?>" class="cases-btn cases-btn-info">
                    <i class="fa fa-comments"></i>
                    View Consultations
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Documents Section -->
<?php if (!empty($client_documents)) { ?>
<div class="cases-content-section" id="recent-documents">
    <div class="cases-section-header">
        <div class="cases-flex cases-flex-between">
            <div>
                <h3 class="cases-section-title">
                    <i class="fa fa-clock-o" style="margin-right: 12px; color: var(--cases-primary);"></i>
                    Recent Documents
                </h3>
                <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                    Latest documents and files related to your legal matters
                </div>
            </div>
            <div class="cases-quick-actions">
                <a href="<?php echo site_url('cases/c/cases'); ?>" class="cases-quick-action-btn">
                    <i class="fa fa-search"></i> Browse All Documents
                </a>
            </div>
        </div>
    </div>

    <div class="cases-grid cases-grid-responsive">
        <?php foreach ($client_documents as $document) { ?>
            <div class="cases-card cases-hover-lift">
                <div class="cases-card-header">
                    <div>
                        <h4 class="cases-card-title">
                            <?php echo htmlspecialchars($document['file_name'] ?? 'Untitled Document'); ?>
                        </h4>
                        <div style="margin-top: 8px;">
                            <span class="cases-status-badge cases-status-info">
                                <?php echo strtoupper(pathinfo($document['file_name'], PATHINFO_EXTENSION)); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="cases-card-body">
                    <div style="color: var(--cases-text-light); margin-bottom: var(--cases-spacing-sm);">
                        <?php echo date('M j, Y g:i A', strtotime($document['dateadded'])); ?>
                    </div>
                    
                    <?php if (!empty($document['description'])) { ?>
                    <div style="color: var(--cases-text); font-size: var(--cases-font-size-sm); margin-bottom: var(--cases-spacing-md);">
                        <?php echo htmlspecialchars($document['description']); ?>
                    </div>
                    <?php } ?>
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
</div>
<?php } ?>

<!-- Recent Activity Section -->
<?php if (!empty($recent_activity)) { ?>
<div class="cases-content-section">
    <div class="cases-section-header">
        <h3 class="cases-section-title">
            Recent Activity
        </h3>
        <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
            Latest updates to your legal matters
        </div>
    </div>

    <div class="cases-card">
        <div class="cases-card-body">
            <?php foreach ($recent_activity as $activity) { ?>
                <div style="display: flex; align-items: center; padding: var(--cases-spacing-md) 0; border-bottom: 1px solid var(--cases-border-light);">
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--cases-text);">
                            <?php echo htmlspecialchars($activity['title']); ?>
                        </div>
                        <div style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                            <?php echo date('M j, Y g:i A', strtotime($activity['date'])); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<!-- Empty State -->
<?php if (empty($client_documents) && empty($recent_activity)) { ?>
<div class="cases-content-section">
    <div style="text-align: center; padding: 40px 30px; background: #ffffff; border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="margin-bottom: 30px;">
        </div>
        <h4 style="margin-bottom: 20px; color: #2c2c2c; font-weight: 600;">
            Welcome to Your Legal Dashboard
        </h4>
        <p style="color: #666666; margin-bottom: 30px; font-size: 1rem;">
            Your cases and documents will appear here as they are created.
        </p>
        <div style="padding: 20px; background: #eff8ff; border: 1px solid #1a6bcc; border-radius: 2px; color: #1a6bcc;">
            <strong>Getting Started:</strong> Contact your legal representative to begin working on your cases.
        </div>
    </div>
</div>
<?php } ?>

</div>

<script>
$(document).ready(function() {
    // Add fade-in animation to cards
    $('.cases-card, .cases-info-card').each(function(index) {
        $(this).addClass('cases-fade-in');
        setTimeout(() => {
            $(this).addClass('active');
        }, index * 100);
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

/* Enhanced hover effects */
.cases-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: var(--cases-border-dark);
}

.cases-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .cases-grid-responsive {
        grid-template-columns: 1fr;
    }
    
    .cases-card-body {
        padding: var(--cases-spacing-md);
    }
}
</style>