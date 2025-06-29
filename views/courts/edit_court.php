<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('cases/courts/edit_court/'.$court['id'])); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="control-label">Court Name</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($court['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="hierarchy" class="control-label">Hierarchy</label>
                                    <input type="text" id="hierarchy" name="hierarchy" class="form-control" value="<?php echo htmlspecialchars($court['hierarchy'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="location" class="control-label">Location</label>
                                    <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($court['location'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="status" class="control-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Active" <?php if($court['status'] == 'Active'){echo 'selected';} ?>>Active</option>
                                        <option value="Inactive" <?php if($court['status'] == 'Inactive'){echo 'selected';} ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-info pull-right">Update Court</button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        appValidateForm($('form'), {
            name: 'required',
            status: 'required'
        });
    });
</script>
</body>
</html>