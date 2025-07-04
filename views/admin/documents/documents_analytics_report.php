<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo _l('document_analytics'); ?></h3>
            </div>
            <div class="panel-body">
                <?php if(empty($analytics_data)): ?>
                    <div class="alert alert-info">
                        <?php echo _l('no_analytics_data'); ?>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="panel panel-primary">
                                <div class="panel-body text-center">
                                    <h3><?php echo $analytics_data['total_documents']; ?></h3>
                                    <p><?php echo _l('total_documents'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="panel panel-success">
                                <div class="panel-body text-center">
                                    <h3><?php echo $analytics_data['documents_this_month']; ?></h3>
                                    <p><?php echo _l('documents_this_month'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="panel panel-info">
                                <div class="panel-body text-center">
                                    <h3><?php echo $analytics_data['active_clients']; ?></h3>
                                    <p><?php echo _l('active_clients'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="panel panel-warning">
                                <div class="panel-body text-center">
                                    <h3><?php echo $analytics_data['storage_used']; ?></h3>
                                    <p><?php echo _l('storage_used'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h4><?php echo _l('document_types_breakdown'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('document_type'); ?></th>
                                            <th><?php echo _l('count'); ?></th>
                                            <th><?php echo _l('percentage'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($analytics_data['type_breakdown'] as $type): ?>
                                        <tr>
                                            <td><?php echo ucfirst($type['type']); ?></td>
                                            <td><?php echo $type['count']; ?></td>
                                            <td><?php echo $type['percentage']; ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>