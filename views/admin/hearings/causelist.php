<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['tables', 'buttons', 'cards', 'status'], 'causelist');
?>

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

<!-- Load the modular causelist JavaScript -->
<script src="<?php echo base_url('modules/cases/assets/js/causelist.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the causelist manager
    const causelist = new CauselistManager({
        adminUrl: '<?php echo admin_url(); ?>',
        currentDate: '<?php echo $date; ?>'
    });
});
</script>
<?php init_tail(); ?>