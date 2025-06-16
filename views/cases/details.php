<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Minimalist Black & White UI with Status Colors */
* {
    box-sizing: border-box;
}

body {
    background: #fafafa;
    color: #2c2c2c;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.case-header {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 40px;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.case-header h1 {
    margin: 0 0 8px 0;
    font-weight: 600;
    font-size: 2.2rem;
    color: #1a1a1a;
    letter-spacing: -0.02em;
}

.case-header .case-number {
    font-size: 1rem;
    color: #666666;
    font-weight: 400;
    margin-bottom: 25px;
}

.case-actions .btn {
    margin-right: 12px;
    margin-bottom: 8px;
    border-radius: 1px;
    padding: 10px 20px;
    font-weight: 500;
    font-size: 0.875rem;
    border: 1px solid #d1d1d1;
    background: #ffffff;
    color: #2c2c2c;
    transition: all 0.15s ease;
    text-decoration: none;
}

.case-actions .btn:hover {
    background: #f8f8f8;
    border-color: #999999;
    color: #1a1a1a;
}

.case-actions .btn-primary {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.case-actions .btn-primary:hover {
    background: #000000;
    border-color: #000000;
}

.info-card {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 30px;
    margin-bottom: 25px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.info-card-header {
    border-bottom: 1px solid #e1e1e1;
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.info-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: -0.01em;
}

.info-table {
    border: none;
    margin: 0;
}

.info-table tr {
    border: none;
}

.info-table th {
    border: none;
    padding: 14px 0;
    font-weight: 500;
    color: #666666;
    font-size: 0.875rem;
    width: 35%;
    vertical-align: top;
}

.info-table td {
    border: none;
    padding: 14px 0;
    color: #1a1a1a;
    font-size: 0.875rem;
    font-weight: 400;
    vertical-align: top;
}

/* Status Colors */
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 1px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid;
}

/* Status Color Codes */
.status-active { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-scheduled { 
    background: #eff8ff; 
    color: #1a6bcc; 
    border-color: #1a6bcc; 
}

.status-completed { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-adjourned { 
    background: #fff8e6; 
    color: #cc8c1a; 
    border-color: #cc8c1a; 
}

.status-cancelled { 
    background: #fff0f0; 
    color: #cc1a1a; 
    border-color: #cc1a1a; 
}

.status-on-hold { 
    background: #f5f5f5; 
    color: #666666; 
    border-color: #666666; 
}

.status-dismissed { 
    background: #fff0f0; 
    color: #cc1a1a; 
    border-color: #cc1a1a; 
}

.section-header {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 25px 30px;
    margin: 30px 0 20px 0;
    border-radius: 2px;
    border-left: 3px solid #1a1a1a;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: -0.01em;
}

.modern-table {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 20px;
}

.modern-table thead th {
    background: #f8f8f8;
    color: #1a1a1a;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 20px;
    border-bottom: 1px solid #e1e1e1;
    border-right: 1px solid #f0f0f0;
}

.modern-table thead th:last-child {
    border-right: none;
}

.modern-table tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid #f5f5f5;
    border-right: 1px solid #f8f8f8;
    vertical-align: middle;
    color: #2c2c2c;
    font-size: 0.875rem;
}

.modern-table tbody td:last-child {
    border-right: none;
}

.modern-table tbody tr:hover {
    background: #fafafa;
}

.modern-table tbody tr:last-child td {
    border-bottom: none;
}

.action-btn {
    border-radius: 1px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-right: 6px;
    margin-bottom: 4px;
    transition: all 0.15s ease;
    text-decoration: none;
    border: 1px solid;
}

.action-btn.btn-primary {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.action-btn.btn-primary:hover {
    background: #000000;
    border-color: #000000;
}

.action-btn.btn-success {
    background: #ffffff;
    border-color: #2d7d2d;
    color: #2d7d2d;
}

.action-btn.btn-success:hover {
    background: #2d7d2d;
    color: #ffffff;
}

.action-btn.btn-info {
    background: #ffffff;
    border-color: #1a6bcc;
    color: #1a6bcc;
}

.action-btn.btn-info:hover {
    background: #1a6bcc;
    color: #ffffff;
}

.action-btn.btn-danger {
    background: #ffffff;
    border-color: #cc1a1a;
    color: #cc1a1a;
}

.action-btn.btn-danger:hover {
    background: #cc1a1a;
    color: #ffffff;
}

.consultation-note {
    background: #fafafa;
    padding: 20px;
    border: 1px solid #e1e1e1;
    border-left: 3px solid #666666;
    line-height: 1.6;
    font-size: 0.875rem;
    color: #2c2c2c;
    border-radius: 2px;
}

.empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #999999;
    background: #fafafa;
    border: 1px dashed #d1d1d1;
    border-radius: 2px;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 20px;
    opacity: 0.6;
    color: #cccccc;
}

.empty-state h5 {
    font-weight: 600;
    color: #666666;
    margin-bottom: 8px;
}

.empty-state p {
    color: #999999;
    margin-bottom: 20px;
}

.file-icon {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    opacity: 0.7;
}

.date-time-cell strong {
    color: #1a1a1a;
    font-weight: 600;
}

.date-time-cell small {
    color: #666666;
    font-weight: normal;
}

.text-muted {
    color: #999999 !important;
}

.text-primary {
    color: #1a1a1a !important;
}

.text-success {
    color: #2d7d2d !important;
}

.text-warning {
    color: #cc8c1a !important;
}

.text-danger {
    color: #cc1a1a !important;
}

.text-info {
    color: #1a6bcc !important;
}

/* Remarks and Important Text Colors */
.remark-critical {
    color: #cc1a1a;
    font-weight: 600;
}

.remark-warning {
    color: #cc8c1a;
    font-weight: 500;
}

.remark-info {
    color: #1a6bcc;
    font-weight: 500;
}

.remark-success {
    color: #2d7d2d;
    font-weight: 500;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .case-header {
        padding: 25px 20px;
        text-align: left;
    }
    
    .case-header h1 {
        font-size: 1.8rem;
    }
    
    .case-actions .btn {
        width: 100%;
        margin-bottom: 8px;
        margin-right: 0;
    }
    
    .info-card {
        padding: 20px;
    }
    
    .section-header {
        padding: 20px;
    }
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 12px 15px;
        font-size: 0.8rem;
    }
}

/* Print Styles */
@media print {
    .case-actions {
        display: none;
    }
    
    .info-card,
    .modern-table,
    .section-header {
        border: 1px solid #000000;
        box-shadow: none;
    }
    
    .status-badge {
        border: 1px solid #000000;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Minimalist Case Header -->
        <div class="case-header">
            <div class="row">
                <div class="col-md-8">
                    <h1><?php echo htmlspecialchars($case['case_title']); ?></h1>
                    <div class="case-number">Case No: <?php echo htmlspecialchars($case['case_number']); ?></div>
                </div>
                <div class="col-md-4">
                    <div class="case-actions text-right">
                        <a href="<?php echo admin_url('cases'); ?>" class="btn">
                            ← Back to Cases
                        </a>
                        <a href="<?php echo admin_url('cases/hearings/add?case_id=' . $case['id']); ?>" class="btn btn-primary">
                            Add Hearing
                        </a>
                        <a href="<?php echo admin_url('documents/upload'); ?>" class="btn"
                           onclick="localStorage.setItem('document_upload_data', JSON.stringify({
                             case_id: <?php echo $case['id']; ?>,
                             customer_id: <?php echo $case['client_id']; ?>,
                             doc_type: 'case'
                           }));">
                            Upload Document
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Case Information Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <h3 class="info-card-title">Case Information</h3>
                    </div>
                    <table class="info-table table">
                        <tbody>
                            <tr>
                                <th>Client</th>
                                <td><?php echo htmlspecialchars($case['client_name'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Person</th>
                                <td><?php echo htmlspecialchars($case['contact_name'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Court</th>
                                <td><?php echo htmlspecialchars($case['court_display'] ?: 'Not specified'); ?></td>
                            </tr>
                            <tr>
                                <th>Date Filed</th>
                                <td><?php echo !empty($case['date_filed']) ? date('d M Y', strtotime($case['date_filed'])) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Date Created</th>
                                <td><?php echo date('d M Y', strtotime($case['date_created'])); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="status-badge status-active">Active</span>
                                </td>
                            </tr>
                            <?php if (!empty($case['consultation_tag'])): ?>
                            <tr>
                                <th>Category</th>
                                <td>
                                    <?php 
                                    $tags = explode(',', $case['consultation_tag']);
                                    foreach ($tags as $tag): 
                                        $tag = trim($tag);
                                        if (!empty($tag)):
                                    ?>
                                        <span class="status-badge status-info" style="margin-right: 6px; margin-bottom: 4px; display: inline-block;"><?php echo htmlspecialchars($tag); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <h3 class="info-card-title">Consultation Note</h3>
                    </div>
                    <?php if (!empty($case['consultation_note'])): ?>
                        <div class="consultation-note">
                            <?php echo $case['consultation_note']; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <p>No consultation note available.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="section-header">
            <h3 class="section-title">Case Documents</h3>
        </div>

        <?php if (!empty($case_documents)): ?>
            <table class="modern-table table">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($case_documents as $doc): ?>
                        <tr>
                            <td>
                                <i class="fas fa-file text-muted file-icon"></i>
                                <?php echo htmlspecialchars($doc['file_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($doc['filetype']); ?></td>
                            <td>
                                <?php if (!empty($doc['tag'])): ?>
                                    <?php 
                                    $doc_tags = explode(',', $doc['tag']);
                                    foreach ($doc_tags as $doc_tag): 
                                        $doc_tag = trim($doc_tag);
                                        if (!empty($doc_tag)):
                                    ?>
                                        <span class="status-badge status-info" style="margin-right: 4px; margin-bottom: 2px; display: inline-block;"><?php echo htmlspecialchars($doc_tag); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                <?php else: ?>
                                    <span class="text-muted">Uncategorized</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($doc['dateadded'])); ?></td>
                            <td>
                                <a href="<?php echo admin_url('documents/download/' . $doc['id']); ?>" 
                                   class="action-btn btn-success" title="Download">Download</a>
                                <a href="<?php echo admin_url('documents/view/' . $doc['id']); ?>" 
                                   class="action-btn btn-info" target="_blank" title="View">View</a>
                                <?php if (has_permission('documents', '', 'delete')): ?>
                                <a href="<?php echo admin_url('documents/delete/' . $doc['id']); ?>" 
                                   class="action-btn btn-danger _delete" title="Delete">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="info-card">
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h5>No Case Documents Found</h5>
                    <p>Upload your first case document to get started.</p>
                    <a href="<?php echo admin_url('documents/upload'); ?>" 
                       class="action-btn btn-primary"
                       onclick="localStorage.setItem('document_upload_data', JSON.stringify({
                         case_id: <?php echo $case['id']; ?>,
                         customer_id: <?php echo $case['client_id']; ?>,
                         doc_type: 'case'
                       }));">
                        Upload Case Document
                    </a>
                </div>
            </div>
        <?php endif; ?>

<!-- Hearing Documents Selector + Table -->
<div class="section-header">
    <h3 class="section-title">Hearing Documents</h3>
</div>

<select id="hearingSelector" class="form-select mb-4">
    <option value="">— Select Hearing Date —</option>
    <?php foreach ($hearings as $h): 
        if (empty($hearing_documents_by_hearing[$h['id']])) continue;
    ?>
        <option value="<?php echo $h['id']; ?>">
            <?php echo date('d M Y', strtotime($h['date'])); ?>
            <?php if ($h['hearing_purpose']): ?>
                (<?php echo htmlspecialchars($h['hearing_purpose']); ?>)
            <?php endif; ?>
        </option>
    <?php endforeach; ?>
</select>

<table id="hearingDocsTable" class="modern-table table">
    <thead>
        <tr>
            <th>Document Name</th>
            <th>Type</th>
            <th>Category</th>
            <th>Date Added</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" class="text-center text-muted">
                Select a hearing above to view its documents.
            </td>
        </tr>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1) preload docs-by-hearing
    const docsByHearing = <?php echo json_encode($hearing_documents_by_hearing, JSON_UNESCAPED_SLASHES); ?>;
    // 2) expose delete‐permission from PHP
    const canDelete = <?php echo has_permission('documents', '', 'delete') ? 'true' : 'false'; ?>;

    const selector = document.getElementById('hearingSelector');
    const tbody    = document.querySelector('#hearingDocsTable tbody');

    selector.addEventListener('change', function() {
        const hearingId = this.value;
        tbody.innerHTML = '';

        const docs = docsByHearing[hearingId] || [];
        if (!docs.length) {
            tbody.innerHTML =
              '<tr><td colspan="5" class="text-center text-muted">' +
              'No documents for this hearing.' +
              '</td></tr>';
            return;
        }

        docs.forEach(function(doc) {
            // build tags
            let tagsHtml = '';
            if (doc.tag) {
                doc.tag.split(',').forEach(function(t) {
                    t = t.trim();
                    if (t) {
                        tagsHtml +=
                          '<span class="status-badge status-info" ' +
                          'style="margin-right:4px;display:inline-block;">' +
                          t +
                          '</span>';
                    }
                });
            } else {
                tagsHtml = '<span class="text-muted">Uncategorized</span>';
            }

            // build actions
            let actions  = 
              '<a href="<?php echo admin_url('documents/download/'); ?>'+doc.id+'" '+
              'class="action-btn btn-success">Download</a>'+
              '<a href="<?php echo admin_url('documents/view/'); ?>'+doc.id+'" '+
              'class="action-btn btn-info" target="_blank">View</a>';
            if (canDelete) {
                actions += 
                  '<a href="<?php echo admin_url('documents/delete/'); ?>'+doc.id+'" '+
                  'class="action-btn btn-danger _delete">Delete</a>';
            }

            // append row
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><i class="fas fa-file file-icon"></i>${doc.file_name}</td>
                <td>${doc.filetype}</td>
                <td>${tagsHtml}</td>
                <td>${new Date(doc.dateadded).toLocaleDateString()}</td>
                <td>${actions}</td>
            `;
            tbody.appendChild(tr);
        });
    });
});
</script>



        <!-- Hearings Section -->
        <div class="section-header">
            <h3 class="section-title">Hearings Schedule</h3>
        </div>

        <!-- Upcoming Hearings -->
        <div class="info-card">
            <div class="info-card-header">
                <h4 class="info-card-title">Upcoming Hearings</h4>
            </div>
            <?php if (!empty($upcoming_hearings)): ?>
                <table class="modern-table table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_hearings as $hearing): ?>
                            <tr>
                                <td class="date-time-cell">
                                    <strong><?php echo date('d M Y', strtotime($hearing['date'])); ?></strong><br>
                                    <small><?php echo date('h:i A', strtotime($hearing['time'])); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($hearing['hearing_purpose'] ?: $hearing['description']); ?></td>
                                <td>
                                    <?php 
                                    $status_class = 'status-scheduled';
                                    switch(strtolower($hearing['status'])) {
                                        case 'completed': $status_class = 'status-completed'; break;
                                        case 'adjourned': $status_class = 'status-adjourned'; break;
                                        case 'cancelled': $status_class = 'status-cancelled'; break;
                                        default: $status_class = 'status-scheduled';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $hearing['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('cases/hearings/edit/' . $hearing['id']); ?>" 
                                       class="action-btn btn-primary">Edit</a>
                                    <a href="<?php echo admin_url('cases/hearings/quick_update/' . $hearing['id']); ?>" 
                                       class="action-btn btn-success">Update</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5>No Upcoming Hearings</h5>
                    <p>Schedule your next hearing for this case.</p>
                    <a href="<?php echo admin_url('cases/hearings/add?case_id=' . $case['id']); ?>" 
                       class="action-btn btn-primary">Schedule Hearing</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Past Hearings -->
        <div class="info-card">
            <div class="info-card-header">
                <h4 class="info-card-title">Past Hearings</h4>
            </div>
            <?php if (!empty($past_hearings)): ?>
                <table class="modern-table table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Outcome</th>
                            <th>Status</th>
                            <th>Next Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($past_hearings as $hearing): ?>
                            <tr>
                                <td class="date-time-cell">
                                    <strong><?php echo date('d M Y', strtotime($hearing['date'])); ?></strong><br>
                                    <small><?php echo date('h:i A', strtotime($hearing['time'])); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $description = htmlspecialchars($hearing['description']);
                                    // Color code important remarks
                                    if (stripos($description, 'adjourned') !== false || stripos($description, 'postponed') !== false) {
                                        echo '<span class="remark-warning">' . $description . '</span>';
                                    } elseif (stripos($description, 'dismissed') !== false || stripos($description, 'rejected') !== false) {
                                        echo '<span class="remark-critical">' . $description . '</span>';
                                    } elseif (stripos($description, 'granted') !== false || stripos($description, 'approved') !== false) {
                                        echo '<span class="remark-success">' . $description . '</span>';
                                    } else {
                                        echo $description;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-completed">
                                        <?php echo $hearing['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($hearing['next_date'])): ?>
                                        <span class="remark-info"><?php echo date('d M Y', strtotime($hearing['next_date'])); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">No next date</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-gavel"></i>
                    <p>No past hearings recorded.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Clean JavaScript without jQuery dependencies
document.addEventListener('DOMContentLoaded', function() {
    // Simple tooltip functionality
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(function(element) {
        element.addEventListener('mouseenter', function() {
            // Simple tooltip could be added here if needed
        });
    });
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('._delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php init_tail(); ?>