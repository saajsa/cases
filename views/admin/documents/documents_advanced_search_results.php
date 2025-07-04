<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _l('advanced_search_results'); ?></h3>
    </div>
    <div class="panel-body">
        <?php if(empty($documents)): ?>
            <div class="alert alert-info">
                <?php echo _l('no_documents_found'); ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo _l('file_name'); ?></th>
                            <th><?php echo _l('tag'); ?></th>
                            <th><?php echo _l('client'); ?></th>
                            <th><?php echo _l('type'); ?></th>
                            <th><?php echo _l('date_added'); ?></th>
                            <th><?php echo _l('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($documents as $document): ?>
                        <tr>
                            <td><?php echo $document['file_name']; ?></td>
                            <td><?php echo $document['tag']; ?></td>
                            <td><?php echo $document['client_name']; ?></td>
                            <td><?php echo ucfirst($document['rel_type']); ?></td>
                            <td><?php echo _dt($document['dateadded']); ?></td>
                            <td>
                                <a href="<?php echo admin_url('cases/documents/view/' . $document['id']); ?>" 
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