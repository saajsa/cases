<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.consultation-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.consultation-header {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 4px solid #3498db;
}

.consultation-header h1 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.consultation-header .subtitle {
    color: #7f8c8d;
    font-size: 16px;
    margin: 0;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #3498db;
    text-decoration: none;
    margin-bottom: 20px;
    font-size: 14px;
}

.back-link:hover {
    text-decoration: underline;
}

.main-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.consultation-info {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-title {
    margin: 0 0 25px 0;
    color: #2c3e50;
    font-size: 20px;
    font-weight: 600;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 12px;
    color: #95a5a6;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.info-value {
    font-size: 16px;
    color: #2c3e50;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
    background: #27ae60;
}

.status-badge.scheduled {
    background: #f39c12;
}

.status-badge.completed {
    background: #27ae60;
}

.status-badge.cancelled {
    background: #e74c3c;
}

.status-badge.pending {
    background: #95a5a6;
}

.status-badge.general {
    background: #95a5a6;
}

.status-badge.consultation {
    background: #3498db;
}

.status-badge.litigation {
    background: #e74c3c;
}

.consultation-notes {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.note-section {
    margin-bottom: 30px;
}

.note-section:last-child {
    margin-bottom: 0;
}

.note-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 15px 0;
}

.note-content {
    background: #f8f9fa;
    padding: 20px;
    border: 1px solid #e9ecef;
    color: #2c3e50;
    line-height: 1.6;
    font-size: 14px;
}

.action-items {
    margin-top: 20px;
}

.action-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px 0;
    border-bottom: 1px solid #ecf0f1;
}

.action-item:last-child {
    border-bottom: none;
}

.action-checkbox {
    font-size: 16px;
    margin-top: 2px;
}

.action-checkbox.completed {
    color: #27ae60;
}

.action-checkbox.pending {
    color: #95a5a6;
}

.action-content {
    flex: 1;
}

.action-text {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-size: 14px;
}

.action-text.completed {
    text-decoration: line-through;
    color: #95a5a6;
}

.action-meta {
    font-size: 12px;
    color: #7f8c8d;
    margin: 0;
}

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.sidebar-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.sidebar-card h3 {
    margin: 0 0 20px 0;
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
}

.meeting-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.btn {
    padding: 12px 20px;
    border: none;
    background: #3498db;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn:hover {
    background: #2980b9;
    text-decoration: none;
    color: white;
}

.btn-success {
    background: #27ae60;
}

.btn-success:hover {
    background: #219a52;
}

.btn-secondary {
    background: #95a5a6;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.consultation-meta {
    list-style: none;
    padding: 0;
    margin: 0;
}

.consultation-meta li {
    padding: 10px 0;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.consultation-meta li:last-child {
    border-bottom: none;
}

.meta-label {
    font-size: 14px;
    color: #7f8c8d;
}

.meta-value {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.documents-section {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.document-groups {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.document-group {
    border: 1px solid #e9ecef;
    background: #f8f9fa;
}

.document-group-header {
    padding: 15px 20px;
    background: #e9ecef;
    border-bottom: 1px solid #dee2e6;
}

.document-group-title {
    margin: 0;
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
}

.document-list {
    padding: 0;
    margin: 0;
    list-style: none;
}

.document-item {
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.document-item:last-child {
    border-bottom: none;
}

.document-info {
    display: flex;
    flex-direction: column;
}

.document-name {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
    margin: 0 0 3px 0;
}

.document-meta {
    font-size: 12px;
    color: #7f8c8d;
    margin: 0;
}

.document-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 6px 12px;
    font-size: 12px;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #ddd;
    background: #fff;
    color: #2c3e50;
}

.action-btn:hover {
    background: #f8f9fa;
    text-decoration: none;
    color: #2c3e50;
}

.action-btn.primary {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.action-btn.primary:hover {
    background: #2980b9;
    border-color: #2980b9;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #95a5a6;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

.empty-state h4 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 18px;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Modal z-index fixes */
.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
}

.modal-dialog {
    z-index: 1060 !important;
    margin: 30px auto;
    max-width: 90%;
    width: 800px;
}

/* Ensure modal content is above backdrop */
#documentPreviewModal {
    z-index: 1070 !important;
}

#documentPreviewModal .modal-dialog {
    z-index: 1080 !important;
}

#documentPreviewModal .modal-content {
    z-index: 1090 !important;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

/* Modal header styling */
#documentPreviewModal .modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}

#documentPreviewModal .modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    flex: 1;
    text-align: center;
}

#documentPreviewModal .modal-header .close {
    position: absolute;
    right: 15px;
    top: 15px;
    font-size: 24px;
    font-weight: bold;
    line-height: 1;
    color: #95a5a6;
    text-shadow: none;
    opacity: 1;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#documentPreviewModal .modal-header .close:hover {
    color: #e74c3c;
    opacity: 1;
}

#documentPreviewModal .modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}

#documentPreviewModal .modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 15px 20px;
    text-align: right;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}

#documentPreviewModal .modal-footer .btn {
    margin-left: 10px;
}

/* Responsive modal */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
        max-width: calc(100% - 20px);
        width: auto;
    }
    
    #documentPreviewModal .modal-header {
        padding: 12px 15px;
    }
    
    #documentPreviewModal .modal-title {
        font-size: 16px;
        padding-right: 40px;
    }
    
    #documentPreviewModal .modal-header .close {
        right: 12px;
        top: 12px;
        font-size: 20px;
    }
    
    #documentPreviewModal .modal-body {
        padding: 15px;
        max-height: 60vh;
    }
    
    #documentPreviewModal .modal-footer {
        padding: 12px 15px;
    }
}

@media (max-width: 480px) {
    .modal-dialog {
        margin: 5px;
        max-width: calc(100% - 10px);
    }
    
    #documentPreviewModal .modal-title {
        font-size: 14px;
    }
    
    #documentPreviewModal .modal-body {
        max-height: 50vh;
    }
}

@media (max-width: 768px) {
    .consultation-container {
        padding: 15px;
    }
    
    .main-content {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .meeting-actions {
        flex-direction: column;
    }
    
    .document-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .document-actions {
        align-self: stretch;
        justify-content: space-between;
    }
}
</style>

<div class="consultation-container">
    <!-- Back Navigation -->
    <a href="<?php echo site_url('cases/Cl_cases/consultations'); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i> Back to Consultations
    </a>

    <!-- Consultation Header -->
    <div class="consultation-header">
        <h1><i class="fa fa-comments"></i> Consultation Details</h1>
        <p class="subtitle">View consultation information, notes, and related documents</p>
    </div>

    <div class="main-content">
        <!-- Main Content -->
        <div>
            <!-- Consultation Information -->
            <div class="consultation-info">
                <h2 class="section-title"><i class="fa fa-info-circle"></i> Consultation Information</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Subject</div>
                        <div class="info-value"><?php echo isset($consultation['subject']) ? htmlspecialchars($consultation['subject']) : 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date & Time</div>
                        <div class="info-value">
                            <?php echo isset($consultation['consultation_date']) ? date('M d, Y \a\t H:i A', strtotime($consultation['consultation_date'])) : 'N/A'; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Duration</div>
                        <div class="info-value"><?php echo isset($consultation['duration']) ? $consultation['duration'] . ' minutes' : 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge <?php echo isset($consultation['status']) ? strtolower($consultation['status']) : 'pending'; ?>">
                                <?php echo isset($consultation['status']) ? htmlspecialchars($consultation['status']) : 'Pending'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Consultation Type</div>
                        <div class="info-value">
                            <span class="status-badge <?php echo strtolower($consultation['consultation_type'] ?? 'general'); ?>">
                                <?php echo isset($consultation['consultation_type']) ? htmlspecialchars($consultation['consultation_type']) : 'General'; ?>
                            </span>
                        </div>
                    </div>
                    <?php if (isset($consultation['case_title'])): ?>
                        <div class="info-item">
                            <div class="info-label">Related Case</div>
                            <div class="info-value">
                                <a href="<?php echo site_url('cases/Cl_cases/case_details/' . $consultation['case_id']); ?>" style="color: #3498db; text-decoration: none;">
                                    <i class="fa fa-balance-scale"></i> <?php echo htmlspecialchars($consultation['case_title']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Meeting Notes & Summary -->
            <div class="consultation-notes">
                <h2 class="section-title"><i class="fa fa-file-text-o"></i> Meeting Notes & Summary</h2>
                
                <?php if (isset($consultation['summary']) && !empty($consultation['summary'])): ?>
                    <div class="note-section">
                        <h3 class="note-title">Summary</h3>
                        <div class="note-content">
                            <?php echo nl2br(htmlspecialchars(strip_tags($consultation['summary']))); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($consultation['notes']) && !empty($consultation['notes'])): ?>
                    <div class="note-section">
                        <h3 class="note-title">Detailed Notes</h3>
                        <div class="note-content">
                            <?php echo nl2br(htmlspecialchars(strip_tags($consultation['notes']))); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($consultation['action_items']) && !empty($consultation['action_items'])): ?>
                    <div class="note-section">
                        <h3 class="note-title">Action Items & Follow-ups</h3>
                        <div class="action-items">
                            <?php foreach ($consultation['action_items'] as $item): ?>
                                <div class="action-item">
                                    <div class="action-checkbox <?php echo $item['completed'] ? 'completed' : 'pending'; ?>">
                                        <i class="fa fa-<?php echo $item['completed'] ? 'check-square-o' : 'square-o'; ?>"></i>
                                    </div>
                                    <div class="action-content">
                                        <p class="action-text <?php echo $item['completed'] ? 'completed' : ''; ?>">
                                            <?php echo htmlspecialchars($item['description']); ?>
                                        </p>
                                        <?php if (isset($item['due_date'])): ?>
                                            <p class="action-meta">
                                                <i class="fa fa-calendar"></i> Due: <?php echo date('M d, Y', strtotime($item['due_date'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ((!isset($consultation['summary']) || empty($consultation['summary'])) && 
                         (!isset($consultation['notes']) || empty($consultation['notes'])) && 
                         (!isset($consultation['action_items']) || empty($consultation['action_items']))): ?>
                    <div class="empty-state">
                        <i class="fa fa-info-circle"></i>
                        <h4>No Meeting Notes Available</h4>
                        <p>Meeting notes and summary will appear here after the consultation.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Documents Section -->
            <div class="documents-section">
                <h2 class="section-title">
                    <i class="fa fa-files-o"></i> Related Documents
                    <?php if (isset($consultation_documents) && !empty($consultation_documents)): ?>
                        <span style="font-size: 12px; font-weight: normal; color: #7f8c8d;">
                            (<?php echo count($consultation_documents); ?> files)
                        </span>
                    <?php endif; ?>
                </h2>
                
                <?php if (isset($consultation_documents) && !empty($consultation_documents)): ?>
                    <div class="document-groups">
                        <!-- Pre-Meeting Documents -->
                        <?php 
                        $pre_meeting = array_filter($consultation_documents, function($doc) {
                            return $doc['document_type'] == 'pre_meeting';
                        });
                        if (!empty($pre_meeting)): 
                        ?>
                            <div class="document-group">
                                <div class="document-group-header">
                                    <h4 class="document-group-title">
                                        <i class="fa fa-folder-o"></i> Pre-Meeting Documents (<?php echo count($pre_meeting); ?>)
                                    </h4>
                                </div>
                                <ul class="document-list">
                                    <?php foreach ($pre_meeting as $doc): ?>
                                        <li class="document-item">
                                            <div class="document-info">
                                                <p class="document-name">
                                                    <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i>
                                                    <?php echo htmlspecialchars($doc['name']); ?>
                                                </p>
                                                <p class="document-meta">
                                                    <?php echo isset($doc['size']) ? $doc['size'] . ' • ' : ''; ?>
                                                    <?php echo date('M d, Y', strtotime($doc['upload_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="document-actions">
                                                <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="action-btn primary">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $doc['id']); ?>" class="action-btn">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Meeting Materials -->
                        <?php 
                        $meeting_materials = array_filter($consultation_documents, function($doc) {
                            return $doc['document_type'] == 'meeting_materials';
                        });
                        if (!empty($meeting_materials)): 
                        ?>
                            <div class="document-group">
                                <div class="document-group-header">
                                    <h4 class="document-group-title">
                                        <i class="fa fa-folder-o"></i> Meeting Materials (<?php echo count($meeting_materials); ?>)
                                    </h4>
                                </div>
                                <ul class="document-list">
                                    <?php foreach ($meeting_materials as $doc): ?>
                                        <li class="document-item">
                                            <div class="document-info">
                                                <p class="document-name">
                                                    <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i>
                                                    <?php echo htmlspecialchars($doc['name']); ?>
                                                </p>
                                                <p class="document-meta">
                                                    <?php echo isset($doc['size']) ? $doc['size'] . ' • ' : ''; ?>
                                                    <?php echo date('M d, Y', strtotime($doc['upload_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="document-actions">
                                                <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="action-btn primary">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $doc['id']); ?>" class="action-btn">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Follow-up Documents -->
                        <?php 
                        $follow_up = array_filter($consultation_documents, function($doc) {
                            return $doc['document_type'] == 'follow_up';
                        });
                        if (!empty($follow_up)): 
                        ?>
                            <div class="document-group">
                                <div class="document-group-header">
                                    <h4 class="document-group-title">
                                        <i class="fa fa-folder-o"></i> Follow-up Documents (<?php echo count($follow_up); ?>)
                                    </h4>
                                </div>
                                <ul class="document-list">
                                    <?php foreach ($follow_up as $doc): ?>
                                        <li class="document-item">
                                            <div class="document-info">
                                                <p class="document-name">
                                                    <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i>
                                                    <?php echo htmlspecialchars($doc['name']); ?>
                                                </p>
                                                <p class="document-meta">
                                                    <?php echo isset($doc['size']) ? $doc['size'] . ' • ' : ''; ?>
                                                    <?php echo date('M d, Y', strtotime($doc['upload_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="document-actions">
                                                <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="action-btn primary">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $doc['id']); ?>" class="action-btn">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-file-o"></i>
                        <h4>No Documents</h4>
                        <p>Documents related to this consultation will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Quick Actions -->
            <div class="sidebar-card">
                <h3><i class="fa fa-bolt"></i> Quick Actions</h3>
                
                <div class="meeting-actions">
                    <?php if (isset($consultation['status']) && $consultation['status'] == 'Scheduled' && isset($consultation['meeting_link'])): ?>
                        <a href="<?php echo $consultation['meeting_link']; ?>" target="_blank" class="btn btn-success">
                            <i class="fa fa-video-camera"></i> Join Meeting
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo site_url('cases/Cl_cases/consultations'); ?>" class="btn btn-secondary">
                        <i class="fa fa-list"></i> All Consultations
                    </a>
                    
                    <?php if (isset($consultation['case_id'])): ?>
                        <a href="<?php echo site_url('cases/Cl_cases/case_details/' . $consultation['case_id']); ?>" class="btn">
                            <i class="fa fa-balance-scale"></i> View Case
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Consultation Details -->
            <div class="sidebar-card">
                <h3><i class="fa fa-info-circle"></i> Details</h3>
                
                <ul class="consultation-meta">
                    <li>
                        <span class="meta-label">Scheduled By</span>
                        <span class="meta-value"><?php echo isset($consultation['scheduled_by']) ? htmlspecialchars($consultation['scheduled_by']) : 'System'; ?></span>
                    </li>
                    <li>
                        <span class="meta-label">Created</span>
                        <span class="meta-value"><?php echo isset($consultation['date_created']) ? date('M d, Y', strtotime($consultation['date_created'])) : 'N/A'; ?></span>
                    </li>
                    <li>
                        <span class="meta-label">Last Updated</span>
                        <span class="meta-value"><?php echo isset($consultation['last_updated']) ? date('M d, Y', strtotime($consultation['last_updated'])) : 'N/A'; ?></span>
                    </li>
                    <?php if (isset($consultation['location']) && !empty($consultation['location'])): ?>
                        <li>
                            <span class="meta-label">Location</span>
                            <span class="meta-value"><?php echo htmlspecialchars($consultation['location']); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Document Preview Modal -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Document Preview</h4>
            </div>
            <div class="modal-body" id="documentPreviewContent">
                <!-- Document content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function previewDocument(docId) {
    if (!docId) {
        alert('Document ID not provided');
        return;
    }
    
    // Close any existing modals first
    $('.modal').modal('hide');
    
    $.ajax({
        url: '<?php echo site_url("cases/Cl_cases/preview_document"); ?>/' + docId,
        type: 'GET',
        beforeSend: function() {
            $('#documentPreviewContent').html('<div style="text-align: center; padding: 50px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Loading document...</div>');
        },
        success: function(response) {
            $('#documentPreviewContent').html(response);
            
            // Ensure proper z-index and show modal
            $('#documentPreviewModal').css('z-index', 1070);
            $('#documentPreviewModal').modal({
                backdrop: true,
                keyboard: true,
                focus: true,
                show: true
            });
            
            // Force modal to front
            setTimeout(function() {
                $('#documentPreviewModal').css('z-index', 1070);
                $('.modal-backdrop').css('z-index', 1040);
            }, 100);
        },
        error: function() {
            alert('Error loading document preview');
        }
    });
}

// Document ready functions
$(document).ready(function() {
    // Fix modal backdrop issues
    $(document).on('show.bs.modal', '#documentPreviewModal', function() {
        var zIndex = 1070;
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 10);
        }, 0);
    });
    
    // Clean up on modal close
    $(document).on('hidden.bs.modal', '#documentPreviewModal', function() {
        $(this).removeData('bs.modal');
        $('#documentPreviewContent').empty();
    });
});
</script>