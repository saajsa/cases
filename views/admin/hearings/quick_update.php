<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel_s">
          <div class="panel-body">
          <div class="row">
                <div class="col-md-7">
                    <h4>
                    <i class="fa fa-gavel"></i> Quick Update Hearing
                    </h4>
                </div>
                <div class="col-md-5 text-right">
                    <!-- Updated back button to go to main cases page -->
                    <a href="<?php echo admin_url('cases'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to Cases
                    </a>
                </div>
            </div>
            <hr>
            
            <!-- Case Information (Read-only) -->
            <div class="alert alert-info">
              <div class="row">
                <div class="col-md-6">
                  <p class="bold">Case Information</p>
                  <p><strong>Case Title:</strong> <?php echo $case['case_title']; ?></p>
                  <p><strong>Case Number:</strong> <?php echo $case['case_number']; ?></p>
                </div>
                <div class="col-md-6">
                  <p class="bold">Current Hearing</p>
                  <p><strong>Hearing Date:</strong> <?php echo date('d M Y', strtotime($hearing['date'])); ?></p>
                  <p><strong>Current Status:</strong> 
                    <span class="label <?php echo $hearing['status'] == 'Completed' ? 'label-success' : 'label-primary'; ?>">
                      <?php echo $hearing['status']; ?>
                    </span>
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Quick Update Form -->
            <form method="POST" action="<?php echo admin_url('cases/hearings/quick_update/' . $hearing['id']); ?>">
              <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
              
              <!-- Form Sections -->
              <div class="panel-group" id="quick-update-accordion">
                <!-- Current Hearing Updates -->
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#quick-update-accordion" href="#current-status-panel">
                        <i class="fa fa-refresh"></i> Update Current Hearing Status
                      </a>
                    </h4>
                  </div>
                  <div id="current-status-panel" class="panel-collapse collapse in">
                    <div class="panel-body">
                      <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-control">
                          <?php 
                          $statuses = ['Scheduled', 'Adjourned', 'Completed', 'Cancelled'];
                          foreach ($statuses as $status) {
                            $selected = ($hearing['status'] == $status) ? 'selected' : '';
                            echo '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                      
                      <div class="form-group">
                        <label for="description" class="control-label">Outcome/Notes</label>
                        <textarea name="description" id="description" class="form-control" rows="3" 
                          placeholder="Enter the outcome or notes about this hearing"><?php echo $hearing['description']; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Next Hearing Details -->
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#quick-update-accordion" href="#next-hearing-panel">
                        <i class="fa fa-calendar-plus-o"></i> Schedule Next Hearing
                      </a>
                    </h4>
                  </div>
                  <div id="next-hearing-panel" class="panel-collapse collapse in">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="next_date" class="control-label">Next Date</label>
                            <div class="input-group date">
                              <input type="date" name="next_date" id="next_date" class="form-control" 
                                value="<?php echo !empty($hearing['next_date']) ? $hearing['next_date'] : 
                                        (!empty($upcoming_hearing['date']) ? $upcoming_hearing['date'] : ''); ?>">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar calendar-icon"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="next_time" class="control-label">Next Time</label>
                            <div class="input-group">
                              <input type="time" name="next_time" id="next_time" class="form-control" 
                                value="<?php echo !empty($upcoming_hearing['time']) ? $upcoming_hearing['time'] : '10:00'; ?>">
                              <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="upcoming_purpose" class="control-label">Purpose of Next Hearing</label>
                        <input type="text" name="upcoming_purpose" id="upcoming_purpose" class="form-control" 
                          value="<?php echo isset($upcoming_hearing['hearing_purpose']) ? $upcoming_hearing['hearing_purpose'] : ''; ?>" 
                          placeholder="e.g., Arguments, Evidence, Witness Examination">
                        <small class="text-muted">This will be shown on the cause list for the next date</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Replace the form actions buttons section in the quick_update.php view -->

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                <i class="fa fa-check"></i> Update and Schedule Next
                </button>
                <!-- Cancel link to go back to cases page -->
                <a href="<?php echo admin_url('cases'); ?>" class="btn btn-default">
                Cancel </a>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
  // Next hearing date validation - must be after current hearing
  $('#next_date').on('change', function() {
    var currentDate = new Date('<?php echo $hearing['date']; ?>');
    var nextDate = new Date($(this).val());
    
    if (nextDate <= currentDate) {
      alert('Next hearing date must be after the current hearing date');
      $(this).val('');
    }
  });
  
  // Set next hearing time to 10:00 by default if empty
  if (!$('#next_time').val()) {
    $('#next_time').val('10:00');
  }
});
</script>