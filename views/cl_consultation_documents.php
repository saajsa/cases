<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.consultation-documents-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.consultation-documents-header {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 4px solid #9b59b6;
}

.consultation-documents-header h1 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.consultation-documents-header p {
    margin: 0;
    color: #7f8c8d;
    font-size: 16px;
}

.consultation-info-bar {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 30px;
    border-left: 3px solid #9b59b6;
}

.consultation-info-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 20px;
    align-items: start;
}

.consultation-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 12px;
    color: #95a5a6;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 3px;
}

.detail-value {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.consultation-type-badge {
    display: inline-block;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
    background: #9b59b6;
    border-radius: 12px;
}

.consultation-type-badge.general {
    background: #95a5a6;
}

.consultation-type-badge.consultation {
    background: #3498db;
}

.consultation-type-badge.litigation {
    background: #e74c3c;
}

.consultation-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.filters-section {
    background: #fff;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 12px;
    color: #95a5a6;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.filter-input {
    padding: 10px 12px;
    border: 1px solid #ddd;
    font-size: 14px;
    color: #2c3e50;
}

.filter-input:focus {
    outline: none;
    border-color: #9b59b6;
}

.filter-btn {
    padding: 10px 20px;
    background: #9b59b6;
    color: white;
    border: none;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
}

.filter-btn:hover {
    background: #8e44ad;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.document-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    border-left: 4px solid #9b59b6;
}

.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.document-card.case-document {
    border-left-color: #f39c12;
}

.document-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.document-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.document-tag {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}

.document-type {
    display: inline-block;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
}

.type-consultation {
    background: #9b59b6;
}

.type-case {
    background: #f39c12;
}

.document-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    flex-direction: column;
}

.meta-label {
    font-size: 11px;
    color: #95a5a6;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 3px;
}

.meta-value {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.document-actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 8px 16px;
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
    background: #9b59b6;
    color: white;
    border-color: #9b59b6;
}

.action-btn.primary:hover {
    background: #8e44ad;
    border-color: #8e44ad;
    color: white;
}

.btn {
    padding: 8px 16px;
    font-size: 12px;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #ddd;
    background: #fff;
    color: #2c3e50;
    display: inline-block;
}

.btn:hover {
    background: #f8f9fa;
    text-decoration: none;
    color: #2c3e50;
}

.btn.primary {
    background: #9b59b6;
    color: white;
    border-color: #9b59b6;
}

.btn.primary:hover {
    background: #8e44ad;
    border-color: #8e44ad;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.empty-state i {
    color: #95a5a6;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-size: 20px;
}

.empty-state p {
    color: #7f8c8d;
    margin: 0;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #9b59b6;
    text-decoration: none;
    margin-bottom: 20px;
    font-size: 14px;
}

.back-link:hover {
    text-decoration: underline;
}

.documents-count {
    background: #f8f9fa;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-left: 3px solid #9b59b6;
}

.documents-count-text {
    color: #2c3e50;
    font-size: 14px;
    margin: 0;
}

.fallback-notice {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-left: 4px solid #f39c12;
}

.fallback-notice h4 {
    margin: 0 0 5px 0;
    color: #856404;
    font-size: 14px;
    font-weight: 600;
}

.fallback-notice p {
    margin: 0;
    color: #856404;
    font-size: 13px;
}

/* Modal styling - same as other views */
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

@media (max-width: 768px) {
    .consultation-documents-container {
        padding: 15px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .documents-grid {
        grid-template-columns: 1fr;
    }
    
    .document-header-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .document-meta {
        grid-template-columns: 1fr;
    }
    
    .document-actions {
        flex-direction: column;
    }
    
    .consultation-info-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .consultation-details {
        grid-template-columns: 1fr;
    }
    
    .consultation-actions {
        flex-direction: row;
        justify-content: space-between;
    }
}
</style>

<div class="consultation-documents-container">
    <!-- Back Navigation -->
    <a href="<?php echo site_url('cases/Cl_cases/consultation_details/' . $consultation['id']); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i> Back to Consultation Details
    </a>

    <!-- Page Header -->
    <div class="consultation-documents-header">
        <h1><i class="fa fa-files-o"></i> Consultation Documents</h1>
        <p>View and manage documents for this consultation</p>
    </div>

    <!-- Consultation Info Bar -->
    <div class="consultation-info-bar">
        <div class="consultation-info-grid">
            <div class="consultation-details">
                <div class="detail-item">
                    <div class="detail-label">Consultation</div>
                    <div class="detail-value"><?php echo htmlspecialchars($consultation['subject']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Type</div>
                    <div class="detail-value">
                        <span class="consultation-type-badge <?php echo strtolower($consultation['consultation_type']); ?>">
                            <?php echo htmlspecialchars($consultation['consultation_type']); ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date</div>
                    <div class="detail-value"><?php echo date('M d, Y', strtotime($consultation['date_added'])); ?></div>
                </div>
                <?php if (isset($consultation['case_title'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Related Case</div>
                        <div class="detail-value">
                            <a href="<?php echo site_url('cases/Cl_cases/case_details/' . $consultation['case_id']); ?>" style="color: #9b59b6; text-decoration: none;">
                                <?php echo htmlspecialchars($consultation['case_title']); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="consultation-actions">
                <a href="<?php echo site_url('cases/Cl_cases/consultation_details/' . $consultation['id']); ?>" class="btn">
                    <i class="fa fa-eye"></i> View Consultation
                </a>
                <a href="<?php echo site_url('cases/Cl_cases/consultations'); ?>" class="btn primary">
                    <i class="fa fa-comments"></i> All Consultations
                </a>
            </div>
        </div>
    </div>

    <!-- Show fallback notice if showing case documents -->
    <?php 
    $show_fallback_notice = false;
    if (!empty($documents) && isset($documents[0]['rel_type']) && $documents[0]['rel_type'] == 'case'): 
        $show_fallback_notice = true;
    endif;
    ?>
    <?php if ($show_fallback_notice): ?>
        <div class="fallback-notice">
            <h4><i class="fa fa-info-circle"></i> Showing Related Case Documents</h4>
            <p>No documents were found specifically for this consultation. Showing documents from the related case instead.</p>
        </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Search Documents</label>
                <input type="text" class="filter-input" id="searchInput" placeholder="Search by name or tag...">
            </div>
            <div class="filter-group">
                <label class="filter-label">File Format</label>
                <select class="filter-input" id="formatFilter">
                    <option value="">All Formats</option>
                    <option value="pdf">PDF</option>
                    <option value="doc">Word Documents</option>
                    <option value="xlsx">Spreadsheets</option>
                    <option value="jpg">Images</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Date Range</label>
                <select class="filter-input" id="dateFilter">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="this-week">This Week</option>
                    <option value="this-month">This Month</option>
                </select>
            </div>
            <div class="filter-group">
                <button class="filter-btn" onclick="clearFilters()">
                    <i class="fa fa-refresh"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Documents Count -->
    <?php if (isset($documents) && !empty($documents)): ?>
        <div class="documents-count">
            <p class="documents-count-text">
                <strong><?php echo count($documents); ?></strong> document<?php echo count($documents) != 1 ? 's' : ''; ?> found
            </p>
        </div>
    <?php endif; ?>

    <!-- Documents Grid -->
    <div class="documents-grid" id="documentsGrid">
        <?php if (isset($documents) && !empty($documents)): ?>
            <?php foreach ($documents as $doc): ?>
                <div class="document-card <?php echo $doc['owner_type'] == 'case' ? 'case-document' : ''; ?>" 
                     data-file-type="<?php echo $doc['file_type']; ?>"
                     data-date="<?php echo $doc['date_added']; ?>"
                     onclick="viewDocument(<?php echo $doc['id']; ?>)">
                    
                    <div class="document-header-row">
                        <div>
                            <h3 class="document-title">
                                <i class="fa fa-file-<?php echo $doc['file_type'] == 'pdf' ? 'pdf-o' : 'text-o'; ?>"></i>
                                <?php echo htmlspecialchars($doc['name']); ?>
                            </h3>
                            <p class="document-tag"><?php echo htmlspecialchars($doc['tag'] ?? ($doc['owner_type'] == 'consultation' ? 'Consultation Document' : 'Case Document')); ?></p>
                        </div>
                        <div class="document-type type-<?php echo $doc['owner_type']; ?>">
                            <?php echo $doc['owner_type'] == 'consultation' ? 'Consultation Document' : 'Case Document'; ?>
                        </div>
                    </div>

                    <div class="document-meta">
                        <div class="meta-item">
                            <div class="meta-label">Upload Date</div>
                            <div class="meta-value"><?php echo date('M d, Y', strtotime($doc['date_added'])); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">File Type</div>
                            <div class="meta-value"><?php echo strtoupper($doc['file_type']); ?></div>
                        </div>
                        <?php if (isset($doc['staffid']) && $doc['staffid']): ?>
                            <div class="meta-item">
                                <div class="meta-label">Uploaded By</div>
                                <div class="meta-value">Staff</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="document-actions" onclick="event.stopPropagation();">
                        <a href="#" onclick="previewDocument(<?php echo $doc['id']; ?>)" class="action-btn primary">
                            <i class="fa fa-eye"></i> Preview
                        </a>
                        <a href="<?php echo site_url('cases/Cl_cases/download_document/' . $doc['id']); ?>" class="action-btn">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-files-o fa-4x"></i>
                <h3>No Documents Found</h3>
                <p>No documents have been uploaded for this consultation<?php echo isset($consultation['case_title']) ? ' or its related case' : ''; ?> yet.</p>
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
function viewDocument(docId) {
    previewDocument(docId);
}

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

function filterDocuments() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const formatFilter = document.getElementById('formatFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const documentCards = document.querySelectorAll('.document-card');
    let visibleCount = 0;
    
    documentCards.forEach(card => {
        const title = card.querySelector('.document-title').textContent.toLowerCase();
        const tag = card.querySelector('.document-tag').textContent.toLowerCase();
        const fileType = card.dataset.fileType;
        const docDate = new Date(card.dataset.date);
        const today = new Date();
        
        const matchesSearch = !searchTerm || title.includes(searchTerm) || tag.includes(searchTerm);
        const matchesFormat = !formatFilter || fileType === formatFilter;
        
        let matchesDate = true;
        if (dateFilter) {
            switch (dateFilter) {
                case 'today':
                    matchesDate = docDate.toDateString() === today.toDateString();
                    break;
                case 'this-week':
                    const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
                    const weekEnd = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    matchesDate = docDate >= weekStart && docDate <= weekEnd;
                    break;
                case 'this-month':
                    matchesDate = docDate.getMonth() === today.getMonth() && docDate.getFullYear() === today.getFullYear();
                    break;
            }
        }
        
        if (matchesSearch && matchesFormat && matchesDate) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update count
    const countElement = document.querySelector('.documents-count-text');
    if (countElement) {
        countElement.innerHTML = '<strong>' + visibleCount + '</strong> document' + (visibleCount !== 1 ? 's' : '') + ' found';
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('formatFilter').value = '';
    document.getElementById('dateFilter').value = '';
    filterDocuments();
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', filterDocuments);
    document.getElementById('formatFilter').addEventListener('change', filterDocuments);
    document.getElementById('dateFilter').addEventListener('change', filterDocuments);
});
</script>