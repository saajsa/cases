<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.cases-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.cases-header {
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 4px solid #3498db;
}

.cases-header h1 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.cases-header p {
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
}

.filter-input:focus {
    outline: none;
    border-color: #3498db;
}

.filter-btn {
    padding: 10px 20px;
    background: #3498db;
    color: white;
    border: none;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
}

.filter-btn:hover {
    background: #2980b9;
}

.cases-grid {
    display: grid;
    gap: 20px;
}

.case-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    border-left: 4px solid #3498db;
}

.case-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.case-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.case-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.case-number {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}

.case-status {
    display: inline-block;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
}

.status-active {
    background: #27ae60;
}

.status-pending {
    background: #f39c12;
}

.status-closed {
    background: #95a5a6;
}

.status-on-hold {
    background: #e74c3c;
}

.case-meta {
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

.case-progress {
    margin-bottom: 20px;
}

.progress-label {
    font-size: 12px;
    color: #7f8c8d;
    margin-bottom: 5px;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #ecf0f1;
    position: relative;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #3498db;
    transition: width 0.3s ease;
}

.case-actions {
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
    color: #3498db;
    text-decoration: none;
    margin-bottom: 20px;
    font-size: 14px;
}

.back-link:hover {
    text-decoration: underline;
}

.cases-count {
    background: #f8f9fa;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-left: 3px solid #3498db;
}

.cases-count-text {
    color: #2c3e50;
    font-size: 14px;
    margin: 0;
}

@media (max-width: 768px) {
    .cases-container {
        padding: 15px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .case-header-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .case-meta {
        grid-template-columns: 1fr;
    }
    
    .case-actions {
        flex-direction: column;
    }
}
</style>

<div class="cases-container">
    <!-- Back Navigation -->
    <a href="<?php echo site_url('cases/Cl_cases'); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Page Header -->
    <div class="cases-header">
        <h1><i class="fa fa-balance-scale"></i> My Cases</h1>
        <p>View and manage all your legal cases</p>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Search Cases</label>
                <input type="text" class="filter-input" id="searchInput" placeholder="Search by title or case number...">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select class="filter-input" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="closed">Closed</option>
                    <option value="on-hold">On Hold</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Case Type</label>
                <select class="filter-input" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="civil">Civil</option>
                    <option value="criminal">Criminal</option>
                    <option value="family">Family</option>
                    <option value="corporate">Corporate</option>
                </select>
            </div>
            <div class="filter-group">
                <button class="filter-btn" onclick="clearFilters()">
                    <i class="fa fa-refresh"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Cases Count -->
    <?php if (isset($cases) && !empty($cases)): ?>
        <div class="cases-count">
            <p class="cases-count-text">
                <strong><?php echo count($cases); ?></strong> case<?php echo count($cases) != 1 ? 's' : ''; ?> found
            </p>
        </div>
    <?php endif; ?>

    <!-- Cases Grid -->
    <div class="cases-grid" id="casesGrid">
        <?php if (isset($cases) && !empty($cases)): ?>
            <?php foreach ($cases as $case): ?>
                <div class="case-card" 
                     data-status="<?php echo strtolower($case['status'] ?? 'pending'); ?>"
                     data-type="<?php echo strtolower($case['case_type'] ?? ''); ?>"
                     onclick="viewCase(<?php echo $case['id']; ?>)">
                    
                    <div class="case-header-row">
                        <div>
                            <h3 class="case-title"><?php echo htmlspecialchars($case['case_title'] ?? 'Untitled Case'); ?></h3>
                            <p class="case-number">Case #<?php echo htmlspecialchars($case['case_number'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="case-status status-<?php echo strtolower(str_replace(' ', '-', $case['status'] ?? 'pending')); ?>">
                            <?php echo htmlspecialchars($case['status'] ?? 'Pending'); ?>
                        </div>
                    </div>

                    <div class="case-meta">
                        <div class="meta-item">
                            <div class="meta-label">Court</div>
                            <div class="meta-value"><?php echo htmlspecialchars($case['court_name'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Case Type</div>
                            <div class="meta-value"><?php echo htmlspecialchars($case['case_type'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Filing Date</div>
                            <div class="meta-value"><?php echo isset($case['filing_date']) ? date('M d, Y', strtotime($case['filing_date'])) : 'N/A'; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Next Hearing</div>
                            <div class="meta-value"><?php echo isset($case['next_hearing']) ? date('M d, Y', strtotime($case['next_hearing'])) : 'No hearing scheduled'; ?></div>
                        </div>
                    </div>

                    <?php if (isset($case['progress'])): ?>
                        <div class="case-progress">
                            <div class="progress-label">Case Progress</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $case['progress']; ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="case-actions" onclick="event.stopPropagation();">
                        <a href="<?php echo site_url('cases/Cl_cases/case_details/' . $case['id']); ?>" class="action-btn primary">
                            <i class="fa fa-eye"></i> View Details
                        </a>
                        <a href="<?php echo site_url('cases/Cl_cases/documents?case=' . $case['id']); ?>" class="action-btn">
                            <i class="fa fa-files-o"></i> Documents
                        </a>
                        <a href="<?php echo site_url('cases/Cl_cases/consultations?case=' . $case['id']); ?>" class="action-btn">
                            <i class="fa fa-comments"></i> Consultations
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-balance-scale fa-4x"></i>
                <h3>No Cases Found</h3>
                <p>You don't have any cases yet, or no cases match your current filters.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function viewCase(caseId) {
    window.location.href = '<?php echo site_url("cases/Cl_cases/case_details"); ?>/' + caseId;
}

function filterCases() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
    
    const caseCards = document.querySelectorAll('.case-card');
    let visibleCount = 0;
    
    caseCards.forEach(card => {
        const title = card.querySelector('.case-title').textContent.toLowerCase();
        const caseNumber = card.querySelector('.case-number').textContent.toLowerCase();
        const status = card.dataset.status;
        const type = card.dataset.type;
        
        const matchesSearch = !searchTerm || title.includes(searchTerm) || caseNumber.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesType = !typeFilter || type === typeFilter;
        
        if (matchesSearch && matchesStatus && matchesType) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update count
    const countElement = document.querySelector('.cases-count-text');
    if (countElement) {
        countElement.innerHTML = '<strong>' + visibleCount + '</strong> case' + (visibleCount !== 1 ? 's' : '') + ' found';
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('typeFilter').value = '';
    filterCases();
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', filterCases);
    document.getElementById('statusFilter').addEventListener('change', filterCases);
    document.getElementById('typeFilter').addEventListener('change', filterCases);
});
</script>