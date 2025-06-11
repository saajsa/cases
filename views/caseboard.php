<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'status', 'tables', 'modals'], 'caseboard');
echo cases_page_wrapper_start(
    'Legal Practice Dashboard',
    'Comprehensive overview of your practice',
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

<!-- Enhanced Statistics Dashboard -->
<div class="cases-grid cases-grid-auto cases-mb-lg">
    <div class="cases-stat-card">
        <div class="cases-stat-number" id="total-cases-stat">-</div>
        <div class="cases-stat-label">Total Cases</div>
        <div class="cases-text-success cases-font-size-sm" id="cases-change">Loading...</div>
    </div>
    
    <div class="cases-stat-card">
        <div class="cases-stat-number" id="revenue-stat">₹-</div>
        <div class="cases-stat-label">Revenue (MTD)</div>
        <div class="cases-text-success cases-font-size-sm" id="revenue-change">Loading...</div>
    </div>
    
    <div class="cases-stat-card">
        <div class="cases-stat-number" id="urgent-stat">-</div>
        <div class="cases-stat-label">Urgent Items</div>
        <div class="cases-text-warning cases-font-size-sm" id="urgent-change">Require attention</div>
    </div>
    
    <div class="cases-stat-card">
        <div class="cases-stat-number" id="pending-stat">-</div>
        <div class="cases-stat-label">Pending Tasks</div>
        <div class="cases-text-info cases-font-size-sm" id="pending-change">Due today</div>
    </div>
    
    <div class="cases-stat-card">
        <div class="cases-stat-number" id="success-rate-stat">-%</div>
        <div class="cases-stat-label">Success Rate</div>
        <div class="cases-text-success cases-font-size-sm" id="success-change">Favorable outcomes</div>
    </div>
    
    <div class="cases-stat-card">
        <div class="cases-stat-number" id="consultations-stat">-</div>
        <div class="cases-stat-label">Consultations</div>
        <div class="cases-text-info cases-font-size-sm" id="consultations-change">This week</div>
    </div>
</div>

<!-- Quick Actions Bar -->
<div class="cases-grid cases-grid-4 cases-mb-lg">
    <div class="cases-card cases-text-center cases-hover-lift" onclick="openQuickConsultation()" style="cursor: pointer;">
        <div style="width: 40px; height: 40px; background: var(--cases-bg-tertiary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--cases-spacing-sm) auto; color: var(--cases-primary);">
            <i class="fas fa-user-plus"></i>
        </div>
        <h5 class="cases-font-weight-semibold cases-mb-xs">Quick Consultation</h5>
        <p class="cases-text-muted cases-font-size-sm">Add new client consultation</p>
    </div>
    
    <div class="cases-card cases-text-center cases-hover-lift" onclick="openHearingScheduler()" style="cursor: pointer;">
        <div style="width: 40px; height: 40px; background: var(--cases-bg-tertiary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--cases-spacing-sm) auto; color: var(--cases-primary);">
            <i class="fas fa-calendar-plus"></i>
        </div>
        <h5 class="cases-font-weight-semibold cases-mb-xs">Schedule Hearing</h5>
        <p class="cases-text-muted cases-font-size-sm">Book next court appearance</p>
    </div>
    
    <div class="cases-card cases-text-center cases-hover-lift" onclick="openDocumentUpload()" style="cursor: pointer;">
        <div style="width: 40px; height: 40px; background: var(--cases-bg-tertiary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--cases-spacing-sm) auto; color: var(--cases-primary);">
            <i class="fas fa-cloud-upload-alt"></i>
        </div>
        <h5 class="cases-font-weight-semibold cases-mb-xs">Upload Documents</h5>
        <p class="cases-text-muted cases-font-size-sm">Add case files & evidence</p>
    </div>
    
    <div class="cases-card cases-text-center cases-hover-lift" onclick="generateReport()" style="cursor: pointer;">
        <div style="width: 40px; height: 40px; background: var(--cases-bg-tertiary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--cases-spacing-sm) auto; color: var(--cases-primary);">
            <i class="fas fa-chart-bar"></i>
        </div>
        <h5 class="cases-font-weight-semibold cases-mb-xs">Generate Report</h5>
        <p class="cases-text-muted cases-font-size-sm">Practice performance analytics</p>
    </div>
</div>

<!-- Main Dashboard Grid -->
<div class="cases-grid" style="grid-template-columns: 2fr 1fr; gap: var(--cases-spacing-lg);">
    <!-- Left Column - Main Content -->
    <div>
        <!-- Advanced Filter Bar -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-flex cases-flex-wrap" style="gap: var(--cases-spacing-sm); align-items: center;">
                <input type="text" class="cases-form-control" id="global-search" 
                       placeholder="Search cases, clients, hearings..." style="flex: 1; min-width: 250px;">
                <select class="cases-form-control" id="status-filter" style="min-width: 150px;">
                    <option value="">All Status</option>
                    <option value="urgent">Urgent</option>
                    <option value="today">Due Today</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="overdue">Overdue</option>
                </select>
                <select class="cases-form-control" id="date-filter" style="min-width: 150px;">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>

        <!-- Today's Priority Items -->
        <div class="cases-section-with-actions">
            <h3 class="cases-section-title">Today's Priority Items</h3>
            <div class="cases-section-actions">
                <a href="#" onclick="refreshPriorityItems()">Refresh →</a>
            </div>
        </div>
        <div id="priority-items-container" class="cases-mb-lg">
            <?php echo cases_loading_state('Loading priority items...'); ?>
        </div>

        <!-- Enhanced Upcoming Hearings -->
        <div class="cases-section-with-actions">
            <h3 class="cases-section-title">Upcoming Hearings</h3>
            <div class="cases-section-actions">
                <a href="<?php echo admin_url('cases/hearings/causelist'); ?>">View Cause List →</a>
            </div>
        </div>
        <div id="hearings-container" class="cases-mb-lg">
            <?php echo cases_loading_state('Loading upcoming hearings...'); ?>
        </div>

        <!-- Enhanced Active Cases -->
        <div class="cases-section-with-actions">
            <h3 class="cases-section-title">Active Cases</h3>
            <div class="cases-section-actions">
                <a href="<?php echo admin_url('cases'); ?>">View All Cases →</a>
            </div>
        </div>
        <div id="cases-container">
            <?php echo cases_loading_state('Loading active cases...'); ?>
        </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div>
        <!-- Mini Calendar -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Calendar</h4>
            </div>
            <div class="cases-flex cases-flex-between cases-flex-center cases-mb-md">
                <button class="cases-btn cases-btn-sm" id="prev-month">‹</button>
                <h5 id="calendar-month" class="cases-m-0">Loading...</h5>
                <button class="cases-btn cases-btn-sm" id="next-month">›</button>
            </div>
            <div id="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: var(--cases-font-size-sm);">
                <!-- Calendar will be generated by JavaScript -->
            </div>
            <div class="cases-mt-sm cases-font-size-xs cases-text-muted">
                <span style="color: var(--cases-warning);">●</span> Hearings
                <span style="color: var(--cases-danger); margin-left: 10px;">●</span> Urgent
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Recent Activity</h4>
            </div>
            <div id="activity-feed" style="max-height: 300px; overflow-y: auto;">
                <?php echo cases_loading_state('Loading recent activity...'); ?>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Performance Overview</h4>
            </div>
            <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: var(--cases-bg-tertiary); border-radius: var(--cases-radius);">
                <canvas id="performance-chart" width="100%" height="200"></canvas>
            </div>
        </div>

        <!-- Notifications Panel -->
        <div class="cases-info-card">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Notifications</h4>
            </div>
            <div id="notifications-panel">
                <div class="cases-flex cases-flex-between cases-mb-sm" style="padding: 10px; border: 1px solid var(--cases-border-light); border-radius: var(--cases-radius);">
                    <div>
                        <strong class="cases-text-warning">Upcoming Deadlines</strong>
                        <p class="cases-text-muted cases-font-size-sm cases-m-0">3 cases require attention this week</p>
                    </div>
                    <span class="cases-notification-badge">3</span>
                </div>
                <div class="cases-flex cases-flex-between" style="padding: 10px; border: 1px solid var(--cases-border-light); border-radius: var(--cases-radius);">
                    <div>
                        <strong class="cases-text-danger">Payment Reminders</strong>
                        <p class="cases-text-muted cases-font-size-sm cases-m-0">2 invoices are overdue</p>
                    </div>
                    <span class="cases-notification-badge">2</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Consultation Modal -->
<div class="modal fade" id="quickConsultationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="cases-modal-content">
            <div class="cases-modal-header">
                <h5 class="cases-modal-title">Quick Consultation</h5>
                <button type="button" class="cases-modal-close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="quickConsultationForm">
                <div class="cases-modal-body">
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Client</label>
                        <select name="client_id" class="cases-form-control" required>
                            <option value="">Select Client</option>
                            <?php if (isset($clients) && !empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['userid']; ?>">
                                        <?php echo htmlspecialchars($client['company']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="cases-form-group">
                        <label class="cases-form-label cases-label-required">Brief Note</label>
                        <textarea name="note" class="cases-form-control cases-textarea" rows="3" 
                                  placeholder="Quick consultation summary..." required></textarea>
                    </div>
                </div>
                <div class="cases-modal-footer">
                    <?php echo cases_button('Cancel', [
                        'type' => 'default',
                        'data' => ['dismiss' => 'modal']
                    ]); ?>
                    <?php echo cases_button('Save Consultation', [
                        'type' => 'primary',
                        'button_type' => 'submit'
                    ]); ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo cases_page_wrapper_end(); ?>

<?php init_tail(); ?>