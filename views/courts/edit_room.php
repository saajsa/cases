<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('cases/courts/edit_room/'.$room['id'])); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="court_id" class="control-label">Court</label>
                                    <select name="court_id" id="court_id" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">Select Court</option>
                                        <?php foreach($courts as $court){ ?>
                                        <option value="<?php echo $court['id']; ?>" <?php if($court['id'] == $room['court_id']){echo 'selected';} ?>><?php echo $court['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="court_no" class="control-label">Court Number</label>
                                    <input type="text" id="court_no" name="court_no" class="form-control" value="<?php echo $room['court_no']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="judge_name" class="control-label">Judge Name</label>
                                    <input type="text" id="judge_name" name="judge_name" class="form-control" value="<?php echo $room['judge_name']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="from_date" class="control-label">From Date</label>
                                    <div class="input-group date">
                                        <input type="text" id="from_date" name="from_date" class="form-control datepicker" value="<?php echo $room['from_date']; ?>">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_date" class="control-label">To Date</label>
                                    <div class="input-group date">
                                        <input type="text" id="to_date" name="to_date" class="form-control datepicker" value="<?php echo $room['to_date']; ?>">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="type" class="control-label">Type</label>
                                    <input type="text" id="type" name="type" class="form-control" value="<?php echo $room['type']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="bench_type" class="control-label">Bench Type</label>
                                    <input type="text" id="bench_type" name="bench_type" class="form-control" value="<?php echo $room['bench_type']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="status" class="control-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Active" <?php if($room['status'] == 'Active'){echo 'selected';} ?>>Active</option>
                                        <option value="Inactive" <?php if($room['status'] == 'Inactive'){echo 'selected';} ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-info pull-right">Update Court Room</button>
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
            court_id: 'required',
            court_no: 'required',
            status: 'required'
        });

        init_datepicker();
    });
</script>
</body>
</html>