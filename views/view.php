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
    <div class="cases-flex cases-flex-between">
        <div>
            <h1>
                <i class="fa fa-gavel" style="margin-right: 12px; color: var(--cases-primary);"></i>
                <?php echo _l('case_information'); ?>
            </h1>
            <div class="subtitle"><?php echo htmlspecialchars($case['case_title']); ?></div>
        </div>
        <div>
            <a href="<?php echo site_url('cases/Cases_client'); ?>" class="cases-btn cases-btn-primary">
                <i class="fa fa-arrow-left"></i>
                <?php echo _l('back_to_cases'); ?>
            </a>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="cases-grid" style="grid-template-columns: 2fr 1fr; gap: var(--cases-spacing-lg);">
    
    <!-- Left Column: Case Information and Documents -->
    <div>
        <!-- Case Information Card -->
        <div class="cases-info-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">
                    <i class="fa fa-gavel" style="color: var(--cases-primary); margin-right: 8px;"></i>
                    <?php echo _l('case_information'); ?>
                </h4>
            </div>
            <div class="cases-info-card-body">
                <!-- Case Title and Number -->
                <div class="cases-grid cases-grid-2" style="margin-bottom: var(--cases-spacing-md);">
                    <div>
                        <h4 class="cases-text-primary" style="margin-bottom: var(--cases-spacing-xs);">
                            <?php echo htmlspecialchars($case['case_title']); ?>
                        </h4>
                        <p class="cases-text-muted cases-font-size-sm"><?php echo _l('case_title'); ?></p>
                    </div>
                    <div>
                        <div style="margin-bottom: var(--cases-spacing-xs);">
                            <span class="cases-status-badge cases-status-consultation">
                                <?php echo htmlspecialchars($case['case_number'] ?: 'Not assigned'); ?>
                            </span>
                        </div>
                        <p class="cases-text-muted cases-font-size-sm"><?php echo _l('case_number'); ?></p>
                    </div>
                </div>
                
                <div class="cases-border-top" style="padding-top: var(--cases-spacing-md);"></div>
                
                <!-- Court and Date Information -->
                <div class="cases-grid cases-grid-2" style="margin-top: var(--cases-spacing-md);">
                    <div>
                        <strong class="cases-text"><?php echo _l('court'); ?>:</strong><br>
                        <span class="cases-text-info cases-font-weight-medium">
                            <?php echo htmlspecialchars($case['court_display'] ?: 'Court not specified'); ?>
                        </span>
                    </div>
                    <div>
                        <strong class="cases-text"><?php echo _l('date_filed'); ?>:</strong><br>
                        <span class="cases-text-success cases-font-weight-medium">
                            <?php 
                            if ($case['date_filed']) {
                                echo _dt($case['date_filed']);
                            } else {
                                echo '<span class="cases-text-muted">Not filed yet</span>';
                            }
                            ?>
                        </span>
                    </div>
                </div>
                
                <div style="margin-top: var(--cases-spacing-md);">
                    <div>
                        <strong class="cases-text"><?php echo _l('date_created'); ?>:</strong><br>
                        <span class="cases-text-muted">
                            <?php echo _dt($case['date_created']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Documents Card with Tabs -->
        <div class="cases-card">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-file-text-o" style="margin-right: 8px; color: var(--cases-info);"></i>
                    <?php echo _l('case_documents'); ?>
                </h4>
                <span class="cases-count-badge"><?php echo count($documents); ?></span>
            </div>
            
            <!-- Document Tabs -->
            <div class="cases-document-tabs">
                <nav class="cases-tab-nav">
                    <button class="cases-tab-btn active" onclick="switchDocumentTab('all')" id="tab-all">
                        <i class="fa fa-files-o"></i>
                        All Documents (<?php echo count($documents); ?>)
                    </button>
                    <button class="cases-tab-btn" onclick="switchDocumentTab('case')" id="tab-case">
                        <i class="fa fa-file-text"></i>
                        Case Only (<?php echo count(array_filter($documents, function($d) { return $d['rel_type'] == 'case'; })); ?>)
                    </button>
                    <button class="cases-tab-btn" onclick="switchDocumentTab('hearings')" id="tab-hearings">
                        <i class="fa fa-calendar"></i>
                        By Hearings (<?php echo count(array_filter($documents, function($d) { return $d['rel_type'] == 'hearing'; })); ?>)
                    </button>
                </nav>
            </div>
            
            <div class="cases-card-body">
                <?php if (!empty($documents)) { ?>
                    <!-- Quick Actions Panel -->
                    <div class="cases-document-actions" style="margin-bottom: var(--cases-spacing-md); padding: var(--cases-spacing-sm); background: var(--cases-bg-secondary); border-radius: var(--cases-radius);">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--cases-spacing-sm);">
                            <div style="display: flex; align-items: center; gap: var(--cases-spacing-sm);">
                                <i class="fa fa-info-circle" style="color: var(--cases-info);"></i>
                                <span style="color: var(--cases-text); font-size: var(--cases-font-size-sm);">
                                    <?php echo count($documents); ?> documents across case and hearings
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
                            <input type="text" id="document-search-input" placeholder="Search documents by name, type, or date..." 
                                   style="width: 100%; padding: 8px; border: 1px solid var(--cases-border); border-radius: var(--cases-radius); font-size: var(--cases-font-size-sm);">
                        </div>
                    </div>
                    
                    <!-- Tab Content Areas -->
                    
                    <!-- All Documents Tab -->
                    <div id="tab-content-all" class="cases-tab-content active">
                        <div class="cases-table-wrapper">
                            <div class="cases-table-responsive">
                                <table class="cases-table" id="all-documents-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('file_name'); ?></th>
                                            <th><?php echo _l('document_context'); ?></th>
                                            <th><?php echo _l('document_uploaded_on'); ?></th>
                                            <th class="cases-text-center"><?php echo _l('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $document) { ?>
                                            <tr class="document-row" data-filename="<?php echo strtolower($document['file_name']); ?>" data-context="<?php echo strtolower($document['document_context']); ?>" data-rel-type="<?php echo $document['rel_type']; ?>">
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: var(--cases-spacing-xs);">
                                                        <i class="fa fa-file-<?php echo get_file_icon_class($document['filetype']); ?>" style="color: var(--cases-info);"></i>
                                                        <div>
                                                            <div class="cases-font-weight-medium"><?php echo htmlspecialchars($document['file_name']); ?></div>
                                                            <?php if (!empty($document['tag'])) { ?>
                                                                <small class="cases-text-muted"><?php echo htmlspecialchars($document['tag']); ?></small>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="cases-status-badge <?php echo $document['rel_type'] == 'case' ? 'cases-status-info' : 'cases-status-warning'; ?>">
                                                        <?php echo htmlspecialchars($document['document_context']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="cases-text-muted cases-font-size-sm">
                                                        <?php echo _dt($document['dateadded']); ?>
                                                    </span>
                                                </td>
                                                <td class="cases-text-center">
                                                    <div style="display: flex; gap: 4px; justify-content: center;">
                                                        <button onclick="previewDocument(<?php echo $document['id']; ?>, '<?php echo htmlspecialchars($document['file_name']); ?>')" 
                                                                class="cases-btn cases-btn-info cases-btn-sm" title="Preview">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <a href="<?php echo site_url('cases/Cases_client/download_document/' . $document['id']); ?>" 
                                                           class="cases-btn cases-btn-success cases-btn-sm" title="Download">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Case Documents Only Tab -->
                    <div id="tab-content-case" class="cases-tab-content" style="display: none;">
                        <div style="margin-bottom: var(--cases-spacing-md); padding: var(--cases-spacing-sm); background: var(--cases-info-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-info);">
                            <span style="color: var(--cases-info); font-size: var(--cases-font-size-sm);">
                                <i class="fa fa-info-circle"></i> Documents directly attached to this case
                            </span>
                        </div>
                        <div class="cases-grid cases-grid-responsive">
                            <?php 
                            $case_documents = array_filter($documents, function($d) { return $d['rel_type'] == 'case'; });
                            if (!empty($case_documents)) {
                                foreach ($case_documents as $document) { ?>
                                <div class="cases-card cases-hover-lift">
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
                            <?php }
                            } else { ?>
                                <div style="text-align: center; padding: var(--cases-spacing-lg); color: var(--cases-text-muted);">
                                    <i class="fa fa-file-o" style="font-size: 2rem; margin-bottom: var(--cases-spacing-sm);"></i>
                                    <div>No case-specific documents yet</div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <!-- Hearing Documents Tab -->
                    <div id="tab-content-hearings" class="cases-tab-content" style="display: none;">
                        <div style="margin-bottom: var(--cases-spacing-md); padding: var(--cases-spacing-sm); background: var(--cases-warning-bg); border-radius: var(--cases-radius); border-left: 3px solid var(--cases-warning);">
                            <span style="color: var(--cases-warning); font-size: var(--cases-font-size-sm);">
                                <i class="fa fa-calendar"></i> Documents organized by hearing dates
                            </span>
                        </div>
                        
                        <?php 
                        // Group hearing documents by hearing
                        $hearing_documents = array_filter($documents, function($d) { return $d['rel_type'] == 'hearing'; });
                        $grouped_hearings = [];
                        foreach ($hearing_documents as $doc) {
                            // Find hearing info from the document context
                            $grouped_hearings[$doc['rel_id']][] = $doc;
                        }
                        
                        if (!empty($grouped_hearings)) {
                            foreach ($hearings as $hearing) {
                                if (isset($grouped_hearings[$hearing['id']])) { ?>
                                    <div class="cases-card" style="margin-bottom: var(--cases-spacing-md);">
                                        <div class="cases-card-header">
                                            <h5 class="cases-card-title">
                                                <i class="fa fa-calendar" style="margin-right: 8px; color: var(--cases-warning);"></i>
                                                Hearing - <?php echo _dt($hearing['date']); ?>
                                                <?php if (!empty($hearing['time'])) { ?>
                                                    <span style="font-size: var(--cases-font-size-sm); color: var(--cases-text-muted);">at <?php echo $hearing['time']; ?></span>
                                                <?php } ?>
                                            </h5>
                                            <span class="cases-count-badge"><?php echo count($grouped_hearings[$hearing['id']]); ?></span>
                                        </div>
                                        <div class="cases-card-body">
                                            <?php if (!empty($hearing['hearing_purpose'])) { ?>
                                                <div style="margin-bottom: var(--cases-spacing-sm); padding: var(--cases-spacing-xs); background: var(--cases-bg-secondary); border-radius: var(--cases-radius);">
                                                    <strong>Purpose:</strong> <?php echo htmlspecialchars($hearing['hearing_purpose']); ?>
                                                </div>
                                            <?php } ?>
                                            
                                            <div class="cases-grid cases-grid-responsive">
                                                <?php foreach ($grouped_hearings[$hearing['id']] as $document) { ?>
                                                    <div class="cases-card cases-card-compact">
                                                        <div class="cases-card-body">
                                                            <div style="display: flex; align-items: center; gap: var(--cases-spacing-xs); margin-bottom: var(--cases-spacing-xs);">
                                                                <i class="fa fa-file-<?php echo get_file_icon_class($document['filetype']); ?>" style="color: var(--cases-warning);"></i>
                                                                <div class="cases-font-weight-medium"><?php echo htmlspecialchars($document['file_name']); ?></div>
                                                            </div>
                                                            <div style="font-size: var(--cases-font-size-xs); color: var(--cases-text-muted); margin-bottom: var(--cases-spacing-sm);">
                                                                <?php echo _dt($document['dateadded']); ?>
                                                            </div>
                                                            <div style="display: flex; gap: 4px;">
                                                                <button onclick="previewDocument(<?php echo $document['id']; ?>, '<?php echo htmlspecialchars($document['file_name']); ?>')" 
                                                                        class="cases-btn cases-btn-info cases-btn-sm">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                                <a href="<?php echo site_url('cases/Cases_client/download_document/' . $document['id']); ?>" 
                                                                   class="cases-btn cases-btn-success cases-btn-sm">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            }
                        } else { ?>
                            <div style="text-align: center; padding: var(--cases-spacing-lg); color: var(--cases-text-muted);">
                                <i class="fa fa-calendar-o" style="font-size: 2rem; margin-bottom: var(--cases-spacing-sm);"></i>
                                <div>No hearing documents yet</div>
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
                            <?php echo _l('no_documents_available'); ?>
                        </h5>
                        <p style="color: var(--cases-text-muted); font-size: var(--cases-font-size-base);">
                            <?php echo _l('contact_lawyer_for_details'); ?>
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Sidebar -->
    <div>
        <!-- Hearings Card -->
        <div class="cases-card" style="margin-bottom: var(--cases-spacing-lg);">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-calendar" style="margin-right: 8px; color: var(--cases-warning);"></i>
                    <?php echo _l('case_hearings'); ?>
                </h4>
                <span class="cases-count-badge"><?php echo count($hearings); ?></span>
            </div>
            <div class="cases-card-body">
                <?php if (!empty($hearings)) { ?>
                    <!-- Help Information -->
                    <div class="cases-info-card" style="background: var(--cases-info-bg); border-left: 3px solid var(--cases-info); margin-bottom: var(--cases-spacing-lg);">
                        <div class="cases-info-card-body" style="padding: var(--cases-spacing-sm);">
                            <div style="display: flex; align-items: center; gap: var(--cases-spacing-xs);">
                                <i class="fa fa-info-circle" style="color: var(--cases-info); font-size: var(--cases-font-size-sm);"></i>
                                <span style="color: var(--cases-info); font-size: var(--cases-font-size-sm);">
                                    <?php echo _l('client_hearings_help'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    $today = date('Y-m-d');
                    $upcoming_hearings = array_filter($hearings, function($h) use ($today) {
                        return $h['date'] >= $today;
                    });
                    $past_hearings = array_filter($hearings, function($h) use ($today) {
                        return $h['date'] < $today;
                    });
                    ?>
                    
                    <!-- Upcoming Hearings -->
                    <?php if (!empty($upcoming_hearings)) { ?>
                        <h5 class="cases-text-primary" style="margin-bottom: var(--cases-spacing-md);">
                            <i class="fa fa-clock-o" style="margin-right: 6px;"></i>
                            <?php echo _l('upcoming_hearings'); ?>
                        </h5>
                        <?php foreach ($upcoming_hearings as $hearing) { ?>
                            <div class="cases-card cases-card-compact" style="background: var(--cases-success-bg); border-left: 3px solid var(--cases-success); margin-bottom: var(--cases-spacing-sm);">
                                <div class="cases-card-body">
                                    <div class="cases-text-success cases-font-weight-semibold">
                                        <?php echo _dt($hearing['date']); ?>
                                        <?php if (!empty($hearing['time'])) { ?>
                                            <span class="cases-text-muted cases-font-size-sm">at <?php echo $hearing['time']; ?></span>
                                        <?php } ?>
                                    </div>
                                    <div style="margin-top: var(--cases-spacing-xs);">
                                        <span class="cases-status-badge cases-status-active"><?php echo htmlspecialchars($hearing['status']); ?></span>
                                    </div>
                                    <?php if (!empty($hearing['hearing_purpose'])) { ?>
                                        <div class="cases-text-muted cases-font-size-sm" style="margin-top: var(--cases-spacing-xs);">
                                            <?php echo htmlspecialchars($hearing['hearing_purpose']); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="cases-border-bottom" style="margin: var(--cases-spacing-md) 0;"></div>
                    <?php } ?>
                    
                    <!-- Past Hearings -->
                    <?php if (!empty($past_hearings)) { ?>
                        <h5 class="cases-text-muted" style="margin-bottom: var(--cases-spacing-md);">
                            <i class="fa fa-history" style="margin-right: 6px;"></i>
                            <?php echo _l('past_hearings'); ?>
                        </h5>
                        <?php foreach (array_slice($past_hearings, 0, 5) as $hearing) { ?>
                            <div class="cases-card cases-card-compact" style="background: var(--cases-bg-tertiary); border-left: 3px solid var(--cases-border); margin-bottom: var(--cases-spacing-sm);">
                                <div class="cases-card-body">
                                    <div class="cases-text-muted cases-font-weight-medium">
                                        <?php echo _dt($hearing['date']); ?>
                                        <?php if (!empty($hearing['time'])) { ?>
                                            <span class="cases-font-size-sm">at <?php echo $hearing['time']; ?></span>
                                        <?php } ?>
                                    </div>
                                    <div style="margin-top: var(--cases-spacing-xs);">
                                        <span class="cases-status-badge cases-status-inactive"><?php echo htmlspecialchars($hearing['status']); ?></span>
                                    </div>
                                    <?php if (!empty($hearing['hearing_purpose'])) { ?>
                                        <div class="cases-text-muted cases-font-size-sm" style="margin-top: var(--cases-spacing-xs);">
                                            <?php echo htmlspecialchars($hearing['hearing_purpose']); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <!-- Empty State -->
                    <div style="text-align: center; padding: var(--cases-spacing-lg) var(--cases-spacing-md); background: var(--cases-bg-secondary); border: 1px solid var(--cases-border); border-radius: var(--cases-radius);">
                        <div style="margin-bottom: var(--cases-spacing-md);">
                            <i class="fa fa-calendar-o" style="font-size: 2.5rem; color: var(--cases-warning); opacity: 0.4;"></i>
                        </div>
                        <h6 style="margin-bottom: var(--cases-spacing-xs); color: var(--cases-text); font-weight: 600;">
                            <?php echo _l('no_hearings_scheduled'); ?>
                        </h6>
                        <p style="color: var(--cases-text-muted); font-size: var(--cases-font-size-sm);">
                            <?php echo _l('contact_lawyer_for_details'); ?>
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- Case Summary Card -->
        <div class="cases-stat-card">
            <div class="cases-card-header">
                <h4 class="cases-card-title">
                    <i class="fa fa-info-circle" style="margin-right: 8px; color: var(--cases-info);"></i>
                    <?php echo _l('case_summary'); ?>
                </h4>
            </div>
            <div class="cases-card-body">
                <div class="cases-grid" style="grid-template-columns: 1fr; gap: var(--cases-spacing-sm);">
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text"><?php echo _l('total_documents'); ?>:</strong>
                        <span class="cases-count-badge"><?php echo count($documents); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text"><?php echo _l('total_hearings'); ?>:</strong>
                        <span class="cases-count-badge"><?php echo count($hearings); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0; border-bottom: 1px solid var(--cases-border-light);">
                        <strong class="cases-text"><?php echo _l('upcoming_hearings'); ?>:</strong>
                        <span class="cases-count-badge"><?php echo count($upcoming_hearings ?? []); ?></span>
                    </div>
                    <div class="cases-flex cases-flex-between" style="padding: var(--cases-spacing-sm) 0;">
                        <strong class="cases-text"><?php echo _l('past_hearings'); ?>:</strong>
                        <span class="cases-count-badge"><?php echo count($past_hearings ?? []); ?></span>
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
// Global variables for document management
let currentPreviewDocumentId = null;
let documentSearchTimeout = null;

$(document).ready(function() {
    // Initialize DataTable for all documents table only
    $('#all-documents-table').DataTable({
        "pageLength": 10,
        "order": [[2, "desc"]], // Order by upload date
        "columnDefs": [
            {
                "targets": [3], // Actions column
                "orderable": false,
                "searchable": false,
                "className": "cases-text-center"
            }
        ],
        "language": {
            "emptyTable": "<?php echo _l('no_documents_available'); ?>",
            "info": "Showing _START_ to _END_ of _TOTAL_ documents",
            "infoEmpty": "Showing 0 to 0 of 0 documents",
            "lengthMenu": "Show _MENU_ documents per page",
            "search": "Search documents:",
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
    
    // Setup document search functionality
    $('#document-search-input').on('input', function() {
        clearTimeout(documentSearchTimeout);
        const searchTerm = $(this).val().toLowerCase();
        
        documentSearchTimeout = setTimeout(function() {
            filterDocuments(searchTerm);
        }, 300);
    });
});

// Document Tab Management
function switchDocumentTab(tabName) {
    // Update tab buttons
    $('.cases-tab-btn').removeClass('active');
    $('#tab-' + tabName).addClass('active');
    
    // Update tab content
    $('.cases-tab-content').hide();
    $('#tab-content-' + tabName).show();
    
    // Clear search when switching tabs
    $('#document-search-input').val('');
    filterDocuments('');
}

// Document Search and Filter
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
    $('.document-row').each(function() {
        const row = $(this);
        const filename = row.data('filename') || '';
        const context = row.data('context') || '';
        const text = (filename + ' ' + context).toLowerCase();
        
        if (searchTerm === '' || text.includes(searchTerm)) {
            row.show();
        } else {
            row.hide();
        }
    });
    
    // Update visible count
    const visibleCount = $('.document-row:visible').length;
    const totalCount = $('.document-row').length;
    
    if (searchTerm !== '') {
        $('.cases-document-actions span').first().html(
            '<i class="fa fa-search"></i> ' + visibleCount + ' of ' + totalCount + ' documents match "' + searchTerm + '"'
        );
    } else {
        $('.cases-document-actions span').first().html(
            '<i class="fa fa-info-circle"></i> <?php echo count($documents); ?> documents across case and hearings'
        );
    }
}

// Document Preview Functions
function previewDocument(documentId, fileName) {
    currentPreviewDocumentId = documentId;
    $('#document-preview-title').text('Preview: ' + fileName);
    $('#document-preview-body').html('<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin"></i> Loading preview...</div>');
    $('#document-preview-modal').show();
    
    // You can implement actual preview loading here
    // For now, show basic info
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
function printDocumentList() {
    const activeTab = $('.cases-tab-btn.active').text().trim();
    let content = '<h2>Case Documents - ' + activeTab + '</h2>';
    content += '<h3><?php echo htmlspecialchars($case["case_title"]); ?></h3>';
    content += '<table border="1" style="width: 100%; border-collapse: collapse;">';
    content += '<tr><th>File Name</th><th>Context</th><th>Upload Date</th></tr>';
    
    $('.document-row:visible').each(function() {
        const fileName = $(this).find('.cases-font-weight-medium').text();
        const context = $(this).find('.cases-status-badge').text();
        const date = $(this).find('.cases-text-muted.cases-font-size-sm').text();
        content += '<tr><td>' + fileName + '</td><td>' + context + '</td><td>' + date + '</td></tr>';
    });
    
    content += '</table>';
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(
        '<html><head><title>Document List</title>' +
        '<style>body { font-family: Arial, sans-serif; } table { width: 100%; } th, td { padding: 8px; text-align: left; }</style>' +
        '</head><body>' + content + '</body></html>'
    );
    printWindow.document.close();
    printWindow.print();
}

// Add fade-in animation to cards
$('.cases-card, .cases-info-card, .cases-stat-card').each(function(index) {
    $(this).addClass('cases-fade-in');
    setTimeout(() => {
        $(this).addClass('active');
    }, index * 100);
});

<?php if (!function_exists('get_file_icon_class')) { ?>
// Fallback function for file icons if not available in Perfex CRM
function getFileIconClass(filetype) {
    if (!filetype) return 'o';
    
    var type = filetype.toLowerCase();
    if (type.includes('pdf')) return 'pdf-o';
    if (type.includes('word') || type.includes('doc')) return 'word-o';
    if (type.includes('excel') || type.includes('sheet')) return 'excel-o';
    if (type.includes('image') || type.includes('jpg') || type.includes('png')) return 'image-o';
    if (type.includes('text')) return 'text-o';
    return 'file-o';
}
<?php } ?>
</script>

<style>
/* Document Tabs Styling */
.cases-document-tabs {
    border-bottom: 2px solid var(--cases-border-light);
    margin-bottom: var(--cases-spacing-lg);
}

.cases-tab-nav {
    display: flex;
    gap: 0;
    background: var(--cases-bg-secondary);
    border-radius: var(--cases-radius) var(--cases-radius) 0 0;
    padding: 0;
    overflow-x: auto;
}

.cases-tab-btn {
    background: transparent;
    border: none;
    padding: var(--cases-spacing-md) var(--cases-spacing-lg);
    cursor: pointer;
    font-size: var(--cases-font-size-sm);
    color: var(--cases-text-muted);
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-width: fit-content;
}

.cases-tab-btn:hover {
    background: var(--cases-bg-hover);
    color: var(--cases-text);
}

.cases-tab-btn.active {
    background: var(--cases-bg-primary);
    color: var(--cases-primary);
    border-bottom-color: var(--cases-primary);
    font-weight: 600;
}

.cases-tab-btn i {
    margin-right: 6px;
}

.cases-tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Document Actions Styling */
.cases-document-actions {
    position: relative;
}

#document-search-box {
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from { opacity: 0; max-height: 0; }
    to { opacity: 1; max-height: 50px; }
}

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

/* Enhanced Card Styling */
.cases-card-compact {
    border: 1px solid var(--cases-border-light);
    border-radius: var(--cases-radius);
    background: var(--cases-bg-primary);
    transition: all 0.2s ease;
}

.cases-card-compact:hover {
    border-color: var(--cases-border);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

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

/* Mobile responsive */
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
}
</style>