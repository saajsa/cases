<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'forms']);
?>
<div id="wrapper">
  <div class="content">

    <!-- 📂 Header & Action Buttons -->
    <div class="row mbot20">
      <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="no-mtop"><?php echo _l('document_manager'); ?></h3>
          <div class="btn-group">
            <a href="<?php echo admin_url('cases/documents/upload'); ?>"
               class="btn btn-<?php echo ($this->uri->segment(3) == 'upload') ? 'primary' : 'default'; ?>">
              <i class="fa fa-upload"></i> <?php echo _l('upload_document'); ?>
            </a>
            <a href="<?php echo admin_url('cases/documents/search'); ?>"
               class="btn btn-<?php echo ($this->uri->segment(3) == 'search') ? 'primary' : 'default'; ?>">
              <i class="fa fa-search"></i> <?php echo _l('search_documents'); ?>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- 📝 Activity Log Panel -->
    <div class="panel_s">
      <div class="panel-body">
        <h4 class="mbot20">
          <i class="fa fa-history text-info"></i> <?php echo _l('recent_document_activity'); ?>
        </h4>

        <?php if (!empty($activities)) { ?>
          <div class="activity-feed">
            <?php foreach ($activities as $activity) { ?>
              <div class="feed-item mbot15">
                <div class="feed-item-list">
                  <span class="text-primary font-medium">
                    <?php echo $activity['staff_name'] ?? _l('system'); ?>
                  </span>:
                  <?php echo $activity['message']; ?>
                  <div class="text-muted small mt-1">
                    <i class="fa fa-clock-o"></i> <?php echo _dt($activity['created_at']); ?>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } else { ?>
          <p class="text-muted">
            <i class="fa fa-info-circle"></i> <?php echo _l('no_recent_activity'); ?>
          </p>
        <?php } ?>
      </div>
    </div>

  </div>
</div>
<?php init_tail(); ?>
