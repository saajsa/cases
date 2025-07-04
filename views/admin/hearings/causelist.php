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

.page-actions .btn-success {
    background: #ffffff;
    border-color: #2d7d2d;
    color: #2d7d2d;
}

.page-actions .btn-success:hover {
    background: #2d7d2d;
    color: #ffffff;
}

.page-actions .btn-info {
    background: #ffffff;
    border-color: #1a6bcc;
    color: #1a6bcc;
}

.page-actions .btn-info:hover {
    background: #1a6bcc;
    color: #ffffff;
}

.date-navigation {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.date-navigation-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 1px solid #e1e1e1;
}

.date-controls {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 20px;
    margin-bottom: 20px;
    align-items: end;
}

.date-input-group {
    display: flex;
    gap: 12px;
    align-items: end;
}

.date-input {
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.date-input:focus {
    outline: none;
    border-color: #1a1a1a;
}

.date-nav-buttons {
    display: flex;
    gap: 8px;
}

.date-nav-btn {
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    background: #ffffff;
    color: #2c2c2c;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.date-nav-btn:hover {
    background: #f8f8f8;
    border-color: #999999;
}

.date-nav-btn.active {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.date-shortcuts {
    margin-top: 20px;
}

.date-shortcuts label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.date-shortcuts select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.date-shortcuts select:focus {
    outline: none;
    border-color: #1a1a1a;
}

.court-section {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    overflow: hidden;
}

.court-header {
    background: #1a1a1a;
    color: #ffffff;
    padding: 25px 30px;
    text-align: center;
}

.court-header h2 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.judge-section {
    border-bottom: 1px solid #f0f0f0;
}

.judge-section:last-child {
    border-bottom: none;
}

.judge-header {
    background: #f8f8f8;
    padding: 20px 30px;
    border-bottom: 1px solid #e1e1e1;
}

.judge-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 5px 0;
}

.court-info {
    font-size: 0.875rem;
    color: #666666;
    margin: 0;
}

.causelist-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.causelist-table thead th {
    background: #f8f8f8;
    color: #1a1a1a;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 15px 20px;
    border-bottom: 1px solid #e1e1e1;
    border-right: 1px solid #f0f0f0;
    text-align: center;
}

.causelist-table thead th:first-child {
    text-align: center;
    width: 60px;
}

.causelist-table thead th:last-child {
    border-right: none;
}

.causelist-table tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid #f5f5f5;
    border-right: 1px solid #f8f8f8;
    vertical-align: top;
    color: #2c2c2c;
    font-size: 0.875rem;
    line-height: 1.4;
}

.causelist-table tbody td:first-child {
    text-align: center;
    font-weight: 600;
    color: #1a1a1a;
}

.causelist-table tbody td:last-child {
    border-right: none;
}

.causelist-table tbody tr:hover {
    background: #fafafa;
}

.causelist-table tbody tr:last-child td {
    border-bottom: none;
}

.case-number {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 2px;
}

.case-name {
    color: #2c2c2c;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.3px;
    line-height: 1.3;
}

.hearing-time {
    font-weight: 600;
    color: #1a1a1a;
    font-size: 0.9rem;
}

.hearing-purpose {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.hearing-description {
    font-size: 0.8rem;
    color: #666666;
    margin-bottom: 8px;
    line-height: 1.3;
}

.follow-up-indicator {
    font-size: 0.75rem;
    color: #1a6bcc;
    font-style: italic;
}

/* Status Colors */
.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 1px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid;
    float: right;
    margin-top: 5px;
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

.empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #999999;
    background: #fafafa;
    margin: 30px;
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

/* Print Styles */
.print-header {
    display: none;
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #1a1a1a;
}

.print-header h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.print-header h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.print-header h4 {
    font-size: 1rem;
    font-weight: 500;
    color: #666666;
    margin: 0;
}

/* Debug Panel */
.debug-panel {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 20px;
    margin-top: 30px;
    border-radius: 2px;
    border-left: 3px solid #cc8c1a;
}

.debug-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #cc8c1a;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.debug-content {
    font-size: 0.8rem;
    color: #666666;
    font-family: 'Courier New', monospace;
}

.debug-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 0.75rem;
}

.debug-table th,
.debug-table td {
    padding: 8px 12px;
    border: 1px solid #e1e1e1;
    text-align: left;
}

.debug-table th {
    background: #f8f8f8;
    font-weight: 600;
}

.debug-table .highlight {
    background: #f0f9f0;
    font-weight: 600;
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
    
    .date-navigation {
        padding: 20px;
    }
    
    .date-controls {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .date-input-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .date-nav-buttons {
        justify-content: center;
    }
    
    .court-header {
        padding: 20px;
    }
    
    .court-header h2 {
        font-size: 1.1rem;
    }
    
    .judge-header {
        padding: 15px 20px;
    }
    
    .causelist-table {
        font-size: 0.8rem;
    }
    
    .causelist-table thead th,
    .causelist-table tbody td {
        padding: 12px 8px;
    }
    
    .status-badge {
        float: none;
        display: block;
        margin-top: 8px;
        text-align: center;
    }
}

@media print {
    body {
        background: #ffffff;
    }
    
    .page-actions,
    .date-navigation,
    .debug-panel {
        display: none !important;
    }
    
    .print-header {
        display: block !important;
    }
    
    .court-section {
        page-break-inside: avoid;
        border: 2px solid #000000;
        margin-bottom: 20px;
    }
    
    .court-header {
        background: #f0f0f0 !important;
        color: #000000 !important;
        border-bottom: 2px solid #000000;
    }
    
    .judge-header {
        background: #f8f8f8 !important;
        border-bottom: 1px solid #000000;
    }
    
    .causelist-table thead th {
        background: #f0f0f0 !important;
        border: 1px solid #000000;
    }
    
    .causelist-table tbody td {
        border: 1px solid #000000;
    }
    
    .status-badge {
        border: 1px solid #000000 !important;
        background: none !important;
        color: #000000 !important;
    }
}
</style>

<div id="wrapper">
  <div class="content">
    <!-- Minimalist Page Header -->
    <div class="page-header">
      <div class="row">
        <div class="col-md-7">
          <h1>Court Cause List</h1>
          <div class="subtitle">
            <?php echo date('l, F d, Y', strtotime($date)); ?> - 
            Showing cases with hearings on this date
          </div>
        </div>
        <div class="col-md-5">
          <div class="page-actions text-right">
            <a href="<?php echo admin_url('cases/hearings/add'); ?>" class="btn btn-primary">
              Add Hearing
            </a>
            <a href="<?php echo admin_url('cases/hearings'); ?>" class="btn btn-info">
              All Hearings
            </a>
            <button class="btn btn-success" id="print-list">
              Print List
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Date Navigation Panel -->
    <div class="date-navigation">
      <h3 class="date-navigation-title">Date Navigation</h3>
      
      <div class="date-controls">
        <div class="date-input-group">
          <input type="date" id="date-selector" class="date-input" value="<?php echo $date; ?>">
          <button class="date-nav-btn" id="go-to-date">Go to Date</button>
        </div>
        
        <div class="date-nav-buttons">
          <button class="date-nav-btn" id="prev-day" title="Previous Day">
            ← Previous
          </button>
          <button class="date-nav-btn active" id="today" title="Today">
            Today
          </button>
          <button class="date-nav-btn" id="next-day" title="Next Day">
            Next →
          </button>
        </div>
      </div>
      
      <div class="date-shortcuts">
        <label for="quick-dates">Scheduled Hearing Dates:</label>
        <select id="quick-dates" class="date-shortcuts-select">
          <option value="">Select a Date</option>
          <?php if (!empty($upcoming_dates)): ?>
            <?php foreach ($upcoming_dates as $date_row): ?>
              <?php 
                $hearing_date = isset($date_row['hearing_date']) ? $date_row['hearing_date'] : $date_row['date'];
                $formatted_date = date('D, d M Y', strtotime($hearing_date));
                $is_selected = ($date == $hearing_date) ? 'selected' : '';
              ?>
              <option value="<?php echo $hearing_date; ?>" <?php echo $is_selected; ?>>
                <?php echo $formatted_date; ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
    </div>
    
    <!-- Print Header (hidden by default) -->
    <div id="print-header" class="print-header">
      <h2><?php echo get_option('company_name'); ?></h2>
      <h3>COURT CAUSE LIST</h3>
      <h4><?php echo date('d.m.Y', strtotime($date)); ?></h4>
    </div>
    
    <!-- Court Sections -->
    <?php
    // Group hearings by court/judge
    $courts = [];
    if (!empty($hearings)) {
      foreach ($hearings as $hearing) {
        $court_key = !empty($hearing['court_name']) ? $hearing['court_name'] : 'Other Courts';
        $judge_key = !empty($hearing['judge_name']) ? $hearing['judge_name'] : 'Not Assigned';
        
        if (!isset($courts[$court_key])) {
          $courts[$court_key] = [];
        }
        
        if (!isset($courts[$court_key][$judge_key])) {
          $courts[$court_key][$judge_key] = [];
        }
        
        $courts[$court_key][$judge_key][] = $hearing;
      }
    }
    
    // Counter for serial numbers
    $sr_no = 1;
    ?>
    
    <?php if (!empty($courts)): ?>
      <?php foreach ($courts as $court_name => $judges): ?>
        <div class="court-section">
          <div class="court-header">
            <h2><?php echo strtoupper($court_name); ?> - CAUSE LIST <?php echo date('d.m.Y', strtotime($date)); ?></h2>
          </div>
          
          <?php foreach ($judges as $judge_name => $judge_hearings): ?>
            <div class="judge-section">
              <div class="judge-header">
                <h4 class="judge-name">HON'BLE <?php echo strtoupper($judge_name); ?></h4>
                <?php if (!empty($judge_hearings) && isset($judge_hearings[0]['court_no'])): ?>
                  <p class="court-info">(Court No. <?php echo $judge_hearings[0]['court_no']; ?>)</p>
                <?php endif; ?>
              </div>
              
              <table class="causelist-table">
                <thead>
                  <tr>
                    <th>Sr.</th>
                    <th>Case No.</th>
                    <th>Case Name</th>
                    <th>Time</th>
                    <th>Purpose/Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($judge_hearings)): ?>
                    <tr>
                      <td colspan="5" style="text-align: center; color: #999999; padding: 40px;">
                        No hearings scheduled
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($judge_hearings as $hearing): ?>
                      <tr>
                        <td><?php echo $sr_no++; ?></td>
                        <td>
                          <div class="case-number"><?php echo htmlspecialchars($hearing['case_number']); ?></div>
                        </td>
                        <td>
                          <div class="case-name">
                            <?php 
                            // Format case title in VS format if client info available
                            $case_name = $hearing['case_title'];
                            if (!empty($hearing['client_name'])) {
                              $opponent = strstr($hearing['case_title'], 'vs') ?: 
                                        strstr($hearing['case_title'], 'VS') ?: 
                                        strstr($hearing['case_title'], 'V/S');
                              
                              if ($opponent) {
                                $case_name = strtoupper($hearing['client_name'] . ' ' . $opponent);
                              } else {
                                $case_name = strtoupper($hearing['client_name'] . ' V/S ' . $hearing['case_title']);
                              }
                            }
                            echo htmlspecialchars($case_name);
                            ?>
                          </div>
                        </td>
                        <td>
                          <div class="hearing-time"><?php echo date('h:i A', strtotime($hearing['time'])); ?></div>
                        </td>
                        <td>
                          <div class="hearing-purpose">
                            <?php 
                            if (isset($hearing['hearing_purpose']) && !empty($hearing['hearing_purpose'])): ?>
                              <?php echo htmlspecialchars($hearing['hearing_purpose']); ?>
                            <?php else: ?>
                              <?php echo htmlspecialchars($hearing['status'] ?: 'Scheduled'); ?>
                            <?php endif; ?>
                          </div>
                          
                          <?php if (!empty($hearing['description'])): ?>
                            <div class="hearing-description">
                              <?php echo htmlspecialchars(strlen($hearing['description']) > 100 ? 
                                  substr($hearing['description'], 0, 100) . '...' : 
                                  $hearing['description']); ?>
                            </div>
                          <?php endif; ?>
                          
                          <?php if (!empty($hearing['parent_hearing_id'])): ?>
                            <div class="follow-up-indicator">
                              → Follow-up from previous hearing
                            </div>
                          <?php endif; ?>
                          
                          <?php
                          $statusClass = 'status-scheduled';
                          switch(strtolower($hearing['status'])) {
                            case 'scheduled': $statusClass = 'status-scheduled'; break;
                            case 'adjourned': $statusClass = 'status-adjourned'; break;
                            case 'completed': $statusClass = 'status-completed'; break;
                            case 'cancelled': $statusClass = 'status-cancelled'; break;
                            default: $statusClass = 'status-scheduled';
                          }
                          ?>
                          <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($hearing['status']); ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="court-section">
        <div class="empty-state">
          <i class="fas fa-calendar-times"></i>
          <h5>No Hearings Scheduled</h5>
          <p>No hearings found for <?php echo date('F d, Y', strtotime($date)); ?></p>
          <a href="<?php echo admin_url('cases/hearings/add'); ?>" class="btn btn-primary">
            Schedule Hearing
          </a>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Debug Information (visible in development only) -->
    <?php if (ENVIRONMENT == 'development' || isset($_GET['debug'])): ?>
    <div class="debug-panel">
      <div class="debug-title">Debug Information</div>
      <div class="debug-content">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Date Requested:</strong> <?php echo isset($debug['date_requested']) ? $debug['date_requested'] : $date; ?></p>
            <p><strong>Formatted Date:</strong> <?php echo isset($debug['formatted_date']) ? $debug['formatted_date'] : date('Y-m-d', strtotime($date)); ?></p>
            <p><strong>Results Count:</strong> <?php echo isset($debug['results_count']) ? $debug['results_count'] : count($hearings); ?></p>
          </div>
          <div class="col-md-6">
            <p><strong>Server Date/Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Environment:</strong> <?php echo ENVIRONMENT; ?></p>
          </div>
        </div>
        
        <?php if (isset($debug['query'])): ?>
          <h6 style="margin-top: 15px; color: #1a1a1a;">SQL Query:</h6>
          <pre style="background: #f8f8f8; padding: 10px; border: 1px solid #e1e1e1; font-size: 0.75rem;"><?php echo htmlspecialchars($debug['query']); ?></pre>
          <p><strong>Parameters:</strong> <?php echo htmlspecialchars(print_r($debug['params'], true)); ?></p>
        <?php endif; ?>
        
        <?php if (isset($debug['raw_hearings']) && !empty($debug['raw_hearings'])): ?>
          <h6 style="margin-top: 15px; color: #1a1a1a;">All Hearings in Database:</h6>
          <table class="debug-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Case ID</th>
                <th>Date</th>
                <th>Next Date</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($debug['raw_hearings'] as $raw): ?>
                <tr <?php echo (isset($raw['next_date']) && date('Y-m-d', strtotime($raw['next_date'])) == date('Y-m-d', strtotime($date))) ? 'class="highlight"' : ''; ?>>
                  <td><?php echo $raw['id']; ?></td>
                  <td><?php echo $raw['case_id']; ?></td>
                  <td><?php echo $raw['date']; ?></td>
                  <td><?php echo isset($raw['next_date']) ? $raw['next_date'] : 'N/A'; ?></td>
                  <td><?php echo $raw['time']; ?></td>
                  <td><?php echo $raw['status']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    
  </div>
</div>

<script>
// Complete JavaScript for Causelist functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Causelist initialized');
    
    // Get admin_url from PHP (make sure this is available)
    const admin_url = '<?php echo admin_url(); ?>';
    const current_date = '<?php echo $date; ?>';
    
    // Date navigation functions
    function goToDate(dateStr) {
        console.log('Navigating to date:', dateStr);
        window.location.href = admin_url + 'cases/hearings/causelist?date=' + dateStr;
    }
    
    // Helper function to format date as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
    
    // Quick date dropdown change handler
    const quickDatesSelect = document.getElementById('quick-dates');
    if (quickDatesSelect) {
        quickDatesSelect.addEventListener('change', function() {
            const selectedDate = this.value;
            console.log('Quick date selected:', selectedDate);
            if (selectedDate) {
                goToDate(selectedDate);
            }
        });
    }
    
    // Date selector and go-to-date button
    const dateSelector = document.getElementById('date-selector');
    const goToDateBtn = document.getElementById('go-to-date');
    
    if (goToDateBtn && dateSelector) {
        goToDateBtn.addEventListener('click', function() {
            const selectedDate = dateSelector.value;
            console.log('Go to date clicked:', selectedDate);
            if (selectedDate) {
                // Add loading state
                this.textContent = 'Loading...';
                this.disabled = true;
                goToDate(selectedDate);
            }
        });
        
        // Enter key on date input
        dateSelector.addEventListener('keypress', function(e) {
            if (e.which === 13 || e.keyCode === 13) {
                const selectedDate = this.value;
                console.log('Enter key pressed on date:', selectedDate);
                if (selectedDate) {
                    goToDate(selectedDate);
                }
                e.preventDefault();
            }
        });
    }
    
    // Previous day button
    const prevDayBtn = document.getElementById('prev-day');
    if (prevDayBtn) {
        prevDayBtn.addEventListener('click', function() {
            console.log('Previous day clicked');
            const currentDate = new Date(current_date);
            currentDate.setDate(currentDate.getDate() - 1);
            const newDate = formatDate(currentDate);
            console.log('Previous date:', newDate);
            
            // Add loading state
            this.textContent = 'Loading...';
            this.disabled = true;
            goToDate(newDate);
        });
    }
    
    // Next day button
    const nextDayBtn = document.getElementById('next-day');
    if (nextDayBtn) {
        nextDayBtn.addEventListener('click', function() {
            console.log('Next day clicked');
            const currentDate = new Date(current_date);
            currentDate.setDate(currentDate.getDate() + 1);
            const newDate = formatDate(currentDate);
            console.log('Next date:', newDate);
            
            // Add loading state
            this.textContent = 'Loading...';
            this.disabled = true;
            goToDate(newDate);
        });
    }
    
    // Today button
    const todayBtn = document.getElementById('today');
    if (todayBtn) {
        todayBtn.addEventListener('click', function() {
            console.log('Today clicked');
            const today = formatDate(new Date());
            console.log('Today date:', today);
            
            // Add loading state
            this.textContent = 'Loading...';
            this.disabled = true;
            goToDate(today);
        });
        
        // Check if current date is today and update button state
        const today = formatDate(new Date());
        if (current_date === today) {
            todayBtn.classList.add('active');
        } else {
            todayBtn.classList.remove('active');
        }
    }
    
    // FIXED PRINT BUTTON FUNCTIONALITY
    const printBtn = document.getElementById('print-list');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            console.log('Print button clicked');
            
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            
            // Get the content to print
            const printHeader = document.getElementById('print-header');
            const courtSections = document.querySelectorAll('.court-section');
            
            // Build the print content
            let printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Court Cause List - ${new Date().toLocaleDateString()}</title>
                    <style>
                        /* Print-specific styles */
                        * {
                            box-sizing: border-box;
                            margin: 0;
                            padding: 0;
                        }
                        
                        body {
                            font-family: 'Arial', sans-serif;
                            background: #ffffff;
                            color: #000000;
                            font-size: 12px;
                            line-height: 1.4;
                            padding: 20px;
                        }
                        
                        .print-header {
                            text-align: center;
                            margin-bottom: 30px;
                            padding-bottom: 20px;
                            border-bottom: 2px solid #000000;
                        }
                        
                        .print-header h2 {
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        
                        .print-header h3 {
                            font-size: 16px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        
                        .print-header h4 {
                            font-size: 14px;
                            font-weight: normal;
                        }
                        
                        .court-section {
                            page-break-inside: avoid;
                            border: 2px solid #000000;
                            margin-bottom: 20px;
                            background: #ffffff;
                        }
                        
                        .court-header {
                            background: #f0f0f0;
                            color: #000000;
                            padding: 15px;
                            text-align: center;
                            border-bottom: 2px solid #000000;
                            font-weight: bold;
                            font-size: 14px;
                        }
                        
                        .judge-section {
                            border-bottom: 1px solid #cccccc;
                        }
                        
                        .judge-section:last-child {
                            border-bottom: none;
                        }
                        
                        .judge-header {
                            background: #f8f8f8;
                            padding: 12px 15px;
                            border-bottom: 1px solid #000000;
                        }
                        
                        .judge-name {
                            font-size: 13px;
                            font-weight: bold;
                            margin-bottom: 3px;
                        }
                        
                        .court-info {
                            font-size: 11px;
                            color: #666666;
                        }
                        
                        .causelist-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 0;
                        }
                        
                        .causelist-table th {
                            background: #f0f0f0;
                            border: 1px solid #000000;
                            padding: 8px 6px;
                            text-align: center;
                            font-weight: bold;
                            font-size: 11px;
                            text-transform: uppercase;
                        }
                        
                        .causelist-table td {
                            border: 1px solid #000000;
                            padding: 8px 6px;
                            vertical-align: top;
                            font-size: 11px;
                            line-height: 1.3;
                        }
                        
                        .causelist-table td:first-child {
                            text-align: center;
                            font-weight: bold;
                            width: 40px;
                        }
                        
                        .case-number {
                            font-weight: bold;
                            margin-bottom: 2px;
                        }
                        
                        .case-name {
                            font-weight: normal;
                            text-transform: uppercase;
                            font-size: 10px;
                            line-height: 1.2;
                        }
                        
                        .hearing-time {
                            font-weight: bold;
                            font-size: 12px;
                        }
                        
                        .hearing-purpose {
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        
                        .hearing-description {
                            font-size: 10px;
                            color: #666666;
                            margin-bottom: 5px;
                            line-height: 1.2;
                        }
                        
                        .follow-up-indicator {
                            font-size: 9px;
                            color: #666666;
                            font-style: italic;
                        }
                        
                        .status-badge {
                            display: inline-block;
                            padding: 2px 6px;
                            border: 1px solid #000000;
                            background: none;
                            color: #000000;
                            font-size: 9px;
                            font-weight: bold;
                            text-transform: uppercase;
                            float: right;
                            margin-top: 3px;
                        }
                        
                        .empty-state {
                            text-align: center;
                            padding: 40px 20px;
                            color: #666666;
                        }
                        
                        /* Ensure proper page breaking */
                        @page {
                            margin: 1inch;
                            size: A4;
                        }
                        
                        @media print {
                            body {
                                padding: 0;
                            }
                            
                            .court-section {
                                page-break-inside: avoid;
                                margin-bottom: 15px;
                            }
                            
                            .judge-section {
                                page-break-inside: avoid;
                            }
                        }
                    </style>
                </head>
                <body>
            `;
            
            // Add print header
            if (printHeader) {
                printContent += `<div class="print-header">${printHeader.innerHTML}</div>`;
            }
            
            // Add court sections
            courtSections.forEach(function(section) {
                printContent += `<div class="court-section">${section.innerHTML}</div>`;
            });
            
            printContent += `
                </body>
                </html>
            `;
            
            // Write content to print window
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Wait for content to load, then print
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                    // Close the print window after printing
                    setTimeout(function() {
                        printWindow.close();
                    }, 1000);
                }, 500);
            };
        });
    }
    
    // Table row hover effects
    const tableRows = document.querySelectorAll('.causelist-table tbody tr');
    tableRows.forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#fafafa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Court section animations
    const courtSections = document.querySelectorAll('.court-section');
    courtSections.forEach(function(section, index) {
        // Add entrance animation with delay
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        setTimeout(function() {
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Only if no input is focused
        if (document.activeElement.tagName !== 'INPUT' && 
            document.activeElement.tagName !== 'SELECT' && 
            document.activeElement.tagName !== 'TEXTAREA') {
            
            switch(e.key) {
                case 'ArrowLeft':
                    console.log('Left arrow key pressed');
                    if (prevDayBtn) {
                        prevDayBtn.click();
                    }
                    e.preventDefault();
                    break;
                    
                case 'ArrowRight':
                    console.log('Right arrow key pressed');
                    if (nextDayBtn) {
                        nextDayBtn.click();
                    }
                    e.preventDefault();
                    break;
                    
                case 't':
                case 'T':
                    console.log('T key pressed for today');
                    if (todayBtn) {
                        todayBtn.click();
                    }
                    e.preventDefault();
                    break;
                    
                case 'p':
                case 'P':
                    if (e.ctrlKey || e.metaKey) {
                        console.log('Ctrl+P pressed for print');
                        if (printBtn) {
                            printBtn.click();
                            e.preventDefault();
                        }
                    }
                    break;
            }
        }
    });
    
    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        if (Math.abs(swipeDistance) > swipeThreshold) {
            if (swipeDistance > 0) {
                // Swipe right - previous day
                console.log('Swipe right detected');
                if (prevDayBtn) {
                    prevDayBtn.click();
                }
            } else {
                // Swipe left - next day
                console.log('Swipe left detected');
                if (nextDayBtn) {
                    nextDayBtn.click();
                }
            }
        }
    }
    
    // Auto-refresh functionality for today's list
    let autoRefreshInterval;
    
    function startAutoRefresh() {
        const today = formatDate(new Date());
        
        // Only auto-refresh if viewing today's cause list
        if (current_date === today) {
            console.log('Starting auto-refresh for today\'s cause list');
            autoRefreshInterval = setInterval(function() {
                // Only refresh if page is visible and no user interaction
                if (!document.hidden && !document.activeElement.matches('input, select, textarea')) {
                    console.log('Auto-refreshing cause list...');
                    location.reload();
                }
            }, 30 * 60 * 1000); // 30 minutes
        }
    }
    
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            console.log('Stopping auto-refresh');
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
        }
    });
    
    // Current time highlighting for today's hearings
    function highlightCurrentTimeSlot() {
        const today = formatDate(new Date());
        
        if (current_date === today) {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            const currentTimeMinutes = currentHour * 60 + currentMinute;
            
            const hearingTimes = document.querySelectorAll('.hearing-time');
            hearingTimes.forEach(function(timeElement) {
                const timeText = timeElement.textContent.trim();
                const timeMatch = timeText.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
                
                if (timeMatch) {
                    let hours = parseInt(timeMatch[1]);
                    const minutes = parseInt(timeMatch[2]);
                    const ampm = timeMatch[3].toUpperCase();
                    
                    // Convert to 24-hour format
                    if (ampm === 'PM' && hours !== 12) {
                        hours += 12;
                    } else if (ampm === 'AM' && hours === 12) {
                        hours = 0;
                    }
                    
                    const hearingTimeMinutes = hours * 60 + minutes;
                    const timeDiff = hearingTimeMinutes - currentTimeMinutes;
                    
                    // Highlight if hearing is within next 30 minutes
                    if (timeDiff >= -15 && timeDiff <= 30) {
                        const row = timeElement.closest('tr');
                        if (row) {
                            row.style.backgroundColor = '#fff8e6';
                            row.style.borderLeft = '3px solid #cc8c1a';
                            row.style.boxShadow = '0 2px 4px rgba(204, 140, 26, 0.1)';
                        }
                    }
                }
            });
        }
    }
    
    // Highlight current time slots
    highlightCurrentTimeSlot();
    
    // Update highlighting every minute
    setInterval(highlightCurrentTimeSlot, 60000);
    
    // Loading overlay function
    function showLoadingOverlay() {
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
            font-family: Inter, sans-serif;
        `;
        
        overlay.innerHTML = `
            <div style="text-align: center; color: #666666;">
                <div style="width: 40px; height: 40px; border: 3px solid #e1e1e1; border-top: 3px solid #1a1a1a; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 15px;"></div>
                <p style="margin: 0; font-weight: 500;">Loading cause list...</p>
            </div>
        `;
        
        document.body.appendChild(overlay);
    }
    
    // Add CSS animation for loading spinner
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .printing .page-actions,
        .printing .date-navigation,
        .printing .debug-panel {
            display: none !important;
        }
        
        .printing .print-header {
            display: block !important;
        }
    `;
    document.head.appendChild(style);
    
    // Add loading state to navigation buttons
    const navButtons = document.querySelectorAll('.date-nav-btn, #go-to-date');
    navButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Don't show loading overlay for same-page actions
            if (!this.id || this.id !== 'print-list') {
                setTimeout(showLoadingOverlay, 200);
            }
        });
    });
    
    // Debug information
    if (window.location.search.includes('debug=1')) {
        console.log('=== CAUSELIST DEBUG INFO ===');
        console.log('Current date:', current_date);
        console.log('Admin URL:', admin_url);
        console.log('Today:', formatDate(new Date()));
        console.log('Elements found:', {
            quickDates: !!quickDatesSelect,
            dateSelector: !!dateSelector,
            goToDateBtn: !!goToDateBtn,
            prevDayBtn: !!prevDayBtn,
            nextDayBtn: !!nextDayBtn,
            todayBtn: !!todayBtn,
            printBtn: !!printBtn
        });
        console.log('Court sections:', courtSections.length);
        console.log('Table rows:', tableRows.length);
        console.log('============================');
    }
    
    // Error handling for navigation
    window.addEventListener('error', function(e) {
        console.error('Causelist error:', e.error);
        
        // Reset button states if there's an error
        navButtons.forEach(function(button) {
            if (button.disabled) {
                button.disabled = false;
                if (button.id === 'prev-day') button.textContent = '← Previous';
                else if (button.id === 'next-day') button.textContent = 'Next →';
                else if (button.id === 'today') button.textContent = 'Today';
                else if (button.id === 'go-to-date') button.textContent = 'Go to Date';
            }
        });
        
        // Remove loading overlay if it exists
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    });
    
    // Page unload cleanup
    window.addEventListener('beforeunload', function() {
        stopAutoRefresh();
        
        // Remove any overlays
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    });
    
    console.log('Causelist script fully loaded and initialized');
});
</script>
<?php init_tail(); ?>