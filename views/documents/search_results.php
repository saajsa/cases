<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'forms', 'status', 'modals', 'tables', 'wizard']);
echo cases_page_wrapper_start(
    'Search Results',
    'Document search results',
    [
        [
            'text' => 'New Search',
            'href' => admin_url('cases/documents/search'),
            'class' => 'cases-btn cases-btn-primary',
            'icon' => 'fas fa-search'
        ],
        [
            'text' => 'Upload Document',
            'href' => admin_url('cases/documents/upload'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-upload'
        ]
    ]
);
?>

<?php echo cases_section_start('Search Results'); ?>

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
                 '<span class="cases-status-badge cases-status-warning">Consultation: '.htmlspecialchars($r->tag).
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
                 '<span class="cases-status-badge cases-status-success">Case: '.htmlspecialchars($r->case_title).
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
                 '<span class="cases-status-badge cases-status-info">Hearing: '.date('d-m-Y', strtotime($r->date)).
                 ' - '.htmlspecialchars($r->hearing_purpose).
                 ' ('.htmlspecialchars($r->case_title).')</span>';
          }
      }
?>

        <div class="cases-table-container">
          <table class="cases-table cases-table-hover">
            <thead>
              <tr>
                <th><i class="fas fa-file"></i> <?php echo _l('file_name'); ?></th>
                <th><i class="fas fa-file-alt"></i> <?php echo _l('file_type'); ?></th>
                <th><i class="fas fa-tag"></i> <?php echo _l('document_tag'); ?></th>
                <th><i class="fas fa-link"></i> <?php echo _l('belongs_to'); ?></th>
                <th><i class="fas fa-calendar"></i> <?php echo _l('date_added'); ?></th>
                <th><i class="fas fa-cogs"></i> <?php echo _l('actions'); ?></th>
              </tr>
            </thead>
            <tbody>
<?php foreach ($results as $file): ?>
              <tr>
                <td>
                  <div class="cases-file-info">
                    <i class="fas fa-file-pdf cases-file-icon"></i>
                    <span class="cases-file-name"><?= htmlspecialchars($file->file_name) ?></span>
                  </div>
                </td>
                <td>
                  <span class="cases-status-badge cases-status-info"><?= htmlspecialchars($file->filetype) ?></span>
                </td>

                <!-- Tag -->
                <td>
                  <?= $file->tag
                         ? '<span class="cases-status-badge cases-status-primary">'.htmlspecialchars($file->tag).'</span>'
                         : '<span class="cases-text-muted cases-no-tag">'._l('no_tag').'</span>'; ?>
                </td>

                <!-- Belongs To -->
                <td>
<?php
      if (!empty($file->contact_id)) {
          echo '<span class="cases-status-badge cases-status-info">'._l('contact').' #'.$file->contact_id.'</span>';
      } elseif (isset($labels[$file->rel_type][$file->rel_id])) {
          echo $labels[$file->rel_type][$file->rel_id];
      } elseif ($file->rel_type === 'client') {
          echo '<span class="cases-status-badge cases-status-primary">'._l('client').' #'.$file->rel_id.'</span>';
      } elseif ($file->rel_type === 'invoice') {
          echo '<span class="cases-status-badge cases-status-danger">'._l('invoice').' #'.$file->rel_id.'</span>';
      } else {
          echo '<span class="cases-text-muted">Unknown</span>';
      }
?>
                </td>

                <!-- Date -->
                <td>
                  <span class="cases-date"><?= _d($file->dateadded) ?></span>
                </td>

                <!-- Actions -->
                <td class="cases-table-actions">
                  <div class="cases-action-buttons">
                    <a href="<?= admin_url('cases/documents/edit/'.$file->id) ?>" 
                       class="cases-action-btn cases-btn-primary" 
                       title="<?= _l('edit') ?>">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="<?= admin_url('cases/documents/view/'.$file->id) ?>" 
                       target="_blank" 
                       class="cases-action-btn cases-btn-info" 
                       title="<?= _l('view') ?>">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= admin_url('cases/documents/download/'.$file->id) ?>" 
                       class="cases-action-btn cases-btn-success" 
                       title="<?= _l('download') ?>">
                      <i class="fas fa-download"></i>
                    </a>
                    <a href="<?= admin_url('cases/documents/delete/'.$file->id) ?>"
                       onclick="return confirm('<?= _l('confirm_action_prompt') ?>');"
                       class="cases-action-btn cases-btn-danger" 
                       title="<?= _l('delete') ?>">
                      <i class="fas fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>

<?php else: ?>
        <?php echo cases_empty_state(
            'No Documents Found',
            'No documents match your search criteria. Try adjusting your filters.',
            [
                'icon' => 'fas fa-search',
                'action' => [
                    'text' => 'New Search',
                    'href' => admin_url('cases/documents/search'),
                    'type' => 'primary'
                ]
            ]
        ); ?>
<?php endif; ?>

<?php echo cases_section_end(); ?>
<?php echo cases_page_wrapper_end(); ?>
<?php init_tail(); ?>
