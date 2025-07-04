<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-mb-2">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo _l('hearings_report'); ?></h3>
                </div>
                <div class="panel-body">
                    <?php if(empty($report_data)): ?>
                        <div class="alert alert-info">
                            <?php echo _l('no_hearings_found'); ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('case'); ?></th>
                                        <th><?php echo _l('hearing_date'); ?></th>
                                        <th><?php echo _l('hearing_purpose'); ?></th>
                                        <th><?php echo _l('court'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($report_data as $hearing): ?>
                                    <tr>
                                        <td><?php echo $hearing['case_title']; ?></td>
                                        <td><?php echo _dt($hearing['date']); ?></td>
                                        <td><?php echo $hearing['hearing_purpose']; ?></td>
                                        <td><?php echo $hearing['court_name']; ?></td>
                                        <td><?php echo $hearing['client_name']; ?></td>
                                        <td>
                                            <?php if(strtotime($hearing['date']) > time()): ?>
                                                <span class="label label-info"><?php echo _l('upcoming'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-success"><?php echo _l('completed'); ?></span>
                                            <?php endif; ?>
                                        </td>
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