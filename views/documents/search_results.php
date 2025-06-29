<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <h4 class="no-mtop"><?php echo _l('search_results'); ?></h4>

<?php if (!empty($results)):

      /* ───────────────────────────────────────────────────
         1)  Gather IDs by rel_type so we can bulk-fetch in
             one query per type (prevents N+1 explosions).
         ─────────────────────────────────────────────────── */
      $ids = ['consultation'=>[], 'case'=>[], 'hearing'=>[]];
      foreach ($results as $f) {
          if (isset($ids[$f->rel_type])) $ids[$f->rel_type][] = (int)$f->rel_id;
      }

      $labels = [];   // id → label HTML

      /* ---- CONSULTATIONS ---- */
      if ($ids['consultation']) {
          $rows = $this->db->select('id, tag, date_added')
                           ->where_in('id', $ids['consultation'])
                           ->get(db_prefix().'case_consultations')
                           ->result();
          foreach ($rows as $r) {
              $labels['consultation'][$r->id] =
                 '<span class="text-warning">Consultation: '.htmlspecialchars($r->tag).
                 ' ('.date('d-m-Y', strtotime($r->date_added)).')</span>';
          }
      }

      /* ---- CASES ---- */
      if ($ids['case']) {
          $rows = $this->db->select('id, case_title, case_number')
                           ->where_in('id', $ids['case'])
                           ->get(db_prefix().'cases')
                           ->result();
          foreach ($rows as $r) {
              $labels['case'][$r->id] =
                 '<span class="text-success">Case: '.htmlspecialchars($r->case_title).
                 ' ('.htmlspecialchars($r->case_number).')</span>';
          }
      }

      /* ---- HEARINGS (join for case title) ---- */
      if ($ids['hearing']) {
          $h = db_prefix().'hearings'; $c = db_prefix().'cases';
          $rows = $this->db->select("h.id, h.date, h.hearing_purpose, c.case_title")
                           ->from("$h h")
                           ->join("$c c", 'c.id = h.case_id', 'left')
                           ->where_in('h.id', $ids['hearing'])
                           ->get()->result();
          foreach ($rows as $r) {
              $labels['hearing'][$r->id] =
                 '<span class="text-info">Hearing: '.date('d-m-Y', strtotime($r->date)).
                 ' - '.htmlspecialchars($r->hearing_purpose).
                 ' ('.htmlspecialchars($r->case_title).')</span>';
          }
      }
?>

        <div class="table-responsive">
          <table class="table dt-table">
            <thead>
              <tr>
                <th><?php echo _l('file_name'); ?></th>
                <th><?php echo _l('file_type'); ?></th>
                <th><?php echo _l('document_tag'); ?></th>
                <th><?php echo _l('belongs_to'); ?></th>
                <th><?php echo _l('date_added'); ?></th>
                <th><?php echo _l('actions'); ?></th>
              </tr>
            </thead>
            <tbody>
<?php foreach ($results as $file): ?>
              <tr>
                <td><?= htmlspecialchars($file->file_name) ?></td>
                <td><?= htmlspecialchars($file->filetype) ?></td>

                <!-- Tag -->
                <td>
                  <?= $file->tag
                         ? '<span class="label label-info">'.htmlspecialchars($file->tag).'</span>'
                         : '<span class="text-muted">'._l('no_tag').'</span>'; ?>
                </td>

                <!-- Belongs To -->
                <td>
<?php
      if (!empty($file->contact_id)) {
          echo '<span class="text-primary">'._l('contact').' #'.$file->contact_id.'</span>';
      } elseif (isset($labels[$file->rel_type][$file->rel_id])) {
          echo $labels[$file->rel_type][$file->rel_id];
      } elseif ($file->rel_type === 'client') {
          echo '<span class="text-primary">'._l('client').' #'.$file->rel_id.'</span>';
      } elseif ($file->rel_type === 'invoice') {
          echo '<span class="text-danger">'._l('invoice').' #'.$file->rel_id.'</span>';
      } else {
          echo '<span class="text-muted">Unknown</span>';
      }
?>
                </td>

                <!-- Date -->
                <td><?= _d($file->dateadded) ?></td>

                <!-- Actions -->
                <td class="actions">
                  <a href="<?= admin_url('cases/documents/edit/'.$file->id) ?>" class="btn btn-default btn-sm">
                    <i class="fa fa-pencil"></i> <?= _l('edit') ?>
                  </a>
                  <a href="<?= admin_url('cases/documents/view/'.$file->id) ?>" target="_blank" class="btn btn-default btn-sm">
                    <i class="fa fa-eye"></i> <?= _l('view') ?>
                  </a>
                  <a href="<?= admin_url('cases/documents/download/'.$file->id) ?>" class="btn btn-default btn-sm">
                    <i class="fa fa-download"></i> <?= _l('download') ?>
                  </a>
                  <a href="<?= admin_url('cases/documents/delete/'.$file->id) ?>"
                     onclick="return confirm('<?= _l('confirm_action_prompt') ?>');"
                     class="btn btn-default btn-sm">
                    <i class="fa fa-trash"></i> <?= _l('delete') ?>
                  </a>
                </td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>

<?php else: ?>
        <p class="text-muted"><?= _l('no_results_found') ?></p>
<?php endif; ?>

        <a href="<?= admin_url('cases/documents/search') ?>" class="btn btn-default">
          <?= _l('back_to_search') ?>
        </a>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
