<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-8">
                    <h1>Appointment Management</h1>
                    <div class="subtitle">Schedule and manage client appointments</div>
                </div>
                <div class="col-md-4">
                    <div class="page-actions text-right">
                        <a href="<?php echo admin_url('cases/calendar'); ?>" class="btn">
                            <i class="fas fa-calendar-alt"></i> Calendar View
                        </a>
                        <a href="<?php echo admin_url('cases/availability'); ?>" class="btn">
                            <i class="fas fa-clock"></i> Availability
                        </a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#appointmentModal">
                            <i class="fas fa-plus"></i> Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Content Header -->
            <div class="content-header">
                <h3 class="section-title">All Appointments</h3>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Date From</label>
                        <input type="date" id="date_from" class="filter-control" value="<?php echo date('Y-m-01'); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Date To</label>
                        <input type="date" id="date_to" class="filter-control" value="<?php echo date('Y-m-t'); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Staff</label>
                        <select id="staff_filter" class="filter-control">
                            <option value="">All Staff</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?php echo $member['staffid']; ?>">
                                    <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select id="status_filter" class="filter-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Client</label>
                        <select id="client_filter" class="filter-control">
                            <option value="">All Clients</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['userid']; ?>">
                                    <?php echo htmlspecialchars($client['company'] ?: 'Individual Client'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Service</label>
                        <select id="service_filter" class="filter-control">
                            <option value="">All Services</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary" id="apply-filters">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button class="btn" id="clear-filters">
                        <i class="fas fa-times"></i> Clear
                    </button>
                    <button class="btn" id="export-appointments">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Statistics Row -->
            <div class="statistics-section">
                <div class="row" id="statistics-row">
                    <div class="col-md-2">
                        <div class="stat-card">
                            <div class="stat-number" id="total-appointments">-</div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card">
                            <div class="stat-number confirmed" id="confirmed-appointments">-</div>
                            <div class="stat-label">Confirmed</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card">
                            <div class="stat-number pending" id="pending-appointments">-</div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card">
                            <div class="stat-number completed" id="completed-appointments">-</div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card">
                            <div class="stat-number cancelled" id="cancelled-appointments">-</div>
                            <div class="stat-label">Cancelled</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card">
                            <div class="stat-number revenue" id="total-revenue">₹0</div>
                            <div class="stat-label">Revenue</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Controls -->
            <div class="view-controls">
                <div class="sort-controls">
                    <label class="sort-label">Sort by:</label>
                    <select id="sort-select" class="sort-select">
                        <option value="date_desc">Date (Newest First)</option>
                        <option value="date_asc">Date (Oldest First)</option>
                        <option value="client_asc">Client Name (A-Z)</option>
                        <option value="status_asc">Status</option>
                        <option value="amount_desc">Amount (High to Low)</option>
                        <option value="amount_asc">Amount (Low to High)</option>
                    </select>
                </div>
                <div class="view-options">
                    <div class="results-count" id="results-count">Loading...</div>
                </div>
            </div>

            <!-- Cards Section -->
            <div class="cards-section">
                <div id="appointments-container">
                    <!-- Appointment cards will be loaded here -->
                </div>
                
                <!-- Pagination -->
                <div id="pagination-container">
                    <!-- Pagination will be loaded here -->
                </div>
                
                <!-- Loading State -->
                <div id="cards-loading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Loading appointments...</div>
                </div>
                
                <!-- Empty State -->
                <div id="cards-empty" style="display: none;">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h5>No Appointments Found</h5>
                        <p>No appointments match your current filters.</p>
                        <button class="action-btn btn-primary" data-toggle="modal" data-target="#appointmentModal">
                            <i class="fas fa-plus"></i> Book First Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Book Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="appointmentForm">
                <div class="modal-header">
                    <h4 class="modal-title">Book New Appointment</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Client <span class="required">*</span></label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['userid']; ?>">
                                        <?php echo htmlspecialchars($client['company'] ?: 'Individual Client'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" id="contact-group" style="display: none;">
                            <label class="form-label">Contact Person</label>
                            <select name="contact_id" id="contact_id" class="form-control">
                                <option value="">Select Contact</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Service <span class="required">*</span></label>
                            <select name="service_id" id="service_id" class="form-control" required>
                                <option value="">Select Service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['id']; ?>" 
                                            data-price="<?php echo $service['price']; ?>"
                                            data-duration="<?php echo $service['duration_minutes']; ?>">
                                        <?php echo htmlspecialchars($service['name']); ?> 
                                        (<?php echo $service['duration_minutes']; ?> min - ₹<?php echo number_format($service['price'], 2); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Staff <span class="required">*</span></label>
                            <select name="staff_id" id="staff_id" class="form-control" required>
                                <option value="">Select Staff</option>
                                <?php foreach ($staff as $member): ?>
                                    <option value="<?php echo $member['staffid']; ?>">
                                        <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Date <span class="required">*</span></label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Time <span class="required">*</span></label>
                            <select name="start_time" id="start_time" class="form-control" required>
                                <option value="">Select Date First</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Any special requirements or notes"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="appointment-summary">
                            <div class="summary-title">Appointment Summary</div>
                            <div id="appointment-summary">
                                <div>Service: <span id="summary-service">-</span></div>
                                <div>Duration: <span id="summary-duration">-</span></div>
                                <div>Amount: <span id="summary-amount">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="action-btn btn-primary">
                        <i class="fas fa-save"></i> Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Appointment Details</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="appointment-details-content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn btn-default" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rescheduleForm">
                <input type="hidden" id="reschedule_appointment_id" name="appointment_id">
                <div class="modal-header">
                    <h4 class="modal-title">Reschedule Appointment</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">New Date</label>
                            <input type="date" name="new_date" id="new_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Time</label>
                            <select name="new_time" id="new_time" class="form-control" required>
                                <option value="">Select Date First</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reason for Rescheduling</label>
                        <textarea name="reschedule_reason" class="form-control" rows="3" placeholder="Optional reason"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="action-btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>

    /* Appointments Management Styles */

/* Base Styles */
* {
    box-sizing: border-box;
}

body {
    background: #fafafa;
    color: #2c2c2c;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Page Header */
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

/* Main Content */
.main-content {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    overflow: hidden;
}

.content-header {
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
    padding: 25px 30px;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: -0.01em;
}

/* Filters Section */
.filters-section {
    padding: 25px 30px;
    border-bottom: 1px solid #e1e1e1;
    background: #f8f8f8;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.filter-control {
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.filter-control:focus {
    outline: none;
    border-color: #1a1a1a;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Statistics Section */
.statistics-section {
    padding: 20px 30px;
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
}

.stat-card {
    text-align: center;
    padding: 15px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
}

.stat-number.confirmed {
    color: #1a6bcc;
}

.stat-number.pending {
    color: #cc8c1a;
}

.stat-number.completed {
    color: #2d7d2d;
}

.stat-number.cancelled {
    color: #cc1a1a;
}

.stat-number.revenue {
    color: #2d7d2d;
}

.stat-label {
    font-size: 0.75rem;
    color: #666666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* View Controls */
.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
}

.sort-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}

.sort-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
}

.sort-select {
    padding: 8px 12px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
    min-width: 150px;
}

.view-options {
    display: flex;
    align-items: center;
    gap: 10px;
}

.results-count {
    font-size: 0.875rem;
    color: #666666;
}

/* Cards Section */
.cards-section {
    padding: 30px;
}

.appointment-card {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: all 0.15s ease;
    position: relative;
}

.appointment-card:hover {
    border-color: #d1d1d1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.appointment-card.status-confirmed {
    border-left: 3px solid #1a6bcc;
}

.appointment-card.status-completed {
    border-left: 3px solid #2d7d2d;
}

.appointment-card.status-pending {
    border-left: 3px solid #cc8c1a;
}

.appointment-card.status-cancelled {
    border-left: 3px solid #cc1a1a;
}

.appointment-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.appointment-date-time {
    flex: 0 0 auto;
}

.appointment-date {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 4px;
}

.appointment-time {
    font-size: 0.875rem;
    color: #666666;
    font-weight: 500;
}

.appointment-status-container {
    flex: 1 1 auto;
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.appointment-card-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 20px;
}

.appointment-info-section h6 {
    font-size: 0.75rem;
    font-weight: 600;
    color: #666666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.appointment-info-section .info-value {
    font-size: 0.875rem;
    color: #1a1a1a;
    font-weight: 500;
    margin-bottom: 12px;
}

.appointment-service-info {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.appointment-service-name {
    font-weight: 600;
    color: #1a1a1a;
}

.appointment-service-duration {
    font-size: 0.75rem;
    color: #666666;
    background: #f8f8f8;
    padding: 2px 8px;
    border-radius: 1px;
}

.appointment-amount {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
}

.appointment-card-footer {
    border-top: 1px solid #f5f5f5;
    padding-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.appointment-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.appointment-meta {
    font-size: 0.75rem;
    color: #999999;
}

/* Notes styling in cards */
.appointment-notes {
    background: #f8f8f8;
    padding: 15px;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    margin-top: 15px;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #2c2c2c;
}

.appointment-notes strong {
    color: #1a1a1a;
    font-weight: 600;
}

/* Action Buttons */
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
    display: inline-block;
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

.action-btn.btn-danger {
    background: #ffffff;
    border-color: #cc1a1a;
    color: #cc1a1a;
}

.action-btn.btn-danger:hover {
    background: #cc1a1a;
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

.action-btn.btn-warning {
    background: #ffffff;
    border-color: #cc8c1a;
    color: #cc8c1a;
}

.action-btn.btn-warning:hover {
    background: #cc8c1a;
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

/* Status Badges */
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

.status-pending { 
    background: #fff8e6; 
    color: #cc8c1a; 
    border-color: #cc8c1a; 
}

.status-confirmed { 
    background: #eff8ff; 
    color: #1a6bcc; 
    border-color: #1a6bcc; 
}

.status-completed { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.status-cancelled { 
    background: #fff0f0; 
    color: #cc1a1a; 
    border-color: #cc1a1a; 
}

.status-no_show { 
    background: #f5f5f5; 
    color: #666666; 
    border-color: #666666; 
}

.status-rescheduled { 
    background: #fff8e6; 
    color: #cc8c1a; 
    border-color: #cc8c1a; 
}

.payment-status-unpaid { 
    background: #fff0f0; 
    color: #cc1a1a; 
    border-color: #cc1a1a; 
}

.payment-status-partial { 
    background: #fff8e6; 
    color: #cc8c1a; 
    border-color: #cc8c1a; 
}

.payment-status-paid { 
    background: #f0f9f0; 
    color: #2d7d2d; 
    border-color: #2d7d2d; 
}

.payment-status-refunded { 
    background: #f5f5f5; 
    color: #666666; 
    border-color: #666666; 
}

/* Pagination */
#pagination-container {
    margin-top: 30px;
    text-align: center;
}

.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.pagination-btn {
    padding: 8px 16px;
    border: 1px solid #d1d1d1;
    background: #ffffff;
    color: #2c2c2c;
    text-decoration: none;
    border-radius: 1px;
    font-size: 0.875rem;
    transition: all 0.15s ease;
    cursor: pointer;
}

.pagination-btn:hover {
    background: #f8f8f8;
    border-color: #999999;
    color: #1a1a1a;
}

.pagination-btn.active {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.875rem;
    color: #666666;
    margin: 0 15px;
}

/* Loading and Empty States */
#cards-loading {
    text-align: center;
    padding: 60px;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e1e1e1;
    border-top: 3px solid #1a1a1a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

.loading-text {
    margin-top: 20px;
    color: #666666;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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

/* Modal Styles */
.modal-content {
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.modal-header {
    background: #f8f8f8;
    border-bottom: 1px solid #e1e1e1;
    padding: 20px 30px;
}

.modal-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    background: #f8f8f8;
    border-top: 1px solid #e1e1e1;
    padding: 20px 30px;
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.form-control:focus {
    outline: none;
    border-color: #1a1a1a;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.required {
    color: #cc1a1a;
}

.appointment-summary {
    background: #f8f8f8;
    padding: 15px;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
}

.summary-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #1a1a1a;
}

#appointment-summary div {
    margin-bottom: 4px;
    font-size: 0.875rem;
    color: #2c2c2c;
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
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .cards-section {
        padding: 20px;
    }
    
    .appointment-card {
        padding: 20px;
    }
    
    .appointment-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .appointment-status-container {
        align-items: flex-start;
        text-align: left;
    }
    
    .appointment-card-body {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .appointment-card-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .appointment-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .action-btn {
        flex: 1;
        text-align: center;
        min-width: 80px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .view-controls {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .sort-controls {
        width: 100%;
    }
    
    .sort-select {
        width: 100%;
    }
}

@media (max-width: 1200px) {
    .appointment-card {
        padding: 20px;
    }
    
    .appointment-card-body {
        gap: 20px;
    }
}

/* Print Styles */
@media print {
    .page-actions,
    .filters-section,
    .view-controls,
    .appointment-actions {
        display: none;
    }
    
    .main-content,
    .appointment-card {
        border: 1px solid #000000;
        box-shadow: none;
    }
    
    .status-badge {
        border: 1px solid #000000;
    }
    
    .appointment-card {
        break-inside: avoid;
        margin-bottom: 15px;
    }
}

/* Utility Classes */
.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.text-left {
    text-align: left;
}

.mb-0 {
    margin-bottom: 0;
}

.mt-10 {
    margin-top: 10px;
}

.mt-15 {
    margin-top: 15px;
}

.mt-20 {
    margin-top: 20px;
}

.hidden {
    display: none;
}

.visible {
    display: block;
}
</style>
<script>
  // Appointment Management JavaScript - Fixed Version
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let appointmentsData = [];
    let filteredData = [];
    let currentPage = 1;
    let itemsPerPage = 12;
    let currentSort = 'date_desc';
    
    // Get admin URL from current page or set default
    const adminUrl = (function() {
        const currentUrl = window.location.href;
        const match = currentUrl.match(/(.*?\/admin\/)/);
        return match ? match[1] : window.location.origin + '/admin/';
    })();
    
    // Get CSRF token - check multiple sources
    let csrfToken = '';
    let csrfHash = '';
    
    // Try to get CSRF from meta tag first
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
        csrfToken = metaToken.getAttribute('content');
    }
    
    // Try to get from hidden input
    if (!csrfToken) {
        const hiddenInput = document.querySelector('input[name*="csrf"]');
        if (hiddenInput) {
            csrfToken = hiddenInput.name;
            csrfHash = hiddenInput.value;
        }
    }
    
    // Cache for contacts and time slots
    const contactsCache = new Map();
    const timeSlotsCache = new Map();
    
    // API Helper Functions - Define early
    const API = {
        post: function(url, data) {
            const formData = new FormData();
            
            if (data instanceof FormData) {
                // Copy FormData entries
                for (let [key, value] of data.entries()) {
                    formData.append(key, value);
                }
            } else if (typeof data === 'object' && data !== null) {
                // Convert object to FormData
                for (let key in data) {
                    if (data.hasOwnProperty(key)) {
                        formData.append(key, data[key]);
                    }
                }
            }
            
            // Add CSRF token if available
            if (csrfToken && csrfHash) {
                formData.append(csrfToken, csrfHash);
            }
            
            return fetch(adminUrl + url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            }).then(data => {
                // Update CSRF hash if provided
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }
                return data;
            }).catch(error => {
                console.error('API POST Error:', error);
                throw error;
            });
        },
        
        get: function(url, params) {
            const queryString = params ? '?' + new URLSearchParams(params).toString() : '';
            return fetch(adminUrl + url + queryString, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            }).then(data => {
                // Update CSRF hash if provided
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }
                return data;
            }).catch(error => {
                console.error('API GET Error:', error);
                throw error;
            });
        }
    };
    
    // Utility Functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function showLoading() {
        // Remove existing overlay first
        hideLoading();
        
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.style.cssText = `
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%;
            background: rgba(250, 250, 250, 0.9); 
            display: flex;
            align-items: center; 
            justify-content: center; 
            z-index: 9999;
        `;
        overlay.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(overlay);
    }
    
    function hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    function showAlert(type, message) {
        // Remove existing alerts
        document.querySelectorAll('.alert').forEach(alert => alert.remove());
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.style.cssText = `
            position: fixed; 
            top: 20px; 
            right: 20px; 
            z-index: 10000; 
            min-width: 300px;
            max-width: 500px;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        alertDiv.innerHTML = `
            <button type="button" class="close" style="position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 20px; cursor: pointer;" onclick="this.parentElement.remove()">&times;</button>
            <div style="margin-right: 30px;">${escapeHtml(message)}</div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    function formatCurrency(amount) {
        const num = parseFloat(amount || 0);
        return '₹' + num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Invalid Date';
            return date.toLocaleDateString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch (e) {
            return 'Invalid Date';
        }
    }
    
    function formatTime(timeString) {
        if (!timeString) return 'N/A';
        try {
            const time = new Date('2000-01-01 ' + timeString);
            if (isNaN(time.getTime())) return 'Invalid Time';
            return time.toLocaleTimeString('en-IN', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        } catch (e) {
            return 'Invalid Time';
        }
    }
    
    // Helper functions for safe DOM manipulation
    function getElementValue(id) {
        const element = document.getElementById(id);
        return element ? element.value : '';
    }
    
    function setElementText(id, text) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = text;
        }
    }
    
    function setElementHTML(id, html) {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = html;
        }
    }
    
    // Data Loading Functions
    function loadAppointments() {
        showCardsLoading();
        
        const filters = {
            date_from: getElementValue('date_from'),
            date_to: getElementValue('date_to'),
            staff_id: getElementValue('staff_filter'),
            status: getElementValue('status_filter'),
            client_id: getElementValue('client_filter'),
            service_id: getElementValue('service_filter')
        };
        
        API.get('cases/appointments_list', filters)
            .then(data => {
                hideCardsLoading();
                
                if (data.error) {
                    showAlert('danger', 'Error: ' + data.error);
                    showEmptyState();
                    return;
                }
                
                appointmentsData = Array.isArray(data.data) ? data.data : [];
                applyFiltersAndSort();
                updateStatistics(appointmentsData);
            })
            .catch(error => {
                hideCardsLoading();
                console.error('Error loading appointments:', error);
                showAlert('danger', 'Failed to load appointments. Please refresh the page.');
                showEmptyState();
            });
    }
    
    function loadStatistics() {
        const dateFrom = getElementValue('date_from');
        const dateTo = getElementValue('date_to');
        
        API.get('cases/statistics', { date_from: dateFrom, date_to: dateTo })
            .then(data => {
                if (data.success && data.data) {
                    const stats = data.data;
                    setElementText('total-appointments', stats.total || 0);
                    setElementText('pending-appointments', stats.by_status?.pending || 0);
                    setElementText('confirmed-appointments', stats.by_status?.confirmed || 0);
                    setElementText('completed-appointments', stats.by_status?.completed || 0);
                    setElementText('cancelled-appointments', stats.by_status?.cancelled || 0);
                    setElementText('total-revenue', formatCurrency(stats.total_revenue || 0));
                }
            })
            .catch(error => {
                console.error('Error loading statistics:', error);
                // Don't show error alert for statistics - it's not critical
            });
    }
    
    // Helper functions for safe DOM manipulation
    function getElementValue(id) {
        const element = document.getElementById(id);
        return element ? element.value : '';
    }
    
    function setElementText(id, text) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = text;
        }
    }
    
    function setElementHTML(id, html) {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = html;
        }
    }
    
    // Data Processing Functions
    function applyFiltersAndSort() {
        filteredData = [...appointmentsData];
        
        switch (currentSort) {
            case 'date_desc':
                filteredData.sort((a, b) => {
                    const dateA = new Date(a.appointment_date + ' ' + (a.start_time || '00:00:00'));
                    const dateB = new Date(b.appointment_date + ' ' + (b.start_time || '00:00:00'));
                    return dateB - dateA;
                });
                break;
            case 'date_asc':
                filteredData.sort((a, b) => {
                    const dateA = new Date(a.appointment_date + ' ' + (a.start_time || '00:00:00'));
                    const dateB = new Date(b.appointment_date + ' ' + (b.start_time || '00:00:00'));
                    return dateA - dateB;
                });
                break;
            case 'client_asc':
                filteredData.sort((a, b) => (a.client_name || '').localeCompare(b.client_name || ''));
                break;
            case 'status_asc':
                filteredData.sort((a, b) => (a.status || '').localeCompare(b.status || ''));
                break;
            case 'amount_desc':
                filteredData.sort((a, b) => parseFloat(b.total_amount || 0) - parseFloat(a.total_amount || 0));
                break;
            case 'amount_asc':
                filteredData.sort((a, b) => parseFloat(a.total_amount || 0) - parseFloat(b.total_amount || 0));
                break;
        }
        
        currentPage = 1;
        renderAppointmentCards();
        renderPagination();
        updateResultsCount();
    }
    
    function updateStatistics(data) {
        if (!Array.isArray(data)) return;
        
        const stats = {
            total: data.length,
            pending: 0,
            confirmed: 0,
            completed: 0,
            cancelled: 0,
            revenue: 0
        };
        
        data.forEach(appointment => {
            const status = appointment.status || 'unknown';
            if (stats.hasOwnProperty(status)) {
                stats[status]++;
            }
            
            if (appointment.payment_status === 'paid' || appointment.status === 'completed') {
                stats.revenue += parseFloat(appointment.total_amount || 0);
            }
        });
        
        setElementText('total-appointments', stats.total);
        setElementText('pending-appointments', stats.pending);
        setElementText('confirmed-appointments', stats.confirmed);
        setElementText('completed-appointments', stats.completed);
        setElementText('cancelled-appointments', stats.cancelled);
        setElementText('total-revenue', formatCurrency(stats.revenue));
    }
    
    // Rendering Functions
    function renderAppointmentCards() {
        const container = document.getElementById('appointments-container');
        if (!container) return;
        
        if (filteredData.length === 0) {
            showEmptyState();
            return;
        }
        
        hideEmptyState();
        
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
        const pageData = filteredData.slice(startIndex, endIndex);
        
        let cardsHtml = '';
        pageData.forEach(appointment => {
            cardsHtml += createAppointmentCard(appointment);
        });
        
        container.innerHTML = cardsHtml;
        
        // Add event listeners to action buttons
        container.querySelectorAll('.appointment-action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.getAttribute('data-action');
                const appointmentId = this.getAttribute('data-appointment-id');
                
                if (window.appointmentManager && window.appointmentManager[action]) {
                    window.appointmentManager[action](appointmentId);
                }
            });
        });
    }
    
    function createAppointmentCard(appointment) {
        if (!appointment) return '';
        
        const date = formatDate(appointment.appointment_date);
        const startTime = formatTime(appointment.start_time);
        const endTime = formatTime(appointment.end_time);
        const status = appointment.status || 'unknown';
        const paymentStatus = appointment.payment_status || 'unpaid';
        
        let cardClass = 'appointment-card';
        if (status === 'confirmed') cardClass += ' status-confirmed';
        else if (status === 'completed') cardClass += ' status-completed';
        else if (status === 'pending') cardClass += ' status-pending';
        else if (status === 'cancelled') cardClass += ' status-cancelled';
        
        let actionButtons = '';
        
        // View action (always available)
        actionButtons += `<button class="action-btn btn-info appointment-action-btn" 
                                 data-action="view" data-appointment-id="${appointment.id}" 
                                 title="View Details">
                            <i class="fas fa-eye"></i>
                          </button>`;
        
        if (status === 'pending' || status === 'confirmed') {
            actionButtons += `<button class="action-btn btn-success appointment-action-btn" 
                                     data-action="complete" data-appointment-id="${appointment.id}" 
                                     title="Mark Complete">
                                <i class="fas fa-check"></i>
                              </button>`;
            
            actionButtons += `<button class="action-btn btn-warning appointment-action-btn" 
                                     data-action="reschedule" data-appointment-id="${appointment.id}" 
                                     title="Reschedule">
                                <i class="fas fa-calendar-alt"></i>
                              </button>`;
            
            actionButtons += `<button class="action-btn btn-danger appointment-action-btn" 
                                     data-action="cancel" data-appointment-id="${appointment.id}" 
                                     title="Cancel">
                                <i class="fas fa-times"></i>
                              </button>`;
        } else if (status === 'completed') {
            if (!appointment.consultation_id) {
                actionButtons += `<button class="action-btn btn-primary appointment-action-btn" 
                                         data-action="convert" data-appointment-id="${appointment.id}" 
                                         title="Convert to Consultation">
                                    <i class="fas fa-exchange-alt"></i>
                                  </button>`;
            }
            
            if (!appointment.invoice_id) {
                actionButtons += `<button class="action-btn btn-info appointment-action-btn" 
                                         data-action="generateInvoice" data-appointment-id="${appointment.id}" 
                                         title="Generate Invoice">
                                    <i class="fas fa-file-invoice"></i>
                                  </button>`;
            }
        }
        
        const statusText = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        const paymentStatusText = paymentStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        let notesSection = '';
        if (appointment.notes) {
            notesSection = `
                <div class="appointment-notes">
                    <strong>Notes:</strong> ${escapeHtml(appointment.notes)}
                </div>
            `;
        }
        
        let consultationLink = '';
        if (appointment.consultation_id) {
            consultationLink = `
                <div style="margin-top: 10px;">
                    <span style="font-size: 0.75rem; color: #2d7d2d;">
                        <i class="fas fa-link"></i> 
                        <a href="${adminUrl}cases/view_consultation/${appointment.consultation_id}" 
                           target="_blank" style="color: #2d7d2d; text-decoration: none;">
                            Linked to Consultation #${appointment.consultation_id}
                        </a>
                    </span>
                </div>
            `;
        }
        
        let invoiceLink = '';
        if (appointment.invoice_id) {
            invoiceLink = `
                <div style="margin-top: 5px;">
                    <span style="font-size: 0.75rem; color: #1a6bcc;">
                        <i class="fas fa-file-invoice"></i> 
                        <a href="${adminUrl}invoices/list_invoices/${appointment.invoice_id}" 
                           target="_blank" style="color: #1a6bcc; text-decoration: none;">
                            Invoice #${appointment.invoice_id}
                        </a>
                    </span>
                </div>
            `;
        }
        
        return `
            <div class="${cardClass}">
                <div class="appointment-card-header">
                    <div class="appointment-date-time">
                        <div class="appointment-date">${date}</div>
                        <div class="appointment-time">${startTime} - ${endTime}</div>
                    </div>
                    <div class="appointment-status-container">
                        <span class="status-badge status-${status}">${statusText}</span>
                        <span class="status-badge payment-status-${paymentStatus}">${paymentStatusText}</span>
                    </div>
                </div>
                
                <div class="appointment-card-body">
                    <div class="appointment-info-section">
                        <h6>Client Information</h6>
                        <div class="info-value">${escapeHtml(appointment.client_name || 'Unknown Client')}</div>
                        ${appointment.contact_name ? `<div style="font-size: 0.8rem; color: #666666;">${escapeHtml(appointment.contact_name)}</div>` : ''}
                        
                        <h6 style="margin-top: 15px;">Service Details</h6>
                        <div class="appointment-service-info">
                            <span class="appointment-service-name">${escapeHtml(appointment.service_name || 'Unknown Service')}</span>
                            ${appointment.duration_minutes ? `<span class="appointment-service-duration">${appointment.duration_minutes} min</span>` : ''}
                        </div>
                        <div style="font-size: 0.8rem; color: #666666;">
                            ${escapeHtml(appointment.staff_full_name || 'Unknown Staff')}
                        </div>
                    </div>
                    
                    <div class="appointment-info-section">
                        <h6>Amount & Payment</h6>
                        <div class="appointment-amount">${formatCurrency(appointment.total_amount)}</div>
                        ${appointment.paid_amount > 0 ? `<div style="font-size: 0.8rem; color: #2d7d2d;">Paid: ${formatCurrency(appointment.paid_amount)}</div>` : ''}
                        
                        ${invoiceLink}
                        ${consultationLink}
                    </div>
                </div>
                
                ${notesSection}
                
                <div class="appointment-card-footer">
                    <div class="appointment-meta">
                        Created ${formatDate(appointment.created_at)}
                        ${appointment.updated_at ? ` • Updated ${formatDate(appointment.updated_at)}` : ''}
                    </div>
                    <div class="appointment-actions">
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;
    }
    
    function renderPagination() {
        const container = document.getElementById('pagination-container');
        if (!container) return;
        
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let paginationHtml = '<div class="pagination-container">';
        
        // Previous button
        paginationHtml += `<button class="pagination-btn ${currentPage === 1 ? 'disabled' : ''}" 
                                  data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
                            <i class="fas fa-chevron-left"></i>
                          </button>`;
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            paginationHtml += `<button class="pagination-btn" data-page="1">1</button>`;
            if (startPage > 2) {
                paginationHtml += `<span style="padding: 8px;">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                                      data-page="${i}">${i}</button>`;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += `<span style="padding: 8px;">...</span>`;
            }
            paginationHtml += `<button class="pagination-btn" data-page="${totalPages}">${totalPages}</button>`;
        }
        
        // Next button
        paginationHtml += `<button class="pagination-btn ${currentPage === totalPages ? 'disabled' : ''}" 
                                  data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
                            <i class="fas fa-chevron-right"></i>
                          </button>`;
        
        paginationHtml += '</div>';
        container.innerHTML = paginationHtml;
        
        // Add click handlers
        container.querySelectorAll('.pagination-btn:not(.disabled)').forEach(btn => {
            btn.addEventListener('click', function() {
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                    currentPage = page;
                    renderAppointmentCards();
                    renderPagination();
                    updateResultsCount();
                    
                    // Scroll to top of cards
                    const appointmentsContainer = document.getElementById('appointments-container');
                    if (appointmentsContainer) {
                        appointmentsContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }
    
    function updateResultsCount() {
        const startIndex = (currentPage - 1) * itemsPerPage + 1;
        const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
        const total = filteredData.length;
        
        let countText = '';
        if (total === 0) {
            countText = 'No appointments found';
        } else if (total <= itemsPerPage) {
            countText = `${total} appointment${total !== 1 ? 's' : ''}`;
        } else {
            countText = `${startIndex}-${endIndex} of ${total} appointments`;
        }
        
        setElementText('results-count', countText);
    }
    
    // State Management Functions
    function showCardsLoading() {
        const loading = document.getElementById('cards-loading');
        const container = document.getElementById('appointments-container');
        const pagination = document.getElementById('pagination-container');
        const empty = document.getElementById('cards-empty');
        
        if (loading) loading.style.display = 'block';
        if (container) container.style.display = 'none';
        if (pagination) pagination.style.display = 'none';
        if (empty) empty.style.display = 'none';
    }
    
    function hideCardsLoading() {
        const loading = document.getElementById('cards-loading');
        const container = document.getElementById('appointments-container');
        const pagination = document.getElementById('pagination-container');
        
        if (loading) loading.style.display = 'none';
        if (container) container.style.display = 'block';
        if (pagination) pagination.style.display = 'block';
    }
    
    function showEmptyState() {
        const empty = document.getElementById('cards-empty');
        const container = document.getElementById('appointments-container');
        const pagination = document.getElementById('pagination-container');
        
        if (empty) empty.style.display = 'block';
        if (container) container.style.display = 'none';
        if (pagination) pagination.style.display = 'none';
        setElementText('results-count', 'No appointments found');
    }
    
    function hideEmptyState() {
        const empty = document.getElementById('cards-empty');
        const container = document.getElementById('appointments-container');
        const pagination = document.getElementById('pagination-container');
        
        if (empty) empty.style.display = 'none';
        if (container) container.style.display = 'block';
        if (pagination) pagination.style.display = 'block';
    }
    
    // Event Listeners Setup
    function setupEventListeners() {
        const debouncedReload = debounce(() => {
            loadAppointments();
            loadStatistics();
        }, 300);
        
        // Filter inputs
        ['date_from', 'date_to', 'staff_filter', 'status_filter', 'client_filter', 'service_filter'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', debouncedReload);
            }
        });
        
        // Sort change handler
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                currentSort = this.value;
                applyFiltersAndSort();
            });
        }
        
        // Filter buttons
        const applyBtn = document.getElementById('apply-filters');
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                loadAppointments();
                loadStatistics();
            });
        }
        
        const clearBtn = document.getElementById('clear-filters');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                // Set date filters to current month
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                
                const dateFromEl = document.getElementById('date_from');
                const dateToEl = document.getElementById('date_to');
                
                if (dateFromEl) dateFromEl.value = firstDay.toISOString().split('T')[0];
                if (dateToEl) dateToEl.value = lastDay.toISOString().split('T')[0];
                
                // Clear other filters
                ['staff_filter', 'status_filter', 'client_filter', 'service_filter'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.value = '';
                });
                
                loadAppointments();
                loadStatistics();
            });
        }
        
        // Export functionality
        const exportBtn = document.getElementById('export-appointments');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportAppointments);
        }
        
        // Client change handler
        const clientSelect = document.getElementById('client_id');
        if (clientSelect) {
            clientSelect.addEventListener('change', function() {
                const clientId = this.value;
                if (clientId) {
                    loadContactsByClient(clientId);
                } else {
                    const contactGroup = document.getElementById('contact-group');
                    const contactSelect = document.getElementById('contact_id');
                    if (contactGroup) contactGroup.style.display = 'none';
                    if (contactSelect) contactSelect.innerHTML = '<option value="">Select Contact</option>';
                }
            });
        }
        
        // Service change handler
        const serviceSelect = document.getElementById('service_id');
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                updateAppointmentSummary();
                loadAvailableTimeSlots();
            });
        }
        
        // Staff/Date change handlers
        ['staff_id', 'appointment_date'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', function() {
                    if (this.id === 'appointment_date') {
                        if (!validateAppointmentDate(this)) return;
                    }
                    loadAvailableTimeSlots();
                });
            }
        });
        
        // Reschedule date change
        const newDateInput = document.getElementById('new_date');
        if (newDateInput) {
            newDateInput.addEventListener('change', function() {
                if (!validateAppointmentDate(this)) return;
                loadRescheduleTimeSlots();
            });
        }
        
        // Form submissions
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                createAppointment();
            });
        }
        
        const rescheduleForm = document.getElementById('rescheduleForm');
        if (rescheduleForm) {
            rescheduleForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitReschedule();
            });
        }
        
        // Modal reset handlers
        const appointmentModal = document.getElementById('appointmentModal');
        if (appointmentModal) {
            appointmentModal.addEventListener('hidden.bs.modal', function() {
                resetAppointmentForm();
            });
        }
        
        const rescheduleModal = document.getElementById('rescheduleModal');
        if (rescheduleModal) {
            rescheduleModal.addEventListener('hidden.bs.modal', function() {
                resetRescheduleForm();
            });
        }
    }
    
    // Contact Loading Functions
    function loadContactsByClient(clientId) {
        if (!clientId) return;
        
        if (contactsCache.has(clientId)) {
            populateContactsDropdown(contactsCache.get(clientId));
            return;
        }
        
        const contactSelect = document.getElementById('contact_id');
        if (contactSelect) {
            contactSelect.innerHTML = '<option value="">Loading contacts...</option>';
        }
        
        API.get('cases/get_contacts_by_client/' + clientId)
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    contactsCache.set(clientId, data.data);
                    populateContactsDropdown(data.data);
                } else {
                    showEmptyContactsDropdown(data.message || 'No contacts available');
                }
            })
            .catch(error => {
                console.error('Error loading contacts:', error);
                showEmptyContactsDropdown('Error loading contacts');
            });
    }
    
    function populateContactsDropdown(contacts) {
        const select = document.getElementById('contact_id');
        const contactGroup = document.getElementById('contact-group');
        
        if (!select) return;
        
        select.innerHTML = '<option value="">Select Contact</option>';
        
        if (contacts && contacts.length > 0) {
            contacts.forEach(contact => {
                const contactName = contact.full_name || 
                    (contact.firstname + ' ' + contact.lastname).trim() || 
                    'Unnamed Contact';
                const option = document.createElement('option');
                option.value = contact.id;
                option.textContent = contactName;
                select.appendChild(option);
            });
            if (contactGroup) contactGroup.style.display = 'block';
        } else {
            showEmptyContactsDropdown('No contacts available');
        }
    }
    
    function showEmptyContactsDropdown(message = 'No contacts available') {
        const select = document.getElementById('contact_id');
        const contactGroup = document.getElementById('contact-group');
        
        if (select) {
            select.innerHTML = `<option value="">${escapeHtml(message)}</option>`;
        }
        if (contactGroup) {
            contactGroup.style.display = 'block';
        }
    }
    
    // Time Slot Functions
    function loadAvailableTimeSlots() {
        const serviceId = getElementValue('service_id');
        const staffId = getElementValue('staff_id');
        const date = getElementValue('appointment_date');
        
        const timeSelect = document.getElementById('start_time');
        if (!timeSelect) return;
        
        if (!serviceId || !staffId || !date) {
            timeSelect.innerHTML = '<option value="">Select service, staff and date first</option>';
            return;
        }
        
        const cacheKey = `${serviceId}-${staffId}-${date}`;
        if (timeSlotsCache.has(cacheKey)) {
            populateTimeSlots(timeSlotsCache.get(cacheKey), timeSelect);
            return;
        }
        
        timeSelect.innerHTML = '<option value="">Loading time slots...</option>';
        
        API.get('cases/get_available_slots', {
            service_id: serviceId,
            staff_id: staffId,
            date: date
        })
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                timeSlotsCache.set(cacheKey, data.data);
                populateTimeSlots(data.data, timeSelect);
            } else {
                timeSelect.innerHTML = '<option value="">No slots available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
        });
    }
    
    function loadRescheduleTimeSlots() {
        const appointmentId = getElementValue('reschedule_appointment_id');
        const date = getElementValue('new_date');
        const timeSelect = document.getElementById('new_time');
        
        if (!timeSelect) return;
        
        if (!appointmentId || !date) {
            timeSelect.innerHTML = '<option value="">Select date first</option>';
            return;
        }
        
        timeSelect.innerHTML = '<option value="">Loading time slots...</option>';
        
        API.get('cases/get_available_slots', {
            appointment_id: appointmentId,
            date: date
        })
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                populateTimeSlots(data.data, timeSelect);
            } else {
                timeSelect.innerHTML = '<option value="">No slots available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
        });
    }
    
    function populateTimeSlots(slots, selectElement) {
        if (!selectElement) return;
        
        selectElement.innerHTML = '<option value="">Select Time</option>';
        
        if (slots && slots.length > 0) {
            slots.forEach(slot => {
                const availability = slot.available !== false ? '' : ' (Unavailable)';
                const option = document.createElement('option');
                option.value = slot.start_time;
                option.textContent = (slot.formatted_time || slot.start_time) + availability;
                if (slot.available === false) {
                    option.disabled = true;
                }
                selectElement.appendChild(option);
            });
        } else {
            selectElement.innerHTML = '<option value="">No slots available</option>';
        }
    }
    
    // Form Functions
    function validateAppointmentDate(dateInput) {
        if (!dateInput || !dateInput.value) return false;
        
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (isNaN(selectedDate.getTime()) || selectedDate < today) {
            showAlert('warning', 'Cannot select a date in the past');
            dateInput.value = today.toISOString().split('T')[0];
            return false;
        }
        return true;
    }
    
    function updateAppointmentSummary() {
        const serviceSelect = document.getElementById('service_id');
        if (!serviceSelect) return;
        
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const serviceName = selectedOption.text.split('(')[0].trim();
            const duration = selectedOption.dataset.duration;
            const price = selectedOption.dataset.price;
            
            setElementText('summary-service', serviceName);
            setElementText('summary-duration', duration ? `${duration} minutes` : '-');
            setElementText('summary-amount', price ? formatCurrency(price) : '-');
        } else {
            setElementText('summary-service', '-');
            setElementText('summary-duration', '-');
            setElementText('summary-amount', '-');
        }
    }
    
    function createAppointment() {
        const form = document.getElementById('appointmentForm');
        if (!form) return;
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const formData = new FormData(form);
        showLoading();
        
        API.post('cases/create_appointment', formData)
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    // Close modal
                    hideModal('appointmentModal');
                    resetAppointmentForm();
                    
                    loadAppointments();
                    loadStatistics();
                    showAlert('success', 'Appointment created successfully');
                    
                    if (data.invoice_id) {
                        showAlert('info', 'Invoice generated automatically for this appointment');
                    }
                    
                    // Clear cache
                    const clientId = formData.get('client_id');
                    if (clientId) {
                        contactsCache.delete(clientId);
                    }
                    timeSlotsCache.clear();
                } else {
                    showAlert('danger', data.message || 'Failed to create appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred. Please try again.');
            });
    }
    
    function submitReschedule() {
        const form = document.getElementById('rescheduleForm');
        if (!form) return;
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const formData = new FormData(form);
        showLoading();
        
        API.post('cases/reschedule_appointment', formData)
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    hideModal('rescheduleModal');
                    resetRescheduleForm();
                    
                    loadAppointments();
                    loadStatistics();
                    showAlert('success', 'Appointment rescheduled successfully');
                    timeSlotsCache.clear();
                } else {
                    showAlert('danger', data.message || 'Failed to reschedule appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred');
            });
    }
    
    function resetAppointmentForm() {
        const form = document.getElementById('appointmentForm');
        if (form) {
            form.reset();
        }
        
        const contactGroup = document.getElementById('contact-group');
        const contactSelect = document.getElementById('contact_id');
        const startTimeSelect = document.getElementById('start_time');
        
        if (contactGroup) contactGroup.style.display = 'none';
        if (contactSelect) contactSelect.innerHTML = '<option value="">Select Contact</option>';
        if (startTimeSelect) startTimeSelect.innerHTML = '<option value="">Select Date First</option>';
        
        updateAppointmentSummary();
    }
    
    function resetRescheduleForm() {
        const form = document.getElementById('rescheduleForm');
        if (form) {
            form.reset();
        }
        
        const newTimeSelect = document.getElementById('new_time');
        if (newTimeSelect) {
            newTimeSelect.innerHTML = '<option value="">Select Date First</option>';
        }
    }
    
    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Try Bootstrap 5 first
            if (window.bootstrap && window.bootstrap.Modal) {
                const modalInstance = window.bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            // Try Bootstrap 4/jQuery
            else if (window.$ && window.$.fn.modal) {
                window.$(modal).modal('hide');
            }
            // Fallback - remove modal backdrop and hide modal
            else {
                modal.style.display = 'none';
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.paddingRight = '';
            }
        }
    }
    
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Try Bootstrap 5 first
            if (window.bootstrap && window.bootstrap.Modal) {
                new window.bootstrap.Modal(modal).show();
            }
            // Try Bootstrap 4/jQuery
            else if (window.$ && window.$.fn.modal) {
                window.$(modal).modal('show');
            }
            // Fallback - basic modal display
            else {
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }
    }
    
    function exportAppointments() {
        const filters = {
            date_from: getElementValue('date_from'),
            date_to: getElementValue('date_to'),
            staff_id: getElementValue('staff_filter'),
            status: getElementValue('status_filter'),
            client_id: getElementValue('client_filter'),
            service_id: getElementValue('service_filter')
        };
        
        const queryString = new URLSearchParams(filters).toString();
        window.open(adminUrl + 'cases/export_appointments?' + queryString, '_blank');
    }
    
    // Appointment Manager Global Object
    window.appointmentManager = {
        view: function(appointmentId) {
            if (!appointmentId) return;
            
            showLoading();
            
            API.get('cases/get_appointment/' + appointmentId)
                .then(data => {
                    hideLoading();
                    
                    if (data.success && data.data) {
                        displayAppointmentDetails(data.data);
                        showModal('viewAppointmentModal');
                    } else {
                        showAlert('danger', data.message || 'Failed to load appointment details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to load appointment details');
                });
        },
        
        complete: function(appointmentId) {
            if (!appointmentId || !confirm('Mark this appointment as completed?')) return;
            
            showLoading();
            
            API.post('cases/complete_appointment/' + appointmentId, {
                create_consultation: '1'
            })
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    loadAppointments();
                    loadStatistics();
                    showAlert('success', 'Appointment completed successfully');
                    
                    if (data.consultation_id) {
                        showAlert('info', 'Consultation record created automatically');
                    }
                } else {
                    showAlert('danger', data.message || 'Failed to complete appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred');
            });
        },
        
        cancel: function(appointmentId) {
            if (!appointmentId) return;
            
            const reason = prompt('Please enter cancellation reason (optional):');
            if (reason === null) return;
            
            showLoading();
            
            API.post('cases/cancel_appointment/' + appointmentId, {
                reason: reason || ''
            })
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    loadAppointments();
                    loadStatistics();
                    showAlert('success', 'Appointment cancelled successfully');
                } else {
                    showAlert('danger', data.message || 'Failed to cancel appointment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred');
            });
        },
        
        reschedule: function(appointmentId) {
            if (!appointmentId) return;
            
            API.get('cases/get_appointment/' + appointmentId)
                .then(data => {
                    if (data.success && data.data) {
                        const rescheduleIdInput = document.getElementById('reschedule_appointment_id');
                        const newDateInput = document.getElementById('new_date');
                        
                        if (rescheduleIdInput) {
                            rescheduleIdInput.value = appointmentId;
                        }
                        if (newDateInput) {
                            newDateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
                        }
                        
                        showModal('rescheduleModal');
                    } else {
                        showAlert('danger', 'Failed to load appointment for rescheduling');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to load appointment details');
                });
        },
        
        convert: function(appointmentId) {
            if (!appointmentId || !confirm('Convert this appointment to a consultation record?')) return;
            
            showLoading();
            
            API.post('cases/convert_to_consultation/' + appointmentId, {})
                .then(data => {
                    hideLoading();
                    
                    if (data.success) {
                        loadAppointments();
                        loadStatistics();
                        showAlert('success', 'Appointment converted to consultation successfully');
                        
                        if (data.consultation_id) {
                            showAlert('info', `Consultation ID: ${data.consultation_id}`);
                        }
                    } else {
                        showAlert('danger', data.message || 'Failed to convert appointment');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showAlert('danger', 'Network error occurred');
                });
        },
        
        generateInvoice: function(appointmentId) {
            if (!appointmentId || !confirm('Generate invoice for this appointment?')) return;
            
            showLoading();
            
            API.post('cases/generate_appointment_invoice/' + appointmentId, {})
                .then(data => {
                    hideLoading();
                    
                    if (data.success) {
                        loadAppointments();
                        showAlert('success', 'Invoice generated successfully');
                        
                        if (data.invoice_id) {
                            showAlert('info', `Invoice ID: ${data.invoice_id}`);
                        }
                    } else {
                        showAlert('danger', data.message || 'Failed to generate invoice');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showAlert('danger', 'Network error occurred');
                });
        }
    };
    
    function displayAppointmentDetails(appointment) {
        if (!appointment) return;
        
        const detailsHtml = `
            <div class="appointment-details">
                <div class="row">
                    <div class="col-md-6">
                        <h5 style="margin-bottom: 20px; color: #1a1a1a; font-weight: 600;">Client Information</h5>
                        <table class="table table-borderless" style="margin-bottom: 30px;">
                            <tr>
                                <td style="width: 40%; font-weight: 500; color: #666666;">Client:</td>
                                <td style="color: #1a1a1a;">${escapeHtml(appointment.client_name || 'N/A')}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Contact:</td>
                                <td style="color: #1a1a1a;">${escapeHtml(appointment.contact_name || 'N/A')}</td>
                            </tr>
                        </table>
                        
                        <h5 style="margin-bottom: 20px; color: #1a1a1a; font-weight: 600;">Service Details</h5>
                        <table class="table table-borderless" style="margin-bottom: 30px;">
                            <tr>
                                <td style="width: 40%; font-weight: 500; color: #666666;">Service:</td>
                                <td style="color: #1a1a1a;">${escapeHtml(appointment.service_name || 'N/A')}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Duration:</td>
                                <td style="color: #1a1a1a;">${appointment.duration_minutes ? appointment.duration_minutes + ' minutes' : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Staff:</td>
                                <td style="color: #1a1a1a;">${escapeHtml(appointment.staff_full_name || 'N/A')}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 style="margin-bottom: 20px; color: #1a1a1a; font-weight: 600;">Appointment Schedule</h5>
                        <table class="table table-borderless" style="margin-bottom: 30px;">
                            <tr>
                                <td style="width: 40%; font-weight: 500; color: #666666;">Date:</td>
                                <td style="color: #1a1a1a;">${formatDate(appointment.appointment_date)}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Time:</td>
                                <td style="color: #1a1a1a;">${formatTime(appointment.start_time)} - ${formatTime(appointment.end_time)}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Status:</td>
                                <td>
                                    <span class="status-badge status-${appointment.status}">
                                        ${appointment.status ? appointment.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown'}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                        <h5 style="margin-bottom: 20px; color: #1a1a1a; font-weight: 600;">Payment Information</h5>
                        <table class="table table-borderless" style="margin-bottom: 30px;">
                            <tr>
                                <td style="width: 40%; font-weight: 500; color: #666666;">Amount:</td>
                                <td style="color: #1a1a1a; font-weight: 600;">${formatCurrency(appointment.total_amount)}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Paid Amount:</td>
                                <td style="color: #1a1a1a;">${formatCurrency(appointment.paid_amount || 0)}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; color: #666666;">Payment Status:</td>
                                <td>
                                    <span class="status-badge payment-status-${appointment.payment_status}">
                                        ${appointment.payment_status ? appointment.payment_status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown'}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                ${appointment.notes ? `
                <div class="row">
                    <div class="col-12">
                        <h5 style="margin-bottom: 15px; color: #1a1a1a; font-weight: 600;">Notes</h5>
                        <div style="background: #f8f8f8; padding: 20px; border: 1px solid #e1e1e1; border-radius: 2px; line-height: 1.6;">
                            ${escapeHtml(appointment.notes).replace(/\n/g, '<br>')}
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        
        setElementHTML('appointment-details-content', detailsHtml);
    }
    
    // Auto-refresh every 5 minutes
    setInterval(function() {
        if (appointmentsData.length > 0) {
            loadAppointments();
        }
    }, 300000);
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            showModal('appointmentModal');
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            loadAppointments();
            loadStatistics();
        }
    });
    
    
    // Initialize everything after API is defined
    function initializeApp() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        const appointmentDateInput = document.getElementById('appointment_date');
        const newDateInput = document.getElementById('new_date');
        
        if (appointmentDateInput) appointmentDateInput.setAttribute('min', today);
        if (newDateInput) newDateInput.setAttribute('min', today);
        
        // Setup event listeners
        setupEventListeners();
        
        // Load initial data
        setTimeout(() => {
            loadAppointments();
            loadStatistics();
        }, 100);
    }
    
    // Call initialization
    initializeApp();
});
</script>


<?php init_tail(); ?>