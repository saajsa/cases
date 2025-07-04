<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _l('advanced_document_search'); ?></h3>
    </div>
    <div class="panel-body">
        <?php echo form_open(admin_url('cases/documents/advanced_search')); ?>
        
        <div class="row">
            <div class="col-md-6">
                <?php echo render_select('customer_id', $customers, array('userid', 'company'), 'client', isset($customer_id) ? $customer_id : ''); ?>
            </div>
            <div class="col-md-6">
                <?php echo render_input('document_tag', 'document_tag', isset($document_tag) ? $document_tag : ''); ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?php echo render_date_input('date_from', 'date_from', isset($date_from) ? $date_from : ''); ?>
            </div>
            <div class="col-md-6">
                <?php echo render_date_input('date_to', 'date_to', isset($date_to) ? $date_to : ''); ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary"><?php echo _l('search'); ?></button>
                <a href="<?php echo admin_url('cases/documents'); ?>" class="btn btn-default"><?php echo _l('reset'); ?></a>
            </div>
        </div>
        
        <?php echo form_close(); ?>
    </div>
</div>