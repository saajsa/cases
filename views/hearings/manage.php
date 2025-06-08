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

.content-section {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 20px 0;
    letter-spacing: -0.01em;
    padding-bottom: 15px;
    border-bottom: 1px solid #e1e1e1;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

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
    padding: 2px 16px;
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

.case-info-box {
    background: #f8f8f8;
    border: 1px solid #e1e1e1;
    border-left: 3px solid #1a1a1a;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 2px;
}

.case-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.case-number {
    font-size: 0.875rem;
    color: #666666;
    margin-bottom: 15px;
}

.case-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.case-meta-item {
    font-size: 0.8rem;
}

.case-meta-item label {
    display: block;
    color: #666666;
    font-weight: 500;
    margin-bottom: 3px;
}

.case-meta-item p {
    color: #1a1a1a;
    margin: 0;
    font-weight: 400;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid #e1e1e1;
    margin-top: 25px;
}

.action-btn {
    border-radius: 1px;
    padding: 12px 24px;
    font-size: 0.875rem;
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

.action-btn.btn-default {
    background: #ffffff;
    border-color: #d1d1d1;
    color: #2c2c2c;
}

.action-btn.btn-default:hover {
    background: #f8f8f8;
    border-color: #999999;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    overflow: hidden;
}

.modern-table thead th {
    background: #f8f8f8;
    color: #1a1a1a;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 20px;
    border-bottom: 1px solid #e1e1e1;
    border-right: 1px solid #f0f0f0;
    text-align: left;
}

.modern-table thead th:last-child {
    border-right: none;
}

.modern-table tbody td {
    padding: 20px;
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

.table-action-btn {
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
}

.table-action-btn.btn-primary {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

.table-action-btn.btn-primary:hover {
    background: #000000;
    border-color: #000000;
}

.table-action-btn.btn-success {
    background: #ffffff;
    border-color: #2d7d2d;
    color: #2d7d2d;
}

.table-action-btn.btn-success:hover {
    background: #2d7d2d;
    color: #ffffff;
}

.table-action-btn.btn-danger {
    background: #ffffff;
    border-color: #cc1a1a;
    color: #cc1a1a;
}

.table-action-btn.btn-danger:hover {
    background: #cc1a1a;
    color: #ffffff;
}

.table-action-btn.btn-default {
    background: #ffffff;
    border-color: #d1d1d1;
    color: #2c2c2c;
}

.table-action-btn.btn-default:hover {
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
    
    .content-section {
        padding: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .case-meta-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        text-align: center;
        margin-bottom: 8px;
    }
    
    .modern-table {
        font-size: 0.75rem;
    }
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 12px 8px;
    }
}
</style>

<div id="wrapper">
  <div class="content">
    <!-- Minimalist Page Header -->
    <div class="page-header">
      <div class="row">
        <div class="col-md-8">
          <h1>
            <?php echo isset($hearing) ? 'Edit Hearing' : 'Manage Hearings'; ?>
            <?php if ($this->input->get('case_id') && isset($case)): ?>
              <span style="color: #666666; font-size: 1.2rem; font-weight: 400;"> - <?php echo htmlspecialchars($case['case_title']); ?> (#<?php echo htmlspecialchars($case['case_number']); ?>)</span>
            <?php endif; ?>
          </h1>
          <div class="subtitle">Schedule and manage court hearings</div>
        </div>
        <div class="col-md-4">
          <div class="page-actions text-right">
            <a href="<?php echo admin_url('cases'); ?>" class="btn">
              ← Back to Cases
            </a>
            <a href="<?php echo admin_url('cases/hearings/causelist'); ?>" class="btn btn-primary">
              View Cause List
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Hearing Form Section -->
    <div class="content-section">
      <h3 class="section-title">
        <?php echo isset($hearing) ? 'Edit Hearing Details' : 'Add New Hearing'; ?>
      </h3>
      
      <form method="POST" action="<?php echo isset($hearing) ? admin_url('cases/hearings/edit/'.$hearing['id']) : admin_url('cases/hearings/add'); ?>">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        
        <?php if ($this->input->get('case_id')): ?>
          <input type="hidden" name="redirect_url" value="<?php echo admin_url('cases/view_case/'.$this->input->get('case_id')); ?>">
        <?php endif; ?>
        
        <!-- Case Information Display -->
        <?php if ($this->input->get('case_id') && isset($case)): ?>
          <div class="case-info-box">
            <div class="case-title"><?php echo htmlspecialchars($case['case_title']); ?></div>
            <div class="case-number">Case #<?php echo htmlspecialchars($case['case_number']); ?></div>
            <input type="hidden" name="case_id" value="<?php echo $case['id']; ?>">
            
            <div class="case-meta-grid">
              <?php if (isset($case['client_id']) && !empty($case['client_id'])): ?>
              <div class="case-meta-item">
                <label>Client:</label>
                <p>
                  <?php 
                  $this->db->select('company');
                  $this->db->from(db_prefix().'clients');
                  $this->db->where('userid', $case['client_id']);
                  $client = $this->db->get()->row_array();
                  echo !empty($client) ? htmlspecialchars($client['company']) : 'N/A';
                  ?>
                </p>
              </div>
              <?php endif; ?>

              <?php if (isset($case['date_filed']) && !empty($case['date_filed'])): ?>
              <div class="case-meta-item">
                <label>Filed On:</label>
                <p><?php echo date('d M Y', strtotime($case['date_filed'])); ?></p>
              </div>
              <?php endif; ?>

              <?php if (isset($case['court_room_id']) && !empty($case['court_room_id'])): ?>
              <div class="case-meta-item">
                <label>Court:</label>
                <p>
                  <?php 
                  $this->db->select('cr.court_no, cr.judge_name, c.name as court_name');
                  $this->db->from('tblcourt_rooms cr');
                  $this->db->join('tblcourts c', 'c.id = cr.court_id', 'left');
                  $this->db->where('cr.id', $case['court_room_id']);
                  $court = $this->db->get()->row_array();
                  
                  if (!empty($court)) {
                    echo htmlspecialchars($court['court_name']);
                    if (!empty($court['court_no'])) echo ' - Court ' . htmlspecialchars($court['court_no']);
                    if (!empty($court['judge_name'])) echo ' (' . htmlspecialchars($court['judge_name']) . ')';
                  } else {
                    echo 'N/A';
                  }
                  ?>
                </p>
              </div>
              <?php endif; ?>
            </div>
          </div>
        <?php else: ?>
          <!-- Case Selection -->
          <div class="form-group">
            <label class="form-label">Case <span style="color: #cc1a1a;">*</span></label>
            <select name="case_id" id="case_id" class="form-control" required>
              <option value="">Select Case</option>
              <?php
              $defaultCaseId = $this->input->get('case_id');
              if (!empty($cases)) {
                foreach ($cases as $case) {
                  $selected = (isset($hearing) && $hearing['case_id'] == $case['id']) || $defaultCaseId == $case['id'] ? 'selected' : '';
                  echo '<option value="' . $case['id'] . '" ' . $selected . '>' . htmlspecialchars($case['case_title']) . ' (#' . htmlspecialchars($case['case_number']) . ')</option>';
                }
              }
              ?>
            </select>
          </div>
        <?php endif; ?>
        
        <!-- Current Hearing Details -->
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Hearing Date <span style="color: #cc1a1a;">*</span></label>
            <input type="date" name="date" id="date" class="form-control" 
              value="<?php echo isset($hearing) ? $hearing['date'] : date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Hearing Time <span style="color: #cc1a1a;">*</span></label>
            <input type="time" name="time" id="time" class="form-control" 
              value="<?php echo isset($hearing) ? $hearing['time'] : '10:00'; ?>" required>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Purpose of Hearing</label>
          <input type="text" name="hearing_purpose" id="hearing_purpose" class="form-control" 
            value="<?php echo isset($hearing['hearing_purpose']) ? htmlspecialchars($hearing['hearing_purpose']) : ''; ?>" 
            placeholder="e.g., Arguments, Evidence, Motion Hearing">
        </div>
        
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="Scheduled" <?php echo (!isset($hearing) || (isset($hearing) && $hearing['status'] == 'Scheduled')) ? 'selected' : ''; ?>>Scheduled</option>
              <option value="Adjourned" <?php echo (isset($hearing) && $hearing['status'] == 'Adjourned') ? 'selected' : ''; ?>>Adjourned</option>
              <option value="Completed" <?php echo (isset($hearing) && $hearing['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
              <option value="Cancelled" <?php echo (isset($hearing) && $hearing['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Next Hearing Date</label>
            <input type="date" name="next_date" id="next_date" class="form-control" 
              value="<?php echo isset($hearing) && !empty($hearing['next_date']) ? $hearing['next_date'] : ''; ?>">
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Notes/Outcome</label>
          <textarea name="description" id="description" class="form-control" rows="4" 
            placeholder="Enter the outcome, proceedings or notes about this hearing"><?php echo isset($hearing) ? htmlspecialchars($hearing['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="action-btn btn-primary">
            <?php echo isset($hearing) ? 'Update Hearing' : 'Save Hearing'; ?>
          </button>
          <?php if (!isset($hearing)): ?>
            <button type="reset" class="action-btn btn-default">
              Reset Form
            </button>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <?php if (!isset($hearing)): ?>
    <!-- All Hearings Section -->
    <div class="content-section">
      <h3 class="section-title">All Hearings</h3>
      
      <?php if (!empty($hearings)): ?>
        <table class="modern-table">
          <thead>
            <tr>
              <th>Case</th>
              <th>Date & Time</th>
              <th>Status</th>
              <th>Next Date</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($hearings as $h): ?>
              <tr>
                <td>
                  <a href="<?php echo admin_url('cases/view_case/' . $h['case_id']); ?>" style="color: #1a1a1a; text-decoration: none; font-weight: 500;">
                    <?php echo htmlspecialchars($h['case_title']); ?>
                  </a>
                </td>
                <td>
                  <div style="font-weight: 600; color: #1a1a1a;"><?php echo date('d M Y', strtotime($h['date'])); ?></div>
                  <div style="font-size: 0.8rem; color: #666666;"><?php echo date('h:i A', strtotime($h['time'])); ?></div>
                </td>
                <td>
                  <?php 
                  $statusClass = 'status-scheduled';
                  switch(strtolower($h['status'])) {
                    case 'completed': $statusClass = 'status-completed'; break;
                    case 'adjourned': $statusClass = 'status-adjourned'; break;
                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                    default: $statusClass = 'status-scheduled';
                  }
                  ?>
                  <span class="status-badge <?php echo $statusClass; ?>"><?php echo $h['status']; ?></span>
                </td>
                <td>
                  <?php echo !empty($h['next_date']) ? date('d M Y', strtotime($h['next_date'])) : '<span style="color: #999999;">No next date</span>'; ?>
                </td>
                <td>
                  <?php 
                  if (!empty($h['description'])) {
                    $truncatedDescription = strlen($h['description']) > 50 
                      ? substr($h['description'], 0, 50) . '...' 
                      : $h['description'];
                    echo '<span title="' . htmlspecialchars($h['description']) . '">' . htmlspecialchars($truncatedDescription) . '</span>';
                  } else {
                    echo '<span style="color: #999999;">No description</span>';
                  }
                  ?>
                </td>
                <td>
                  <a href="<?php echo admin_url('cases/hearings/quick_update/' . $h['id']); ?>" 
                    class="table-action-btn btn-success" title="Quick Update">
                    Update
                  </a>
                  <a href="<?php echo admin_url('cases/hearings/edit/' . $h['id']); ?>" 
                    class="table-action-btn btn-primary">
                    Edit
                  </a>
                  <a href="<?php echo admin_url('cases/hearings/delete/' . $h['id']); ?>" 
                    class="table-action-btn btn-danger" 
                    onclick="return confirm('Are you sure you want to delete this hearing?');">
                    Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-calendar-times"></i>
          <h5>No Hearings Found</h5>
          <p>Start by scheduling your first hearing</p>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Clean JavaScript
document.addEventListener('DOMContentLoaded', function() {
  // Next hearing date validation
  const dateField = document.getElementById('date');
  const nextDateField = document.getElementById('next_date');
  
  if (nextDateField && dateField) {
    nextDateField.addEventListener('change', function() {
      const currentDate = new Date(dateField.value);
      const nextDate = new Date(this.value);
      
      if (nextDate <= currentDate) {
        alert('Next hearing date must be after the current hearing date');
        this.value = '';
      }
    });
  }
  
  // Form reset confirmation
  const resetBtn = document.querySelector('button[type="reset"]');
  if (resetBtn) {
    resetBtn.addEventListener('click', function(e) {
      if (!confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
        e.preventDefault();
      }
    });
  }
  
  // Table row hover effects
  const tableRows = document.querySelectorAll('.modern-table tbody tr');
  tableRows.forEach(row => {
    row.addEventListener('mouseenter', function() {
      this.style.backgroundColor = '#fafafa';
    });
    
    row.addEventListener('mouseleave', function() {
      this.style.backgroundColor = '';
    });
  });
  
  // Status change handling
  const statusSelect = document.querySelector('select[name="status"]');
  if (statusSelect) {
    statusSelect.addEventListener('change', function() {
      const isCompleted = this.value === 'Completed';
      const nextDateField = document.getElementById('next_date');
      
      if (isCompleted && nextDateField) {
        nextDateField.value = '';
        nextDateField.style.backgroundColor = '#f5f5f5';
        nextDateField.disabled = true;
      } else if (nextDateField) {
        nextDateField.style.backgroundColor = '';
        nextDateField.disabled = false;
      }
    });
  }
});
</script>

<?php init_tail(); ?>