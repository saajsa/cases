<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-mb-2">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo _l('consultations_report'); ?></h3>
                </div>
                <div class="panel-body">
                    <?php if(empty($report_data)): ?>
                        <div class="alert alert-info">
                            <?php echo _l('no_consultations_found'); ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('date'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('tag'); ?></th>
                                        <th><?php echo _l('phase'); ?></th>
                                        <th><?php echo _l('staff'); ?></th>
                                        <th><?php echo _l('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($report_data as $consultation): ?>
                                    <tr>
                                        <td><?php echo _dt($consultation['date_added']); ?></td>
                                        <td><?php echo $consultation['client_name']; ?></td>
                                        <td><?php echo $consultation['tag']; ?></td>
                                        <td>
                                            <span class="label label-<?php echo $consultation['phase'] == 'litigation' ? 'danger' : 'info'; ?>">
                                                <?php echo ucfirst($consultation['phase']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $consultation['staff_name']; ?></td>
                                        <td>
                                            <a href="<?php echo admin_url('cases/view_consultation/' . $consultation['id']); ?>" 
                                               class="btn btn-sm btn-primary">
                                                <?php echo _l('view'); ?>
                                            </a>
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