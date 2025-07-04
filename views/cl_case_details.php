<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.case-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.case-header {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 4px solid #3498db;
}

.case-header h1 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.case-number {
    color: #7f8c8d;
    font-size: 16px;
    margin-bottom: 20px;
}

.case-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.meta-item {
    display: flex;
    flex-direction: column;
}

.meta-label {
    font-size: 12px;
    color: #95a5a6;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.meta-value {
    font-size: 16px;
    color: #2c3e50;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #27ae60;
    color: white;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.pending {
    background: #f39c12;
}

.status-badge.closed {
    background: #95a5a6;
}

.main-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.timeline-section {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-title {
    margin: 0 0 25px 0;
    color: #2c3e50;
    font-size: 20px;
    font-weight: 600;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding: 20px 0 20px 40px;
    border-left: 2px solid #ecf0f1;
}

.timeline-item:last-child {
    border-left: 2px solid transparent;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 25px;
    width: 14px;
    height: 14px;
    background: #3498db;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #3498db;
}

.timeline-date {
    font-size: 12px;
    color: #95a5a6;
    margin-bottom: 5px;
}

.timeline-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.timeline-description {
    color: #7f8c8d;
    font-size: 14px;
    line-height: 1.5;
}

.timeline-documents {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.timeline-documents h6 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 14px;
    font-weight: 600;
}

.document-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.document-item {
    padding: 5px 0;
    border-bottom: 1px solid #e9ecef;
}

.document-item:last-child {
    border-bottom: none;
}

.document-link {
    color: #3498db;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.document-link:hover {
    text-decoration: underline;
}

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-card h3 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-item {
    padding: 10px 0;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 14px;
    color: #7f8c8d;
}

.info-value {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
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
}

.btn:hover {
    background: #2980b9;
    text-decoration: none;
    color: white;
}

.btn-secondary {
    background: #95a5a6;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #95a5a6;
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

@media (max-width: 768px) {
    .case-container {
        padding: 15px;
    }
    
    .main-content {
        grid-template-columns: 1fr;
    }
    
    .case-meta {
        grid-template-columns: 1fr;
    }
    
    .timeline-item {
        padding-left: 30px;
    }
}
</style>

<div class="case-container">
    <!-- Back Navigation -->
    <a href="<?php echo site_url('cases/Cl_cases'); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Case Header -->
    <div class="case-header">
        <h1><?php echo isset($case['case_title']) ? htmlspecialchars($case['case_title']) : 'Case Details'; ?></h1>
        <div class="case-number">Case #<?php echo isset($case['case_number']) ? htmlspecialchars($case['case_number']) : 'N/A'; ?></div>
        
        <div class="case-meta">
            <div class="meta-item">
                <div class="meta-label">Court</div>
                <div class="meta-value"><?php echo isset($case['court_name']) ? htmlspecialchars($case['court_name']) : 'N/A'; ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Case Type</div>
                <div class="meta-value"><?php echo isset($case['case_type']) ? htmlspecialchars($case['case_type']) : 'N/A'; ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Filing Date</div>
                <div class="meta-value"><?php echo isset($case['filing_date']) ? date('M d, Y', strtotime($case['filing_date'])) : 'N/A'; ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Status</div>
                <div class="meta-value">
                    <span class="status-badge <?php echo isset($case['status']) ? strtolower($case['status']) : 'pending'; ?>">
                        <?php echo isset($case['status']) ? htmlspecialchars($case['status']) : 'Pending'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <!-- Timeline Section -->
        <div class="timeline-section">
            <h2 class="section-title"><i class="fa fa-clock-o"></i> Case Timeline</h2>
            
            <?php if (isset($timeline) && !empty($timeline)): ?>
                <div class="timeline">
                    <?php foreach ($timeline as $event): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-date"><?php echo date('M d, Y', strtotime($event['date'])); ?></div>
                            <div class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></div>
                            <div class="timeline-description"><?php echo htmlspecialchars($event['description']); ?></div>
                            
                            <?php if (isset($event['documents']) && !empty($event['documents'])): ?>
                                <div class="timeline-documents">
                                    <h6><i class="fa fa-files-o"></i> Related Documents (<?php echo count($event['documents']); ?>)</h6>
                                    <ul class="document-list">
                                        <?php foreach ($event['documents'] as $doc): ?>
                                            <li class="document-item">
                                                <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="document-link">
                                                    <i class="fa fa-file-text-o"></i>
                                                    <?php echo htmlspecialchars($doc['name']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-info-circle fa-3x"></i>
                    <p>No timeline events available yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Case Information -->
            <div class="info-card">
                <h3><i class="fa fa-info-circle"></i> Case Information</h3>
                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Judge</span>
                        <span class="info-value"><?php echo isset($case['judge_name']) ? htmlspecialchars($case['judge_name']) : 'N/A'; ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Next Hearing</span>
                        <span class="info-value"><?php echo isset($case['next_hearing']) ? date('M d, Y', strtotime($case['next_hearing'])) : 'No scheduled hearing'; ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Priority</span>
                        <span class="info-value"><?php echo isset($case['priority']) ? htmlspecialchars($case['priority']) : 'Normal'; ?></span>
                    </li>
                </ul>
                
                <div class="action-buttons">
                    <a href="<?php echo site_url('cases/Cl_cases/consultations?case=' . (isset($case['id']) ? $case['id'] : '')); ?>" class="btn">
                        <i class="fa fa-comments"></i> Schedule Consultation
                    </a>
                    <a href="<?php echo site_url('cases/Cl_cases/documents?case=' . (isset($case['id']) ? $case['id'] : '')); ?>" class="btn btn-secondary">
                        <i class="fa fa-files-o"></i> View All Documents
                    </a>
                </div>
            </div>

            <!-- Document Categories -->
            <?php if (isset($document_categories) && !empty($document_categories)): ?>
                <div class="info-card">
                    <h3><i class="fa fa-folder-o"></i> Document Categories</h3>
                    <ul class="info-list">
                        <?php foreach ($document_categories as $category => $docs): ?>
                            <li class="info-item">
                                <span class="info-label"><?php echo ucfirst($category); ?></span>
                                <span class="info-value"><?php echo count($docs); ?> files</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
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
    
    $.ajax({
        url: '<?php echo site_url("cases/Cl_cases/preview_document"); ?>/' + docId,
        type: 'GET',
        success: function(response) {
            $('#documentPreviewContent').html(response);
            $('#documentPreviewModal').modal('show');
        },
        error: function() {
            alert('Error loading document preview');
        }
    });
}
</script>