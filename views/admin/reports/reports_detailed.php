<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-mb-2">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo _l('cases_detailed_report'); ?></h3>
                </div>
                <div class="panel-body">
                    <?php if(empty($report_data)): ?>
                        <div class="alert alert-info">
                            <?php echo _l('no_data_found'); ?>
                        </div>
                    <?php else: ?>
                        <?php foreach($report_data as $case): ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4><?php echo $case['case_title']; ?> - <?php echo $case['case_number']; ?></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong><?php echo _l('client'); ?>:</strong> <?php echo $case['client_name']; ?><br>
                                            <strong><?php echo _l('date_filed'); ?>:</strong> <?php echo _dt($case['date_filed']); ?><br>
                                            <strong><?php echo _l('court'); ?>:</strong> <?php echo $case['court_name']; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong><?php echo _l('hearings_count'); ?>:</strong> <?php echo $case['hearings_count']; ?><br>
                                            <strong><?php echo _l('documents_count'); ?>:</strong> <?php echo $case['documents_count']; ?><br>
                                            <strong><?php echo _l('next_hearing'); ?>:</strong> 
                                            <?php echo !empty($case['next_hearing']) ? _dt($case['next_hearing']) : _l('no_scheduled_hearings'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>