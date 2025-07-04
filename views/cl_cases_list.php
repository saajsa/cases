<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.dashboard-header {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.dashboard-header h1 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.dashboard-header p {
    margin: 0;
    color: #7f8c8d;
    font-size: 16px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    border-left: 4px solid #3498db;
}

.stat-number {
    font-size: 36px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 8px;
}

.stat-label {
    color: #7f8c8d;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.action-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
    display: block;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
    color: inherit;
}

.action-card h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 18px;
}

.action-card p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
}

.content-section {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-title {
    margin: 0 0 20px 0;
    color: #2c3e50;
    font-size: 20px;
    font-weight: 600;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #ecf0f1;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-date {
    font-size: 12px;
    color: #95a5a6;
    margin-bottom: 5px;
}

.activity-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.activity-description {
    color: #7f8c8d;
    font-size: 14px;
    line-height: 1.4;
}

.document-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.document-group {
    background: #f8f9fa;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.document-group h4 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 16px;
}

.document-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.document-item {
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.document-item:last-child {
    border-bottom: none;
}

.document-link {
    color: #3498db;
    text-decoration: none;
    font-size: 14px;
}

.document-link:hover {
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #95a5a6;
}

.document-date {
    display: block;
    color: #95a5a6;
    font-size: 11px;
    margin-top: 3px;
}

.document-owner-type {
    color: #3498db;
    font-weight: 500;
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
    .dashboard-container {
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .document-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <h1><i class="fa fa-tachometer"></i> My Cases Dashboard</h1>
        <p>Welcome back! Here's your case summary and recent activity.</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo isset($stats['total_cases']) ? $stats['total_cases'] : '0'; ?></div>
            <div class="stat-label">Total Cases</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo isset($stats['active_consultations']) ? $stats['active_consultations'] : '0'; ?></div>
            <div class="stat-label">Active Consultations</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo isset($stats['total_documents']) ? $stats['total_documents'] : '0'; ?></div>
            <div class="stat-label">Documents</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo isset($stats['upcoming_hearings']) ? $stats['upcoming_hearings'] : '0'; ?></div>
            <div class="stat-label">Upcoming Hearings</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="actions-grid">
        <a href="<?php echo site_url('cases/Cl_cases/cases'); ?>" class="action-card">
            <h3><i class="fa fa-balance-scale"></i> View My Cases</h3>
            <p>Review case details, progress, and timeline</p>
        </a>
        <a href="<?php echo site_url('cases/Cl_cases/consultations'); ?>" class="action-card">
            <h3><i class="fa fa-comments"></i> Consultations</h3>
            <p>Join meetings, view notes, and schedule appointments</p>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="content-section">
        <h2 class="section-title"><i class="fa fa-clock-o"></i> Recent Activity</h2>
        <?php if (isset($recent_activity) && !empty($recent_activity)): ?>
            <?php foreach (array_slice($recent_activity, 0, 5) as $activity): ?>
                <div class="activity-item">
                    <div class="activity-date"><?php echo date('M d, Y - H:i', strtotime($activity['date'])); ?></div>
                    <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                    <div class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-info-circle fa-3x"></i>
                <p>No recent activity to display.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- My Documents -->
    <div class="content-section">
        <h2 class="section-title"><i class="fa fa-files-o"></i> My Documents</h2>
        <?php if (isset($client_documents) && !empty($client_documents)): ?>
            <div class="document-grid">
                <!-- Client-Owned Documents -->
                <?php if (!empty($client_documents['client_owned'])): ?>
                    <div class="document-group">
                        <h4><i class="fa fa-user"></i> My Documents (<?php echo count($client_documents['client_owned']); ?>)</h4>
                        <ul class="document-list">
                            <?php foreach (array_slice($client_documents['client_owned'], 0, 5) as $doc): ?>
                                <li class="document-item">
                                    <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="document-link">
                                        <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i> 
                                        <?php echo htmlspecialchars($doc['name']); ?>
                                    </a>
                                    <small class="document-date"><?php echo date('M d, Y', strtotime($doc['date_added'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                            <?php if (count($client_documents['client_owned']) > 5): ?>
                                <li class="document-item">
                                    <a href="<?php echo site_url('cases/Cl_cases/my_documents?type=client'); ?>" class="document-link">
                                        <strong>View all <?php echo count($client_documents['client_owned']); ?> documents →</strong>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Contact Documents (if client has contacts) -->
                <?php if (!empty($client_documents['contact_documents'])): ?>
                    <?php foreach ($client_documents['contact_documents'] as $contact_name => $docs): ?>
                        <div class="document-group">
                            <h4><i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($contact_name); ?> (<?php echo count($docs); ?>)</h4>
                            <ul class="document-list">
                                <?php foreach (array_slice($docs, 0, 3) as $doc): ?>
                                    <li class="document-item">
                                        <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="document-link">
                                            <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i> 
                                            <?php echo htmlspecialchars($doc['name']); ?>
                                        </a>
                                        <small class="document-date"><?php echo date('M d, Y', strtotime($doc['date_added'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (count($docs) > 3): ?>
                                    <li class="document-item">
                                        <a href="<?php echo site_url('cases/Cl_cases/my_documents?type=contact&contact=' . $docs[0]['contact_id']); ?>" class="document-link">
                                            <strong>View all <?php echo count($docs); ?> documents →</strong>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Recent Uploads -->
                <?php if (!empty($client_documents['recent_uploads'])): ?>
                    <div class="document-group">
                        <h4><i class="fa fa-clock-o"></i> Recently Added (<?php echo count($client_documents['recent_uploads']); ?>)</h4>
                        <ul class="document-list">
                            <?php foreach (array_slice($client_documents['recent_uploads'], 0, 5) as $doc): ?>
                                <li class="document-item">
                                    <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="document-link">
                                        <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i> 
                                        <?php echo htmlspecialchars($doc['name']); ?>
                                    </a>
                                    <small class="document-date">
                                        <?php echo date('M d, Y', strtotime($doc['date_added'])); ?>
                                        <?php if (!empty($doc['owner_type'])): ?>
                                            <span class="document-owner-type">(<?php echo ucfirst($doc['owner_type']); ?>)</span>
                                        <?php endif; ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- View All Documents Link -->
            <div style="text-align: center; margin-top: 20px;">
                <a href="<?php echo site_url('cases/Cl_cases/my_documents'); ?>" class="action-card" style="display: inline-block; padding: 15px 30px; text-decoration: none;">
                    <h4 style="margin: 0;"><i class="fa fa-files-o"></i> View All My Documents</h4>
                    <p style="margin: 5px 0 0 0;">Access, download, and manage all your documents</p>
                </a>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-files-o fa-3x"></i>
                <p>No documents available yet.</p>
            </div>
        <?php endif; ?>
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