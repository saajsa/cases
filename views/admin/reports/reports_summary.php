<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-mb-2">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo _l('cases_summary_report'); ?></h3>
                </div>
                <div class="panel-body">
                    <div class="clearfix"></div>
                    
                    <?php if(empty($report_data)): ?>
                        <div class="alert alert-info">
                            <?php echo _l('no_data_found'); ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('case_number'); ?></th>
                                        <th><?php echo _l('case_title'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('date_filed'); ?></th>
                                        <th><?php echo _l('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($report_data as $case): ?>
                                    <tr>
                                        <td><?php echo $case['case_number']; ?></td>
                                        <td><?php echo $case['case_title']; ?></td>
                                        <td><?php echo $case['client_name']; ?></td>
                                        <td><?php echo _dt($case['date_filed']); ?></td>
                                        <td><?php echo $case['status']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>