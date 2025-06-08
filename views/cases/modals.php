<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<!-- Consultation Modal -->
<?php echo form_open_multipart(admin_url('consultations/consultation' . (isset($consultation) ? '/' . $consultation->id : '')), ['id' => 'consultationForm']); ?>
<div class="modal fade<?php if (isset($consultation)) { echo ' edit'; } ?>" id="consultationModal" tabindex="-1" role="dialog" aria-labelledby="consultationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="consultationModalLabel">
                    <?php echo isset($consultation) ? _l('edit_consultation') : _l('add_consultation'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('consultation_id', isset($consultation) ? $consultation->id : ''); ?>

                        <?php echo render_select('client_id', $clients, ['userid', ['company']], 'client', isset($consultation) ? $consultation->client_id : '', ['required' => true]); ?>

                        <div id="contact-group" class="form-group<?php if (!(isset($consultation) && $consultation->contact_id)) echo ' hide'; ?>">
                            <?php echo render_select('contact_id', $contacts, ['id', ['firstname', 'lastname']], 'contact', isset($consultation) ? $consultation->contact_id : ''); ?>
                        </div>

                        <div class="form-group">
                            <label for="invoice_id" class="control-label"><?php echo _l('invoice'); ?></label>
                            <select name="invoice_id" id="invoice_id" class="selectpicker" data-width="100%" required data-none-selected-text="<?php echo _l('dropdown_non_selected_text'); ?>">
                                <option value=""></option>
                                <?php if (isset($consultation) && $consultation->invoice_id): ?>
                                    <option value="<?php echo $consultation->invoice_id; ?>" selected><?php echo format_invoice_number($consultation->invoice_id); ?></option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tag" class="control-label"><?php echo _l('tag'); ?></label>
                            <input type="text" name="tag" id="tag" class="form-control" value="<?php echo isset($consultation) ? $consultation->tag : ''; ?>">
                        </div>

                        <hr class="-tw-mx-3.5" />

                        <?php
                        echo render_textarea(
                            'note',
                            _l('note'),
                            isset($consultation) ? $consultation->note : '',
                            [
                                'rows' => 6,
                                'placeholder' => _l('consultation_add_note'),
                                'data-task-ae-editor' => true,
                                !is_mobile() ? 'onclick' : 'onfocus' => (!isset($consultation) || (isset($consultation) && $consultation->note == '') ?
                                    'init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')
                            ],
                            [],
                            'no-mbot',
                            'tinymce-task'
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script>
$(document).ready(function() {
    init_selectpicker();
    init_datepicker();

    $('#client_id').on('change', function() {
        var client_id = $(this).val();
        if (client_id) {
            $('#contact-group').removeClass('hide');
        } else {
            $('#contact-group').addClass('hide');
        }
    });

    appValidateForm($('#consultationForm'), {
        client_id: 'required',
        invoice_id: 'required'
    });
});
</script>

<!-- View Note Modal -->
<div class="modal fade" id="viewNoteModal" tabindex="-1" role="dialog" aria-labelledby="viewNoteModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo _l('close'); ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="viewNoteModalLabel"><?php echo _l('consultation_note'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div id="noteContent"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>


<!-- Upgrade to Litigant Modal -->
<?php echo form_open(admin_url('litigation/register_case'), ['id' => 'upgradeForm']); ?>
<div class="modal fade<?php if (isset($litigation)) { echo ' edit'; } ?>" id="upgradeModal" tabindex="-1" role="dialog" aria-labelledby="upgradeModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo _l('close'); ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="upgradeModalLabel"><?php echo _l('register_litigation_case'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php echo render_input('litigation_consultation_id', 'linked_consultation_id', isset($consultation) ? $consultation->id : '', 'text', ['readonly' => true]); ?>

            <?php echo render_input('case_title', 'case_title', isset($litigation) ? $litigation->case_title : ''); ?>

            <?php echo render_input('case_number', 'case_number', isset($litigation) ? $litigation->case_number : ''); ?>

            <?php echo render_select('court_id', $courts, ['id', 'name'], 'court', isset($litigation) ? $litigation->court_id : '', ['required' => true]); ?>

            <div class="form-group">
              <label for="court_room_id"><?php echo _l('court_room_judge'); ?></label>
              <select name="court_room_id" id="court_room_id" class="form-control" required>
                <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                <?php if (isset($litigation) && $litigation->court_room_id): ?>
                  <option value="<?php echo $litigation->court_room_id; ?>" selected><?php echo $litigation->court_room_name; ?></option>
                <?php endif; ?>
              </select>
            </div>

            <?php echo render_date_input('date_filed', 'date_filed', isset($litigation) ? _d($litigation->date_filed) : ''); ?>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-success"><?php echo _l('register_case'); ?></button>
      </div>
    </div>
  </div>
<?php echo form_close(); ?>
