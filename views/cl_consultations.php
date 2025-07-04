<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.consultations-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.consultations-header {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 4px solid #3498db;
}

.consultations-header h1 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.consultations-header p {
    margin: 0;
    color: #7f8c8d;
    font-size: 16px;
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
    height: 40px;
}

.filter-input:focus {
    outline: none;
    border-color: #3498db;
}

.filter-btn {
    padding: 10px 12px;
    background: #95a5a6;
    color: white;
    border: none;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
    height: 40px;
}

.filter-btn:hover {
    background: #7f8c8d;
}

.schedule-btn {
    padding: 10px 16px;
    background: #27ae60;
    color: white;
    border: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-block;
    height: 40px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.schedule-btn:hover {
    background: #229954;
    text-decoration: none;
    color: white;
}

.consultations-grid {
    display: grid;
    gap: 20px;
}

.consultation-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    border-left: 4px solid #3498db;
}

.consultation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.consultation-card.upcoming {
    border-left-color: #f39c12;
}

.consultation-card.completed {
    border-left-color: #27ae60;
}

.consultation-card.cancelled {
    border-left-color: #e74c3c;
}

.consultation-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.consultation-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.consultation-type {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}

.consultation-status {
    display: inline-block;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
}

.status-scheduled {
    background: #f39c12;
}

.status-completed {
    background: #27ae60;
}

.status-cancelled {
    background: #e74c3c;
}

.status-pending {
    background: #95a5a6;
}

.consultation-meta {
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

.consultation-description {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.consultation-description h6 {
    margin: 0 0 8px 0;
    color: #2c3e50;
    font-size: 14px;
    font-weight: 600;
}

.consultation-description p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
    line-height: 1.5;
}

.consultation-actions {
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
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.action-btn.primary:hover {
    background: #2980b9;
    border-color: #2980b9;
    color: white;
}

.action-btn.success {
    background: #27ae60;
    color: white;
    border-color: #27ae60;
}

.action-btn.success:hover {
    background: #229954;
    border-color: #229954;
    color: white;
}

.urgent-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e74c3c;
    color: white;
    font-size: 10px;
    padding: 3px 8px;
    font-weight: 600;
    text-transform: uppercase;
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
    margin: 0 0 20px 0;
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

.consultations-count {
    background: #f8f9fa;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-left: 3px solid #3498db;
}

.consultations-count-text {
    color: #2c3e50;
    font-size: 14px;
    margin: 0;
}

.upcoming-today {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-left: 4px solid #f39c12;
}

.upcoming-today h4 {
    margin: 0 0 5px 0;
    color: #856404;
    font-size: 16px;
}

.upcoming-today p {
    margin: 0;
    color: #856404;
    font-size: 14px;
}

@media (max-width: 768px) {
    .consultations-container {
        padding: 15px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .consultation-header-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .consultation-meta {
        grid-template-columns: 1fr;
    }
    
    .consultation-actions {
        flex-direction: column;
    }
}
</style>

<div class="consultations-container">
    <!-- Back Navigation -->
    <a href="<?php echo site_url('cases/Cl_cases'); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Page Header -->
    <div class="consultations-header">
        <h1><i class="fa fa-comments"></i> My Consultations</h1>
        <p>View your scheduled and completed consultations</p>
    </div>

    <!-- Upcoming Today Alert -->
    <?php 
    $today_consultations = [];
    if (isset($consultations)) {
        foreach ($consultations as $consultation) {
            if (isset($consultation['consultation_date']) && date('Y-m-d', strtotime($consultation['consultation_date'])) == date('Y-m-d') && $consultation['status'] == 'Scheduled') {
                $today_consultations[] = $consultation;
            }
        }
    }
    if (!empty($today_consultations)): 
    ?>
        <div class="upcoming-today">
            <h4><i class="fa fa-exclamation-triangle"></i> Consultations Today</h4>
            <p>You have <?php echo count($today_consultations); ?> consultation<?php echo count($today_consultations) != 1 ? 's' : ''; ?> scheduled for today. Please review your schedule.</p>
        </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Search Consultations</label>
                <input type="text" class="filter-input" id="searchInput" placeholder="Search by subject or type...">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select class="filter-input" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Date Range</label>
                <select class="filter-input" id="dateFilter">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="this-week">This Week</option>
                    <option value="this-month">This Month</option>
                    <option value="past">Past Consultations</option>
                </select>
            </div>
            <div class="filter-group">
                <button class="filter-btn" onclick="clearFilters()">
                    <i class="fa fa-refresh"></i> Clear Filters
                </button>
            </div>
            <div class="filter-group">
                <a href="<?php echo site_url('cases/Cl_cases/schedule_consultation'); ?>" class="schedule-btn">
                    <i class="fa fa-plus"></i> New Consultation
                </a>
            </div>
        </div>
    </div>

    <!-- Consultations Count -->
    <?php if (isset($consultations) && !empty($consultations)): ?>
        <div class="consultations-count">
            <p class="consultations-count-text">
                <strong><?php echo count($consultations); ?></strong> consultation<?php echo count($consultations) != 1 ? 's' : ''; ?> found
            </p>
        </div>
    <?php endif; ?>

    <!-- Consultations Grid -->
    <div class="consultations-grid" id="consultationsGrid">
        <?php if (isset($consultations) && !empty($consultations)): ?>
            <?php foreach ($consultations as $consultation): ?>
                <div class="consultation-card <?php echo strtolower($consultation['status'] ?? 'pending'); ?>" 
                     data-status="<?php echo strtolower($consultation['status'] ?? 'pending'); ?>"
                     data-date="<?php echo $consultation['consultation_date'] ?? ''; ?>"
                     onclick="viewConsultation(<?php echo $consultation['id']; ?>)">
                    
                    <?php if (isset($consultation['urgent']) && $consultation['urgent']): ?>
                        <div class="urgent-indicator">Urgent</div>
                    <?php endif; ?>
                    
                    <div class="consultation-header-row">
                        <div>
                            <h3 class="consultation-title"><?php echo htmlspecialchars($consultation['subject'] ?? 'Consultation'); ?></h3>
                            <p class="consultation-type"><?php echo htmlspecialchars($consultation['consultation_type'] ?? 'General Consultation'); ?></p>
                        </div>
                        <div class="consultation-status status-<?php echo strtolower(str_replace(' ', '-', $consultation['status'] ?? 'pending')); ?>">
                            <?php echo htmlspecialchars($consultation['status'] ?? 'Pending'); ?>
                        </div>
                    </div>

                    <div class="consultation-meta">
                        <div class="meta-item">
                            <div class="meta-label">Date</div>
                            <div class="meta-value"><?php echo isset($consultation['consultation_date']) ? date('M d, Y', strtotime($consultation['consultation_date'])) : 'N/A'; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Time</div>
                            <div class="meta-value"><?php echo isset($consultation['consultation_date']) ? date('H:i A', strtotime($consultation['consultation_date'])) : 'N/A'; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Duration</div>
                            <div class="meta-value"><?php echo isset($consultation['duration']) ? $consultation['duration'] . ' mins' : 'N/A'; ?></div>
                        </div>
                        <?php if (isset($consultation['case_title'])): ?>
                            <div class="meta-item">
                                <div class="meta-label">Related Case</div>
                                <div class="meta-value"><?php echo htmlspecialchars($consultation['case_title']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($consultation['summary']) && !empty($consultation['summary'])): ?>
                        <div class="consultation-description">
                            <h6>Summary</h6>
                            <p><?php echo htmlspecialchars(substr($consultation['summary'], 0, 120)); ?><?php echo strlen($consultation['summary']) > 120 ? '...' : ''; ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="consultation-actions" onclick="event.stopPropagation();">
                        <a href="<?php echo site_url('cases/Cl_cases/consultation_details/' . $consultation['id']); ?>" class="action-btn primary">
                            <i class="fa fa-eye"></i> View Details
                        </a>
                        
                        <?php if ($consultation['status'] == 'Scheduled' && isset($consultation['meeting_link'])): ?>
                            <a href="<?php echo $consultation['meeting_link']; ?>" target="_blank" class="action-btn success">
                                <i class="fa fa-video-camera"></i> Join Meeting
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($consultation['status'] == 'Scheduled'): ?>
                            <a href="<?php echo site_url('cases/Cl_cases/reschedule_consultation/' . $consultation['id']); ?>" class="action-btn">
                                <i class="fa fa-calendar"></i> Reschedule
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo site_url('cases/Cl_cases/consultation_documents/' . $consultation['id']); ?>" class="action-btn">
                            <i class="fa fa-files-o"></i> Documents
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-comments fa-4x"></i>
                <h3>No Consultations Found</h3>
                <p>You don't have any consultations yet, or no consultations match your current filters.</p>
                <a href="<?php echo site_url('cases/Cl_cases/schedule_consultation'); ?>" class="schedule-btn">
                    <i class="fa fa-plus"></i> New Consultation
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function viewConsultation(consultationId) {
    window.location.href = '<?php echo site_url("cases/Cl_cases/consultation_details"); ?>/' + consultationId;
}

function filterConsultations() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const dateFilter = document.getElementById('dateFilter').value;
    
    const consultationCards = document.querySelectorAll('.consultation-card');
    let visibleCount = 0;
    
    consultationCards.forEach(card => {
        const title = card.querySelector('.consultation-title').textContent.toLowerCase();
        const type = card.querySelector('.consultation-type').textContent.toLowerCase();
        const status = card.dataset.status;
        const consultationDate = new Date(card.dataset.date);
        const today = new Date();
        
        const matchesSearch = !searchTerm || title.includes(searchTerm) || type.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        
        let matchesDate = true;
        if (dateFilter) {
            switch (dateFilter) {
                case 'today':
                    matchesDate = consultationDate.toDateString() === today.toDateString();
                    break;
                case 'this-week':
                    const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
                    const weekEnd = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    matchesDate = consultationDate >= weekStart && consultationDate <= weekEnd;
                    break;
                case 'this-month':
                    matchesDate = consultationDate.getMonth() === today.getMonth() && consultationDate.getFullYear() === today.getFullYear();
                    break;
                case 'past':
                    matchesDate = consultationDate < today;
                    break;
            }
        }
        
        if (matchesSearch && matchesStatus && matchesDate) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update count
    const countElement = document.querySelector('.consultations-count-text');
    if (countElement) {
        countElement.innerHTML = '<strong>' + visibleCount + '</strong> consultation' + (visibleCount !== 1 ? 's' : '') + ' found';
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = '';
    filterConsultations();
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', filterConsultations);
    document.getElementById('statusFilter').addEventListener('change', filterConsultations);
    document.getElementById('dateFilter').addEventListener('change', filterConsultations);
});
</script>