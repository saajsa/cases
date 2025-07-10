<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _l('smart_document_upload'); ?></h3>
    </div>
    <div class="panel-body">
        <?php echo form_open_multipart(admin_url('cases/documents/smart_upload')); ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo _l('select_files'); ?></label>
                    <input type="file" name="documents[]" multiple class="cases-form-control" accept=".pdf,.doc,.docx,.jpg,.png,.txt">
                    <small class="help-block"><?php echo _l('smart_upload_help'); ?></small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?php echo render_select('customer_id', $customers, array('userid', 'company'), 'client', ''); ?>
            </div>
            <div class="col-md-6">
                <?php echo render_input('default_tag', 'default_tag', ''); ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="auto_categorize" value="1" checked> 
                        <?php echo _l('auto_categorize_documents'); ?>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary"><?php echo _l('upload_documents'); ?></button>
                <a href="<?php echo admin_url('cases/documents'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
            </div>
        </div>
        
        <?php echo form_close(); ?>
    </div>
</div>