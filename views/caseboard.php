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

.page-header {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 40px;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.page-header h1 {
    margin: 0 0 8px 0;
    font-weight: 600;
    font-size: 2.2rem;
    color: #1a1a1a;
    letter-spacing: -0.02em;
}

.page-header .subtitle {
    font-size: 1rem;
    color: #666666;
    font-weight: 400;
    margin-bottom: 25px;
}

.page-actions .btn {
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

.page-actions .btn:hover {
    background: #f8f8f8;
    border-color: #999999;
    color: #1a1a1a;
}

.page-actions .btn-primary {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.page-actions .btn-primary:hover {
    background: #000000;
    border-color: #000000;
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

.section-actions {
    float: right;
    margin-top: -5px;
}

.section-actions a {
    color: #666666;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: color 0.15s ease;
}

.section-actions a:hover {
    color: #1a1a1a;
}

.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.dashboard-card {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 25px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: all 0.15s ease;
}

.dashboard-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-color: #d1d1d1;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.card-date {
    font-size: 0.875rem;
    color: #666666;
    font-weight: 500;
}

.card-content {
    margin-bottom: 20px;
}

.card-subtitle {
    font-size: 0.875rem;
    color: #1a1a1a;
    font-weight: 500;
    margin-bottom: 12px;
    line-height: 1.4;
}

.card-meta {
    font-size: 0.75rem;
    color: #666666;
    margin-bottom: 8px;
}

.card-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 15px;
}

.card-meta-item {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #666666;
}

.card-meta-value {
    color: #1a1a1a;
    font-weight: 500;
}

.card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    border-radius: 1px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.15s ease;
    text-decoration: none;
    border: 1px solid;
    cursor: pointer;
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

.action-btn.btn-default {
    background: #ffffff;
    border-color: #d1d1d1;
    color: #2c2c2c;
}

.action-btn.btn-default:hover {
    background: #f8f8f8;
    border-color: #999999;
}

/* Status Colors */
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 1px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid;
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

.status-consultation { 
    background: #f5f5f5; 
    color: #666666; 
    border-color: #666666; 
}

.status-litigation { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-active { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.badge-count {
    background: #f8f8f8;
    color: #1a1a1a;
    border: 1px solid #e1e1e1;
    padding: 2px 8px;
    border-radius: 1px;
    font-size: 0.7rem;
    font-weight: 600;
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

/* Filter Pills */
.filter-pills {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.filter-pill {
    padding: 8px 16px;
    border: 1px solid #d1d1d1;
    background: #ffffff;
    color: #666666;
    border-radius: 1px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-pill:hover {
    border-color: #999999;
    color: #1a1a1a;
}

.filter-pill.active {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .page-header {
        padding: 25px 20px;
    }
    
    .page-header h1 {
        font-size: 1.8rem;
    }
    
    .page-actions .btn {
        width: 100%;
        margin-bottom: 8px;
        margin-right: 0;
    }
    
    .section-header {
        padding: 20px;
    }
    
    .section-actions {
        float: none;
        margin-top: 10px;
    }
    
    .cards-container {
        grid-template-columns: 1fr;
    }
    
    .card-meta-grid {
        grid-template-columns: 1fr;
    }
    
    .card-actions {
        justify-content: stretch;
    }
    
    .action-btn {
        flex: 1;
        text-align: center;
    }
    
    .filter-pills {
        justify-content: center;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Minimalist Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-8">
                    <h1>Caseboard</h1>
                    <div class="subtitle">Dashboard overview of active cases and upcoming hearings</div>
                </div>
                <div class="col-md-4">
                    <div class="page-actions text-right">
                        <a href="<?php echo admin_url('cases'); ?>" class="btn">
                            View All Cases
                        </a>
                        <a href="<?php echo admin_url('cases/hearings'); ?>" class="btn btn-primary">
                            Manage Hearings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Pills -->
        <div class="filter-pills">
            <button class="filter-pill active" data-filter="all">All</button>
            <button class="filter-pill" data-filter="active-cases">Active Cases</button>
            <button class="filter-pill" data-filter="pending">Pending</button>
            <button class="filter-pill" data-filter="completed">Completed</button>
            <button class="filter-pill" data-filter="recent-consultations">Recent Consultations</button>
        </div>

        <!-- Upcoming Hearings Section -->
        <div class="section-header" id="upcoming-hearings-section">
            <h3 class="section-title">Upcoming Hearings (Next 7 Days)</h3>
            <div class="section-actions">
                <a href="<?php echo admin_url('cases/hearings'); ?>">View All Hearings →</a>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="cards-container">
            <?php if (!empty($upcoming_hearings)): ?>
                <?php foreach ($upcoming_hearings as $hearing): ?>
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title"><?php echo date('d M', strtotime($hearing['date'])); ?></div>
                            <span class="status-badge <?php 
                                echo $hearing['status'] == 'Scheduled' ? 'status-scheduled' : 
                                    ($hearing['status'] == 'Completed' ? 'status-completed' : 'status-adjourned'); 
                            ?>"><?php echo $hearing['status']; ?></span>
                        </div>
                        <div class="card-content">
                            <div class="card-subtitle">
                                <?php 
                                    echo !empty($hearing['case_title']) ? htmlspecialchars($hearing['case_title']) : 'Case #'.$hearing['case_id']; 
                                ?>
                            </div>
                            <div class="card-meta-grid">
                                <div class="card-meta-item">
                                    <span>Time:</span>
                                    <span class="card-meta-value"><?php echo date('h:i A', strtotime($hearing['time'])); ?></span>
                                </div>
                                <div class="card-meta-item">
                                    <span>Purpose:</span>
                                    <span class="card-meta-value">
                                        <?php 
                                            echo !empty($hearing['hearing_purpose']) ? 
                                                (strlen($hearing['hearing_purpose']) > 25 ? 
                                                    substr($hearing['hearing_purpose'], 0, 22) . '...' : 
                                                    htmlspecialchars($hearing['hearing_purpose'])) : 
                                                'N/A'; 
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="<?php echo admin_url('cases/hearings/edit/'.$hearing['id']); ?>" class="action-btn btn-primary">Update</a>
                            <a href="<?php echo admin_url('documents/upload'); ?>"
                               onclick="localStorage.setItem('document_upload_data', JSON.stringify({
                                 hearing_id: <?php echo $hearing['id']; ?>,
                                 case_id: <?php echo $hearing['case_id']; ?>,
                                 customer_id: <?php echo isset($hearing['client_id']) ? $hearing['client_id'] : 0; ?>,
                                 doc_type: 'hearing'
                               }));" class="action-btn btn-info">Add Doc</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dashboard-card">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h5>No Upcoming Hearings</h5>
                        <p>No hearings scheduled for the next 7 days</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active Cases Section -->
        <div class="section-header" id="active-cases-section">
            <h3 class="section-title">Active Cases</h3>
            <div class="section-actions">
                <a href="<?php echo admin_url('cases'); ?>">View All Cases →</a>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="cards-container">
            <?php if (!empty($cases)): ?>
                <?php foreach ($cases as $case): ?>
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <?php echo !empty($case['case_number']) ? htmlspecialchars($case['case_number']) : '#'.$case['id']; ?>
                            </div>
                            <span class="status-badge status-active">Active</span>
                        </div>
                        <div class="card-content">
                            <div class="card-subtitle">
                                <?php 
                                    echo !empty($case['case_title']) ? 
                                        (strlen($case['case_title']) > 35 ? 
                                            substr(htmlspecialchars($case['case_title']), 0, 32) . '...' : 
                                            htmlspecialchars($case['case_title'])) : 
                                        'Untitled Case'; 
                                ?>
                            </div>
                            <div class="card-meta-grid">
                                <div class="card-meta-item">
                                    <span>Client:</span>
                                    <span class="card-meta-value">
                                        <?php 
                                            echo isset($case['client_name']) && $case['client_name'] !== '' ? 
                                                (strlen($case['client_name']) > 20 ? 
                                                    substr(htmlspecialchars($case['client_name']), 0, 17) . '...' : 
                                                    htmlspecialchars($case['client_name'])) : 
                                                'N/A'; 
                                        ?>
                                    </span>
                                </div>
                                <div class="card-meta-item">
                                    <span>Filed:</span>
                                    <span class="card-meta-value">
                                        <?php echo !empty($case['date_filed']) ? date('d M Y', strtotime($case['date_filed'])) : 'N/A'; ?>
                                    </span>
                                </div>
                                <div class="card-meta-item">
                                    <span>Hearings:</span>
                                    <span class="card-meta-value">
                                        <span class="badge-count">
                                            <?php echo isset($case['hearing_count']) ? $case['hearing_count'] : '0'; ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="card-meta-item">
                                    <span>Documents:</span>
                                    <span class="card-meta-value">
                                        <span class="badge-count">
                                            <?php echo isset($case['document_count']) ? $case['document_count'] : '0'; ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="<?php echo admin_url('cases/details?id='.$case['id']); ?>" class="action-btn btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dashboard-card">
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <h5>No Active Cases</h5>
                        <p>No active cases found</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Consultations Section -->
        <div class="section-header" id="recent-consultations-section">
            <h3 class="section-title">Recent Consultations</h3>
            <div class="section-actions">
                <a href="<?php echo admin_url('cases'); ?>">View All Consultations →</a>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="cards-container">
            <?php if (!empty($consultations)): ?>
                <?php foreach ($consultations as $consultation): ?>
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title"><?php echo date('d M', strtotime($consultation['date_added'])); ?></div>
                            <span class="status-badge <?php echo $consultation['phase'] == 'consultation' ? 'status-consultation' : 'status-litigation'; ?>">
                                <?php echo ucfirst($consultation['phase']); ?>
                            </span>
                        </div>
                        <div class="card-content">
                            <div class="card-subtitle">
                                <?php 
                                    echo !empty($consultation['client_name']) ? 
                                        (strlen($consultation['client_name']) > 35 ? 
                                            substr(htmlspecialchars($consultation['client_name']), 0, 32) . '...' : 
                                            htmlspecialchars($consultation['client_name'])) : 
                                        'Unknown Client'; 
                                ?>
                            </div>
                            <div class="card-meta-grid">
                                <div class="card-meta-item">
                                    <span>Contact:</span>
                                    <span class="card-meta-value">
                                        <?php 
                                            echo !empty($consultation['contact_name']) ? 
                                                (strlen($consultation['contact_name']) > 20 ? 
                                                    substr(htmlspecialchars($consultation['contact_name']), 0, 17) . '...' : 
                                                    htmlspecialchars($consultation['contact_name'])) : 
                                                'N/A'; 
                                        ?>
                                    </span>
                                </div>
                                <div class="card-meta-item">
                                    <span>Tag:</span>
                                    <span class="card-meta-value">
                                        <?php 
                                            echo !empty($consultation['tag']) ? 
                                                (strlen($consultation['tag']) > 25 ? 
                                                    substr(htmlspecialchars($consultation['tag']), 0, 22) . '...' : 
                                                    htmlspecialchars($consultation['tag'])) : 
                                                'N/A'; 
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-btn btn-default consultation-view" data-id="<?php echo $consultation['id']; ?>">View Notes</button>
                            <?php if ($consultation['phase'] == 'consultation'): ?>
                                <a href="<?php echo admin_url('cases/upgrade_to_litigation?consultation_id='.$consultation['id']); ?>" class="action-btn btn-success">Upgrade to Case</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dashboard-card">
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h5>No Recent Consultations</h5>
                        <p>No recent consultations found</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Consultation Note Modal -->
<div class="modal fade" id="consultation_note_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: 1px solid #e1e1e1; border-radius: 2px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: #f8f8f8; border-bottom: 1px solid #e1e1e1; padding: 20px 30px;">
                <h5 class="modal-title" style="font-size: 1.2rem; font-weight: 600; color: #1a1a1a; margin: 0;">Consultation Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="border: none; background: none; font-size: 1.5rem; color: #666666;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <!-- Consultation note will be loaded here -->
            </div>
            <div class="modal-footer" style="background: #f8f8f8; border-top: 1px solid #e1e1e1; padding: 20px 30px;">
                <button type="button" class="action-btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Clean, minimal JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterPills = document.querySelectorAll('.filter-pill');
    const sections = document.querySelectorAll('[id$="-section"]');
    
    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide sections
            sections.forEach(section => {
                section.style.display = 'block';
            });
            
            if (filter === 'active-cases') {
                document.getElementById('upcoming-hearings-section').style.display = 'none';
                document.getElementById('recent-consultations-section').style.display = 'none';
            } else if (filter === 'pending') {
                document.getElementById('active-cases-section').style.display = 'none';
                document.getElementById('recent-consultations-section').style.display = 'none';
            } else if (filter === 'completed') {
                document.getElementById('upcoming-hearings-section').style.display = 'none';
                document.getElementById('recent-consultations-section').style.display = 'none';
            } else if (filter === 'recent-consultations') {
                document.getElementById('upcoming-hearings-section').style.display = 'none';
                document.getElementById('active-cases-section').style.display = 'none';
            }
        });
    });
    
    // Consultation view functionality
    const consultationViewButtons = document.querySelectorAll('.consultation-view');
    consultationViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const consultationId = this.dataset.id;
            
            // Show loading in modal
            const modalBody = document.querySelector('#consultation_note_modal .modal-body');
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #999999;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 20px; color: #cccccc;"></i>
                    <p>Loading consultation note...</p>
                </div>
            `;
            
            // Show modal
            $('#consultation_note_modal').modal('show');
            
            // Make AJAX request
            fetch(admin_url + 'cases/get_consultation_note/' + consultationId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalBody.innerHTML = `
                            <div style="background: #fafafa; padding: 20px; border: 1px solid #e1e1e1; border-left: 3px solid #666666; line-height: 1.6; font-size: 0.875rem; color: #2c2c2c; border-radius: 2px;">
                                ${data.note || 'No note content available.'}
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #cc1a1a;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 20px;"></i>
                                <p>Could not retrieve consultation note</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #cc1a1a;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 20px;"></i>
                            <p>An error occurred while retrieving consultation note</p>
                        </div>
                    `;
                });
        });
    });
    
    // Card hover effects
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php init_tail(); ?>