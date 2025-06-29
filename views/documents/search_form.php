<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- ========================================= -->
<!--  Custom UI Enhancements (CSS only)        -->
<!--  Functional logic & selectors untouched   -->
<!-- ========================================= -->
<style>
  /* ── Panel Wrapper */
  .panel_s {
    border: 0;
    border-radius: .5rem;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.05);
  }
  .panel_s > .panel-body { padding: 2rem; }

  /* ── Global Panels */
  .panel.panel-default {
    border: 0;
    border-radius: .5rem;
    box-shadow: 0 .25rem .75rem rgba(0,0,0,.05);
    margin-bottom: 1.5rem;
  }
  .panel.panel-default > .panel-heading {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-top-left-radius: .5rem;
    border-top-right-radius: .5rem;
  }
  .panel.panel-default > .panel-heading .panel-title { font-weight: 600; }
  .panel.panel-default > .panel-body { padding: 1.5rem; }

  /* ── Navigation Tabs */
  .nav-tabs { border-bottom: 0; }
  .nav-tabs .nav-link {
    border: 0;
    color: #6c757d;
  }
  .nav-tabs .nav-link:hover { color: #0d6efd; }
  .nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 600;
    border-bottom: 2px solid #0d6efd;
  }

  /* ── Form Controls */
  .form-group { margin-bottom: 1.25rem; }
  .form-group > label { font-weight: 600; margin-bottom: .5rem; }
  .form-control { border-radius: .3rem; }
  .btn-primary { padding: .5rem 1.75rem; font-weight: 600; }

  /* ── Radio / Toggle Buttons */
  .btn-group .btn {
    border-radius: 2rem;
    padding: .4rem 1.1rem;
  }
  .btn-group .btn.active {
    background: #0d6efd;
    color: #fff;
  }

  /* ── Icon spacing */
  .form-group i { margin-right: .4rem; }
</style>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s shadow-sm">
          <div class="panel-body">
            <!-- 🧭 Navigation Tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
              <li class="nav-item">
                <a class="nav-link <?php echo $this->uri->segment(3) == 'upload' ? 'active' : ''; ?>" href="<?php echo admin_url('cases/documents/upload'); ?>">
                  <i class="fa fa-upload"></i> <?php echo _l('upload_document'); ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $this->uri->segment(3) == 'search' ? 'active' : ''; ?>" href="<?php echo admin_url('cases/documents/search'); ?>">
                  <i class="fa fa-search"></i> <?php echo _l('search_documents'); ?>
                </a>
              </li>
            </ul>

            <!-- === Search Tab (always active on this view) === -->
            <h4 class="fw-bold text-secondary mb-4"><i class="fa fa-filter"></i> <?php echo _l('search_documents'); ?></h4>

            <?php echo form_open(admin_url('cases/documents/search'), ['id' => 'document-search-form']); ?>

              <!-- 📂 Document Relationship Type -->
              <div class="panel panel-default">
                <div class="panel-heading"><h4 class="panel-title"><?php echo _l('filter_by_type'); ?></h4></div>
                <div class="panel-body">
                  <div class="btn-group w-100 flex-wrap" data-toggle="buttons">
                    <label class="btn btn-light active mb-2">
                      <input type="radio" name="search_type" value="all" checked> <i class="fa fa-globe"></i> <?php echo _l('all'); ?>
                    </label>
                    <label class="btn btn-light mb-2">
                      <input type="radio" name="search_type" value="customer"> <i class="fa fa-building"></i> <?php echo _l('customer'); ?>
                    </label>
                    <label class="btn btn-light mb-2">
                      <input type="radio" name="search_type" value="invoice"> <i class="fa fa-file-text-o"></i> <?php echo _l('invoice'); ?>
                    </label>
                    <label class="btn btn-light mb-2">
                      <input type="radio" name="search_type" value="contact"> <i class="fa fa-user"></i> <?php echo _l('contact'); ?>
                    </label>
                    <label class="btn btn-light mb-2">
                      <input type="radio" name="search_type" value="consultation"> <i class="fa fa-comments"></i> <?php echo _l('consultation'); ?>
                    </label>
                    <label class="btn btn-light mb-2">
                      <input type="radio" name="search_type" value="case"> <i class="fa fa-briefcase"></i> <?php echo _l('case'); ?>
                    </label>
                    <label class="btn btn-light mb-2">
                      <input type="radio" name="search_type" value="hearing"> <i class="fa fa-gavel"></i> <?php echo _l('hearing'); ?>
                    </label>
                  </div>
                </div>
              </div>

              <!-- 🎯 Selection Criteria -->
              <div class="panel panel-default">
                <div class="panel-heading"><h4 class="panel-title"><?php echo _l('selection_criteria'); ?></h4></div>
                <div class="panel-body">

                  <!-- Customer -->
                  <div class="form-group">
                    <label for="customer_id"><i class="fa fa-building-o"></i> <?php echo _l('select_customer'); ?></label>
                    <select name="customer_id" id="customer_id" class="form-control selectpicker" data-live-search="true">
                      <option value=""><?php echo _l('select_customer'); ?></option>
                      <?php foreach ($customers as $customer) { ?>
                        <option value="<?php echo htmlspecialchars($customer->userid); ?>">
                          <?php echo htmlspecialchars($customer->company); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                  <!-- Dynamic Relationship Options -->
                  <div id="relationship-options">
                    <!-- Invoice -->
                    <div class="form-group search-option" id="invoice_div">
                      <label for="invoice_id"><i class="fa fa-file-text-o"></i> <?php echo _l('select_invoice'); ?></label>
                      <select name="invoice_id" id="invoice_id" class="form-control selectpicker" data-live-search="true">
                        <option value=""><?php echo _l('select_invoice'); ?></option>
                      </select>
                    </div>

                    <!-- Contact -->
                    <div class="form-group search-option" id="contact_div" style="display:none;">
                      <label for="contact_id"><i class="fa fa-user"></i> <?php echo _l('select_contact'); ?></label>
                      <select name="contact_id" id="contact_id" class="form-control selectpicker" data-live-search="true">
                        <option value=""><?php echo _l('select_contact'); ?></option>
                      </select>
                    </div>

                    <!-- Consultation -->
                    <div class="form-group search-option" id="consultation_div" style="display:none;">
                      <label for="consultation_id"><i class="fa fa-comments"></i> <?php echo _l('select_consultation'); ?></label>
                      <select name="consultation_id" id="consultation_id" class="form-control selectpicker" data-live-search="true">
                        <option value=""><?php echo _l('select_consultation'); ?></option>
                      </select>
                    </div>

                    <!-- Case -->
                    <div class="form-group search-option" id="case_div" style="display:none;">
                      <label for="case_id"><i class="fa fa-briefcase"></i> <?php echo _l('select_case'); ?></label>
                      <select name="case_id" id="case_id" class="form-control selectpicker" data-live-search="true">
                        <option value=""><?php echo _l('select_case'); ?></option>
                      </select>
                    </div>

                    <!-- Hearing -->
                    <div class="form-group search-option" id="hearing_div" style="display:none;">
                      <label for="hearing_id"><i class="fa fa-gavel"></i> <?php echo _l('select_hearing'); ?></label>
                      <select name="hearing_id" id="hearing_id" class="form-control selectpicker" data-live-search="true">
                        <option value=""><?php echo _l('select_hearing'); ?></option>
                      </select>
                    </div>
                  </div>

                  <!-- Tag -->
                  <div class="form-group mt-3">
                    <label for="document_tag"><i class="fa fa-tag"></i> <?php echo _l('document_tag'); ?></label>
                    <input type="text" name="document_tag" id="document_tag" class="form-control" placeholder="<?php echo _l('enter_document_tag'); ?>">
                  </div>

                </div><!-- /panel-body -->
              </div><!-- /panel -->

              <!-- 🔍 Search Button -->
              <button type="submit" id="search-btn" class="btn btn-primary">
                <i class="fa fa-search"></i> <?php echo _l('search'); ?>
              </button>

            <?php echo form_close(); ?>
          </div><!-- /panel-body -->
        </div><!-- /panel_s -->
      </div>
    </div>
  </div>
</div>

<!-- ===================================================================== -->
<!--  JS (intact from original, only minified whitespace where safe)       -->
<!-- ===================================================================== -->
<script>
(function () {
  const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
  const admin_url = "<?php echo admin_url(); ?>";

  document.addEventListener("DOMContentLoaded", function () {
    if ($.fn.selectpicker) {
      $('.selectpicker').selectpicker({ showSubtext: true });
    }

    const customerSelect = document.getElementById('customer_id');
    const invoiceSelect = document.getElementById('invoice_id');
    const contactSelect = document.getElementById('contact_id');
    const consultationSelect = document.getElementById('consultation_id');
    const caseSelect = document.getElementById('case_id');
    const hearingSelect = document.getElementById('hearing_id');

    const radioButtons = document.querySelectorAll('input[name="search_type"]');
    const divs = {
      invoice: document.getElementById('invoice_div'),
      contact: document.getElementById('contact_div'),
      consultation: document.getElementById('consultation_div'),
      case: document.getElementById('case_div'),
      hearing: document.getElementById('hearing_div')
    };

    function toggleDivs(type) {
      Object.keys(divs).forEach(k => divs[k].style.display = 'none');
      if (type === 'invoice') divs.invoice.style.display = 'block';
      else if (type === 'contact') divs.contact.style.display = 'block';
      else if (type === 'consultation') divs.consultation.style.display = 'block';
      else if (type === 'case') divs.case.style.display = 'block';
      else if (type === 'hearing') { divs.case.style.display = 'block'; divs.hearing.style.display = 'block'; }
    }

    document.querySelectorAll('.btn-group label.btn').forEach(label => {
      label.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        if (radio) { radio.checked = true; toggleDivs(radio.value); }
      });
    });

    radioButtons.forEach(r => r.addEventListener('change', function() { toggleDivs(this.value); }));

    toggleDivs(document.querySelector('input[name="search_type"]:checked').value);

    customerSelect.addEventListener("change", function () {
      const customerId = this.value; if (!customerId) return;

      function post(url, body, selectEl) {
        fetch(url, { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: new URLSearchParams(body).toString() })
          .then(r => r.text())
          .then(html => { selectEl.innerHTML = html; if ($.fn.selectpicker) $(selectEl).selectpicker('refresh'); })
          .catch(err => console.error(url, err));
      }
      const body = id => ({ [id]: customerId, [csrfName]: csrfHash });
      post(admin_url+"documents/get_invoices_by_customer", body('customer_id'), invoiceSelect);
      post(admin_url+"documents/get_contacts_by_customer", body('customer_id'), contactSelect);
      post(admin_url+"documents/get_consultations_by_client", body('customer_id'), consultationSelect);
      post(admin_url+"documents/get_cases_by_client", body('customer_id'), caseSelect);
    });

    if (caseSelect) {
      caseSelect.addEventListener('change', function() {
        if (document.querySelector('input[name="search_type"]:checked').value !== 'hearing') return;
        fetch(admin_url+"documents/get_hearings_by_case", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: new URLSearchParams({ case_id: this.value, [csrfName]: csrfHash }).toString() })
          .then(r => r.text())
          .then(html => { hearingSelect.innerHTML = html; if ($.fn.selectpicker) $(hearingSelect).selectpicker('refresh'); })
          .catch(err => console.error("hearings", err));
      });
    }
  });
})();
</script>

<?php init_tail(); ?>
