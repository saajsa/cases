<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'status', 'tables', 'modals'], 'caseboard');
echo cases_page_wrapper_start(
    'Legal Practice Dashboard',
    'Comprehensive overview of your legal practice',
    [
        [
            'text' => 'New Consultation',
            'href' => admin_url('cases?new_consultation=1'),
            'class' => 'cases-btn cases-btn-primary',
            'icon' => 'fas fa-plus'
        ],
        [
            'text' => 'Schedule Hearing',
            'href' => admin_url('cases/hearings/add'),
            'class' => 'cases-btn cases-btn-success',
            'icon' => 'fas fa-gavel'
        ],
        [
            'text' => 'View All Cases',
            'href' => admin_url('cases'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-briefcase'
        ]
    ]
);
?>

<!-- Key Metrics Row -->
<div class="cases-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--cases-spacing-md); margin-bottom: var(--cases-spacing-lg);">
    <div class="cases-info-card cases-stat-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">Active Cases</h4>
        </div>
        <div class="cases-stat-number" id="total-cases">-</div>
        <div class="cases-stat-change" id="cases-growth"></div>
    </div>
    <div class="cases-info-card cases-stat-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">Consultations</h4>
        </div>
        <div class="cases-stat-number" id="total-consultations">-</div>
        <div class="cases-stat-change" id="consultations-growth"></div>
    </div>
    <div class="cases-info-card cases-stat-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">Today's Hearings</h4>
        </div>
        <div class="cases-stat-number" id="today-hearings">-</div>
        <div class="cases-stat-label">Scheduled for today</div>
    </div>
    <div class="cases-info-card cases-stat-card">
        <div class="cases-info-card-header">
            <h4 class="cases-info-card-title">Success Rate</h4>
        </div>
        <div class="cases-stat-number" id="success-rate">-</div>
        <div class="cases-stat-label">Hearing completion</div>
    </div>
</div>

<!-- Priority Items Alert Section -->
<div id="priority-alerts-section" class="cases-section cases-mb-lg" style="display: none;">
    <div class="cases-section-with-actions">
        <h3 class="cases-section-title">⚠️ Priority Items Requiring Attention</h3>
        <div class="cases-section-actions">
            <a href="#" id="dismiss-priority">Dismiss</a>
        </div>
    </div>
    <div id="priority-items-container"></div>
</div>

<!-- Main Dashboard Grid: Three columns on desktop, responsive -->
<div class="cases-grid" style="grid-template-columns: 2fr 1fr 1fr; gap: var(--cases-spacing-lg);" id="main-dashboard-grid">
    <!-- Left Column: Document Activity and Recent Documents -->
    <div>
        <!-- Recent Document Activity -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Recent Document Activity</h4>
                <a href="<?php echo admin_url('cases/documents'); ?>" class="cases-link-btn">View All</a>
            </div>
            <div id="recent-document-activity" class="cases-activity-feed">
                <div style="text-align:center;padding:20px;color:var(--cases-text-muted);">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading document activity...</p>
                </div>
            </div>
        </div>
        
        <!-- Document Statistics -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Document Statistics</h4>
            </div>
            <div class="cases-grid" style="grid-template-columns: 1fr 1fr; gap: var(--cases-spacing-sm);">
                <div class="cases-stat-mini">
                    <div class="cases-stat-mini-number" id="total-documents">-</div>
                    <div class="cases-stat-mini-label">Total Documents</div>
                </div>
                <div class="cases-stat-mini">
                    <div class="cases-stat-mini-number" id="documents-this-week">-</div>
                    <div class="cases-stat-mini-label">This Week</div>
                </div>
                <div class="cases-stat-mini">
                    <div class="cases-stat-mini-number" id="case-documents">-</div>
                    <div class="cases-stat-mini-label">Case Documents</div>
                </div>
                <div class="cases-stat-mini">
                    <div class="cases-stat-mini-number" id="hearing-documents">-</div>
                    <div class="cases-stat-mini-label">Hearing Documents</div>
                </div>
            </div>
        </div>
        
        <!-- Quick Document Actions -->
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Document Actions</h4>
            </div>
            <div class="cases-grid cases-grid-responsive cases-p-2">
                <button class="cases-btn cases-btn-primary cases-mb-xs" onclick="window.location.href='<?=admin_url('cases/documents/upload')?>'">
                    <i class="fas fa-upload"></i> Upload Document
                </button>
                <button class="cases-btn cases-btn-info cases-mb-xs" onclick="window.location.href='<?=admin_url('cases/documents/search')?>'">
                    <i class="fas fa-search"></i> Search Documents
                </button>
                <button class="cases-btn cases-btn-outline cases-mb-xs" onclick="window.location.href='<?=admin_url('cases/documents')?>'">
                    <i class="fas fa-folder"></i> Document Manager
                </button>
            </div>
        </div>
    </div>
    
    <!-- Middle Column: Quick Actions and Recent Activity -->
    <div>
        <!-- Quick Actions -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Quick Actions</h4>
            </div>
            <div class="cases-grid cases-grid-responsive cases-p-2">
                <button class="cases-btn cases-btn-primary cases-mb-xs" onclick="window.location.href='<?=admin_url('cases?new_consultation=1')?>'">
                    <i class="fas fa-plus"></i> New Consultation
                </button>
                <button class="cases-btn cases-btn-success cases-mb-xs" onclick="window.location.href='<?=admin_url('cases/hearings/add')?>'">
                    <i class="fas fa-gavel"></i> Schedule Hearing
                </button>
                <button class="cases-btn cases-btn-info cases-mb-xs" onclick="window.location.href='<?=admin_url('cases')?>'">
                    <i class="fas fa-briefcase"></i> All Cases
                </button>
                <button class="cases-btn cases-btn-secondary cases-mb-xs" onclick="window.location.href='<?=admin_url('cases?tab=consultations')?>'">
                    <i class="fas fa-comments"></i> Consultations
                </button>
                <button class="cases-btn cases-btn-outline cases-mb-xs" onclick="window.location.href='<?=admin_url('clients')?>'">
                    <i class="fas fa-users"></i> Clients
                </button>
                <button class="cases-btn cases-btn-outline cases-mb-xs" onclick="window.location.href='<?=admin_url('invoices')?>'">
                    <i class="fas fa-file-invoice"></i> Invoices
                </button>
            </div>
        </div>
        
    </div>
    
    <!-- Right Column: Calendar and Performance Chart -->
    <div>
        <!-- Additional Metrics -->
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Key Metrics</h4>
            </div>
            <div class="cases-metrics-list">
                <div class="cases-metric-item">
                    <span class="cases-metric-label">Active Cases:</span>
                    <span class="cases-metric-value" id="active-cases-metric">-</span>
                </div>
                <div class="cases-metric-item">
                    <span class="cases-metric-label">Active Clients:</span>
                    <span class="cases-metric-value" id="active-clients">-</span>
                </div>
                <div class="cases-metric-item">
                    <span class="cases-metric-label">This Month Revenue:</span>
                    <span class="cases-metric-value" id="monthly-revenue">-</span>
                </div>
                <div class="cases-metric-item">
                    <span class="cases-metric-label">Outstanding Amount:</span>
                    <span class="cases-metric-value" id="outstanding-amount">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo cases_page_wrapper_end(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminBase = '<?php echo admin_url(); ?>';
    
    // Utility functions
    function htmlEscape(str) {
        if (str == null) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function formatDate(d) {
        if (!d) return '';
        const dt = new Date(d);
        return dt.toLocaleDateString(undefined,{year:'numeric',month:'short',day:'numeric'});
    }
    function formatTime(t) {
        if (!t) return '';
        const dt = new Date(t.includes('T')? t : ('1970-01-01T'+t));
        return dt.toLocaleTimeString(undefined,{hour:'numeric',minute:'2-digit'});
    }
    function formatNumber(num) {
        return num ? num.toLocaleString() : '0';
    }
    function formatCurrency(amount) {
        return amount ? '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2}) : '₹0.00';
    }
    function showLoading(id,msg='Loading...'){
        const c=document.getElementById(id);
        if(c) c.innerHTML=`<div style="text-align:center;padding:20px;color:var(--cases-text-muted);"><i class="fas fa-spinner fa-spin"></i><p>${htmlEscape(msg)}</p></div>`;
    }
    function showEmptyState(id,title,msg){
        const c=document.getElementById(id);
        if(c) c.innerHTML=`<div class="cases-empty-state"><i class="fas fa-check-circle" style="font-size:2rem;color:var(--cases-text-muted);"></i><h5 style="color:var(--cases-text-light);">${htmlEscape(title)}</h5><p style="color:var(--cases-text-muted);">${htmlEscape(msg)}</p></div>`;
    }
    
    // Load Dashboard Statistics
    function loadDashboardStats() {
        fetch(adminBase + 'cases/dashboard/get_stats', {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            if(data.success && data.stats) {
                const stats = data.stats;
                document.getElementById('total-cases').textContent = formatNumber(stats.total_cases);
                document.getElementById('total-consultations').textContent = formatNumber(stats.total_consultations);
                document.getElementById('today-hearings').textContent = formatNumber(stats.today_hearings);
                document.getElementById('success-rate').textContent = stats.success_rate + '%';
                document.getElementById('active-cases-metric').textContent = formatNumber(stats.total_cases);
                document.getElementById('active-clients').textContent = formatNumber(stats.active_clients);
                document.getElementById('monthly-revenue').textContent = formatCurrency(stats.revenue_this_month);
                document.getElementById('outstanding-amount').textContent = formatCurrency(stats.outstanding_amount);
                
                // Document statistics
                if(stats.document_stats) {
                    document.getElementById('total-documents').textContent = formatNumber(stats.document_stats.total_documents);
                    document.getElementById('documents-this-week').textContent = formatNumber(stats.document_stats.documents_this_week);
                    document.getElementById('case-documents').textContent = formatNumber(stats.document_stats.case_documents);
                    document.getElementById('hearing-documents').textContent = formatNumber(stats.document_stats.hearing_documents);
                }
                
                // Growth indicators
                updateGrowthIndicator('cases-growth', stats.cases_growth, 'this month');
                updateGrowthIndicator('consultations-growth', stats.consultations_growth, 'this month');
            }
        }).catch(err=>console.error('Error loading stats:', err));
    }
    
    function updateGrowthIndicator(id, growth, period) {
        const elem = document.getElementById(id);
        if(elem && growth !== undefined) {
            const isPositive = growth > 0;
            const icon = isPositive ? '↗' : growth < 0 ? '↘' : '→';
            const color = isPositive ? 'var(--cases-success)' : growth < 0 ? 'var(--cases-danger)' : 'var(--cases-text-muted)';
            elem.innerHTML = `<span style="color: ${color}">${icon} ${Math.abs(growth)}% ${period}</span>`;
            elem.className = 'cases-stat-change';
        }
    }
    
    // Load Priority Items
    function loadPriorityItems() {
        fetch(adminBase + 'cases/dashboard/get_priority_items', {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            if(data.success && data.items && data.items.length > 0) {
                const section = document.getElementById('priority-alerts-section');
                const container = document.getElementById('priority-items-container');
                section.style.display = 'block';
                
                let html = '<div class="cases-grid cases-grid-responsive">';
                data.items.slice(0, 6).forEach(item => {
                    const statusClass = item.priority === 'high' ? 'caseboard-status-urgent' : 'caseboard-status-today';
                    html += `<div class="cases-card cases-priority-${item.priority}">
                        <div class="cases-card-header">
                            <div class="cases-card-title">${htmlEscape(item.title)}</div>
                            <span class="cases-status-badge ${statusClass}">${htmlEscape(item.status)}</span>
                        </div>
                        <div class="cases-card-body">
                            ${item.subtitle ? `<div class="cases-card-subtitle">${htmlEscape(item.subtitle)}</div>` : ''}
                            <div class="cases-card-meta-grid">
                                ${item.client ? `<div class="cases-card-meta-item"><span class="cases-card-meta-label">Client:</span><span class="cases-card-meta-value">${htmlEscape(item.client)}</span></div>` : ''}
                                ${item.date ? `<div class="cases-card-meta-item"><span class="cases-card-meta-label">Date:</span><span class="cases-card-meta-value">${formatDate(item.date)} ${item.time ? formatTime(item.time) : ''}</span></div>` : ''}
                                ${item.description ? `<div class="cases-card-meta-item"><span class="cases-card-meta-label">Details:</span><span class="cases-card-meta-value">${htmlEscape(item.description)}</span></div>` : ''}
                                ${item.amount ? `<div class="cases-card-meta-item"><span class="cases-card-meta-label">Amount:</span><span class="cases-card-meta-value">${item.currency}${item.amount}</span></div>` : ''}
                            </div>
                        </div>
                        <div class="cases-card-footer">
                            <button class="cases-btn cases-btn-primary" onclick="window.location.href='${item.action_url}'">Take Action</button>
                        </div>
                    </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        }).catch(err=>console.error('Error loading priority items:', err));
    }
    
    // Load Document Activity
    function loadDocumentActivity() {
        fetch(adminBase + 'cases/documents/get_recent_activity', {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            const container = document.getElementById('recent-document-activity');
            if(data.success && data.activities && data.activities.length > 0) {
                let html = '<div class="cases-activity-list">';
                data.activities.slice(0, 5).forEach(activity => {
                    const timeAgo = activity.time_ago || 'Recently';
                    const staffName = activity.staff_name || 'System';
                    html += `<div class="cases-activity-item">
                        <div class="cases-activity-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="cases-activity-content">
                            <div class="cases-activity-title">${activity.message}</div>
                            <div class="cases-activity-meta">
                                <span class="cases-activity-user">${htmlEscape(staffName)}</span>
                                <span class="cases-activity-time">${htmlEscape(timeAgo)}</span>
                            </div>
                        </div>
                    </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                showEmptyState('recent-document-activity', 'No Recent Activity', 'Document activity will appear here when documents are uploaded or modified.');
            }
        }).catch(err=>{
            console.error('Error loading document activity:', err);
            showEmptyState('recent-document-activity', 'Unable to Load', 'Document activity could not be loaded at this time.');
        });
    }
    
    
    
    
    // Event Handlers
    document.getElementById('dismiss-priority')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('priority-alerts-section').style.display = 'none';
    });
    
    // Responsive layout adjustments
    function adjustLayout() {
        const grid = document.getElementById('main-dashboard-grid');
        if(window.innerWidth < 1200) {
            grid.style.gridTemplateColumns = '1fr 1fr';
        } else if(window.innerWidth < 768) {
            grid.style.gridTemplateColumns = '1fr';
        } else {
            grid.style.gridTemplateColumns = '2fr 1fr 1fr';
        }
    }
    
    window.addEventListener('resize', adjustLayout);
    
    // Initialize Dashboard
    function initialize() {
        loadDashboardStats();
        loadPriorityItems();
        loadDocumentActivity();
        adjustLayout();
    }
    
    initialize();
    
    // Auto-refresh every 5 minutes
    setInterval(() => {
        loadDashboardStats();
        loadPriorityItems();
        loadDocumentActivity();
    }, 300000);
});
</script>
<?php init_tail(); ?>