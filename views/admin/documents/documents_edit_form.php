<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['forms', 'buttons', 'cards']);
?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">

        <!-- 🧭 Navigation Tabs -->
        <div class="page-title-actions" style="margin-bottom: 20px;">
          <ul class="nav nav-tabs" style="border-bottom: none;">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo admin_url('cases/documents/upload'); ?>"><?php echo _l('upload_document'); ?></a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="#"><?php echo _l('edit_document'); ?></a>
            </li>
          </ul>
        </div>

        <h4 class="no-mtop"><?php echo _l('edit_document'); ?>: <strong><?php echo htmlspecialchars($file->file_name); ?></strong></h4>

        <?php echo form_open_multipart(admin_url('cases/documents/edit/' . $file->id), ['id' => 'editDocumentForm']); ?>

          <!-- 🔁 Replace File -->
          <div class="form-group">
            <?php echo form_label(_l('replace_file'), 'document'); ?>
            <input type="file" name="document" id="document" class="cases-form-control">
            <small class="text-muted">
              <?php echo _l('leave_blank_to_keep_existing'); ?> |
              <?php echo _l('current_file'); ?>: <strong><?php echo htmlspecialchars($file->file_name); ?></strong>
            </small>
          </div>

          <!-- 👤 Customer -->
          <div class="form-group">
            <?php echo form_label(_l('select_customer'), 'customer_id'); ?>
            <select name="customer_id" id="customer_id" class="cases-form-select" required>
              <option value=""><?php echo _l('select_customer'); ?></option>
              <?php foreach ($customers as $customer) { ?>
                <option value="<?php echo $customer->userid; ?>" <?php echo ($file->rel_id == $customer->userid) ? 'selected' : ''; ?>>
                  <?php echo $customer->company; ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <!-- 📌 Relation Type -->
          <div class="form-group">
            <label><?php echo _l('document_belongs_to'); ?></label><br>
            <label><input type="radio" name="relation_type" value="invoice" <?php echo $file->rel_type == 'invoice' ? 'checked' : ''; ?>> <?php echo _l('invoice'); ?></label>
            <label><input type="radio" name="relation_type" value="customer" <?php echo $file->rel_type == 'client' && $file->contact_id == 0 ? 'checked' : ''; ?>> <?php echo _l('customer'); ?></label>
            <label><input type="radio" name="relation_type" value="contact" <?php echo $file->rel_type == 'client' && $file->contact_id > 0 ? 'checked' : ''; ?>> <?php echo _l('contact'); ?></label>
          </div>

          <!-- Invoice -->
          <div class="form-group" id="invoice_div" style="display: none;">
            <?php echo form_label(_l('select_invoice'), 'invoice_id'); ?>
            <select name="invoice_id" id="invoice_id" class="cases-form-select">
              <option value=""><?php echo _l('select_invoice'); ?></option>
            </select>
          </div>

          <!-- Contact -->
          <div class="form-group" id="contact_div" style="display: none;">
            <?php echo form_label(_l('select_contact'), 'contact_id'); ?>
            <select name="contact_id" id="contact_id" class="cases-form-select">
              <option value=""><?php echo _l('select_contact'); ?></option>
            </select>
          </div>

          <!-- 🏷️ Tag -->
          <div class="form-group">
            <?php echo form_label(_l('document_tag'), 'document_tag'); ?>
            <input type="text" name="document_tag" id="document_tag" class="cases-form-control" value="<?php echo htmlspecialchars($file->tag); ?>" placeholder="<?php echo _l('enter_document_tag'); ?>">
          </div>

          <button type="submit" id="saveBtn" class="btn btn-primary"><?php echo _l('save_changes'); ?></button>
          <a href="<?php echo admin_url('cases/documents/search'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  const csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
  const adminUrl = '<?php echo admin_url(); ?>';

  const selectedInvoiceId = '<?php echo $file->rel_type == 'invoice' ? $file->rel_id : ''; ?>';
  const selectedContactId = '<?php echo $file->rel_type == 'client' && $file->contact_id > 0 ? $file->contact_id : ''; ?>';

  const customerSelect = document.getElementById('customer_id');
  const invoiceSelect = document.getElementById('invoice_id');
  const contactSelect = document.getElementById('contact_id');

  const invoiceDiv = document.getElementById('invoice_div');
  const contactDiv = document.getElementById('contact_div');

  const relationRadios = document.querySelectorAll('input[name="relation_type"]');
  const form = document.querySelector('form');
  const saveBtn = document.querySelector('button[type="submit"]');

  function toggleDivs() {
    const type = getSelectedRelationType();
    invoiceDiv.style.display = (type === 'invoice') ? 'block' : 'none';
    contactDiv.style.display = (type === 'contact') ? 'block' : 'none';
  }

  function getSelectedRelationType() {
    const checked = document.querySelector('input[name="relation_type"]:checked');
    return checked ? checked.value : '';
  }

  function fetchDropdown(url, customerId, targetSelect, selectedId = '') {
    const params = new URLSearchParams();
    params.append('customer_id', customerId);
    params.append(csrfName, csrfHash);

    targetSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(adminUrl + url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: params.toString()
    })
    .then(res => res.text())
    .then(data => {
      targetSelect.innerHTML = data;
      if (selectedId) targetSelect.value = selectedId;
    });
  }

  function onCustomerChange() {
    const customerId = customerSelect.value;
    const relType = getSelectedRelationType();

    if (relType === 'invoice') {
      fetchDropdown('documents/get_invoices_by_customer', customerId, invoiceSelect);
    } else if (relType === 'contact') {
      fetchDropdown('documents/get_contacts_by_customer', customerId, contactSelect);
    }
  }

  function onRelationTypeChange() {
    toggleDivs();
    onCustomerChange();
  }

  function validateForm(event) {
    const relType = getSelectedRelationType();

    if (relType === 'invoice' && !invoiceSelect.value) {
      alert('<?php echo _l('select_invoice'); ?>');
      event.preventDefault();
      return;
    }

    if (relType === 'contact' && !contactSelect.value) {
      alert('<?php echo _l('select_contact'); ?>');
      event.preventDefault();
      return;
    }

    // Disable button during submission
    saveBtn.disabled = true;
    saveBtn.innerText = '<?php echo _l('saving'); ?>...';
  }

  // Events
  customerSelect.addEventListener('change', onCustomerChange);
  relationRadios.forEach(radio => radio.addEventListener('change', onRelationTypeChange));
  form.addEventListener('submit', validateForm);

  // Initial render
  toggleDivs();

  const customerId = customerSelect.value;
  const relType = getSelectedRelationType();

  if (relType === 'invoice') {
    fetchDropdown('documents/get_invoices_by_customer', customerId, invoiceSelect, selectedInvoiceId);
  } else if (relType === 'contact') {
    fetchDropdown('documents/get_contacts_by_customer', customerId, contactSelect, selectedContactId);
  }
});
</script>



<?php init_tail(); ?>
