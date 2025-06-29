<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Documents extends AdminController {

    public function __construct() {
        parent::__construct();
        $this->load->model('cases_model');
        
        // Ensure user has cases permissions (documents are now part of cases module)
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }
    }

    public function index() {
        $data['title'] = _l('document_manager');
        $data['activities'] = $this->cases_model->get_recent_document_activities();
        $this->load->view('documents/index', $data);
    }

    /**
 * Upload a document and attach it to the chosen entity.
 *
 * POST fields expected (same names you already use in the search form):
 * ─ customer_id        (int)   – always present
 * ─ search_type        (string)  client|contact|invoice|consultation|case|hearing
 * ─ contact_id         (int)     when search_type = contact
 * ─ invoice_id         (int)     when search_type = invoice
 * ─ consultation_id    (int)     when search_type = consultation
 * ─ case_id            (int)     when search_type = case|hearing
 * ─ hearing_id         (int)     when search_type = hearing
 * ─ document_tag       (string)  optional label
 * ─ document           (file)    the upload itself
 */
public function upload()
{
    // Check create permission
    if (!has_permission('cases', '', 'create')) {
        access_denied('cases');
    }
    
    if (!$this->input->post()) {
        // ——— render empty upload form ———
        $data['customers'] = $this->db->get(db_prefix().'clients')->result();
        $data['title']     = _l('upload_document');
        return $this->load->view('documents/upload_form', $data);
    }

    /* -------------------------------------------------------------
       1. Work out the relationship pair we will store in tblfiles
       ------------------------------------------------------------- */
    $type = $this->input->post('doc_owner_type');   // radio button value
    $cid  = (int) $this->input->post('customer_id');

    switch ($type) {
        case 'hearing':
            $relType    = 'hearing';
            $relId      = (int) $this->input->post('hearing_id');
            $contact_id = 0;
            break;

        case 'case':
            $relType    = 'case';
            $relId      = (int) $this->input->post('case_id');
            $contact_id = 0;
            break;

        case 'consultation':
            $relType    = 'consultation';
            $relId      = (int) $this->input->post('consultation_id');
            $contact_id = 0;
            break;

        case 'invoice':
            $relType    = 'invoice';
            $relId      = (int) $this->input->post('invoice_id');
            $contact_id = 0;
            break;

        case 'contact':
            $relType    = 'client';                // still stored against the client
            $relId      = $cid;
            $contact_id = (int) $this->input->post('contact_id');
            break;

        case 'client':        // fall-through
        default:
            $relType    = 'client';
            $relId      = $cid;
            $contact_id = 0;
    }

    /* -------------------------------------------------------------
       2. Handle the actual file upload
       ------------------------------------------------------------- */
    if (empty($_FILES['document']['name'])) {
        set_alert('danger', _l('no_file_selected'));
        return redirect(admin_url('cases/documents/upload'));
    }

    $config = [
        'upload_path'   => './uploads/documents/',
        'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
        'max_size'      => 20048,
    ];
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('document')) {
        set_alert('danger', $this->upload->display_errors('', ''));
        return redirect(admin_url('cases/documents/upload'));
    }

    $u            = $this->upload->data();
    $fileId       = $this->db->insert(db_prefix().'files', [
        'rel_id'     => $relId,
        'rel_type'   => $relType,
        'file_name'  => $u['file_name'],
        'filetype'   => $u['file_type'],
        'tag'        => $this->input->post('document_tag'),
        'staffid'    => get_staff_user_id(),
        'contact_id' => $contact_id,
        'dateadded'  => date('Y-m-d H:i:s'),
    ]);
    
    $fileId = $this->db->insert_id();   // ← call it separately
    

    /* -------------------------------------------------------------
       3. Activity log (optional – keep your helper)
       ------------------------------------------------------------- */
    $relation_name = $this->resolve_relation_name($relType, $relId, $contact_id);
    $this->cases_model->log_document_activity([
        'staff_id'    => get_staff_user_id(),
        'document_id' => $fileId,
        'rel_id'      => $relId,
        'rel_type'    => $relType,
        'message'     => 'Uploaded document: <strong>'.$u['file_name'].'</strong> for <em>'.$relation_name.'</em>',
    ]);

    set_alert('success', _l('document_uploaded_successfully'));
    redirect(admin_url('cases/documents'));
}


    public function download($id) {
        $file = $this->db->where('id', $id)->get(db_prefix() . 'files')->row();
        if (!$file) show_404();

        $file_path = FCPATH . 'uploads/documents/' . $file->file_name;
        if (!file_exists($file_path)) show_error('File not found');

        $relation_name = $this->resolve_relation_name($file->rel_type, $file->rel_id, $file->contact_id);
        $this->cases_model->log_document_activity([
            'staff_id'    => get_staff_user_id(),
            'document_id' => $file->id,
            'rel_id'      => $file->rel_id,
            'rel_type'    => $file->rel_type,
            'message'     => 'Downloaded document: <strong>' . $file->file_name . '</strong> from <em>' . $relation_name . '</em>',
        ]);

        $this->load->helper('download');
        force_download($file->file_name, file_get_contents($file_path));
    }

    public function delete($id) {
        // Check delete permission
        if (!has_permission('cases', '', 'delete')) {
            access_denied('cases');
        }
        
        $file = $this->db->where('id', $id)->get(db_prefix() . 'files')->row();
        if (!$file) {
            set_alert('danger', _l('document_not_found'));
            redirect(admin_url('cases/documents'));
        }

        $file_path = FCPATH . 'uploads/documents/' . $file->file_name;
        if (file_exists($file_path)) @unlink($file_path);
        $this->db->where('id', $id)->delete(db_prefix() . 'files');

        $relation_name = $this->resolve_relation_name($file->rel_type, $file->rel_id, $file->contact_id);
        $this->cases_model->log_document_activity([
            'staff_id'    => get_staff_user_id(),
            'document_id' => $file->id,
            'rel_id'      => $file->rel_id,
            'rel_type'    => $file->rel_type,
            'message'     => 'Deleted document: <strong>' . $file->file_name . '</strong> from <em>' . $relation_name . '</em>',
        ]);

        set_alert('success', _l('document_deleted_successfully'));
        redirect(admin_url('cases/documents/search'));
    }

    public function view($id) {
        $file = $this->db->where('id', $id)->get(db_prefix() . 'files')->row();
        if (!$file) show_404();

        $file_path = FCPATH . 'uploads/documents/' . $file->file_name;
        if (!file_exists($file_path)) show_error('File not found');

        $mime = mime_content_type($file_path);
        header('Content-Type: ' . $mime);
        readfile($file_path);
        exit;
    }

    public function get_invoices_by_customer() {
        $customer_id = $this->input->post('customer_id');
        $options = '<option value="">' . _l('select_invoice') . '</option>';

        if ($customer_id) {
            $invoices = $this->cases_model->get_invoices_by_customer($customer_id);
            foreach ($invoices as $invoice) {
                $display = !empty($invoice->formatted_number) ? $invoice->formatted_number : $invoice->number;
                $options .= '<option value="'.$invoice->id.'">'.$display.'</option>';
            }
        }

        echo $options;
    }

    public function get_contacts_by_customer() {
        $customer_id = $this->input->post('customer_id');
        $options = '<option value="">' . _l('select_contact') . '</option>';

        if ($customer_id) {
            $contacts = $this->cases_model->get_contacts_by_customer($customer_id);
            foreach ($contacts as $contact) {
                $options .= '<option value="'.$contact->id.'">'.$contact->firstname.' '.$contact->lastname.'</option>';
            }
        }

        echo $options;
    }

    /**
 * Documents->search()
 *
 * Accepts all filter inputs coming from the Blade/CI view and
 * passes them as an array to Documents_model::search_documents().
 */
public function search()
{
    // POST = run search │ GET = show empty form
    if (!$this->input->post()) {
        $data['customers'] = $this->db->get(db_prefix().'clients')->result();
        $data['title']     = _l('search_documents');
        return $this->load->view('documents/search_form', $data);
    }

    // ── Collect & sanitise incoming filters ───────────────
    $filters = [
        'search_type'      => $this->input->post('search_type',  true),   // all | customer | invoice | …
        'customer_id'      => $this->input->post('customer_id',  true),
        'invoice_id'       => $this->input->post('invoice_id',   true),
        'contact_id'       => $this->input->post('contact_id',   true),
        'consultation_id'  => $this->input->post('consultation_id', true),
        'case_id'          => $this->input->post('case_id',      true),
        'hearing_id'       => $this->input->post('hearing_id',   true),
        'document_tag'     => $this->input->post('document_tag', true),
    ];

    // Optionally: strip empty values so the model can loop easily
    $filters = array_filter($filters, static fn($v) => $v !== null && $v !== '');

    // ── Run search & render results ───────────────────────
    $data['results'] = $this->cases_model->search_documents($filters);
    $this->load->view('documents/search_results', $data);
}

    

    

    public function edit($id) {
        // Check edit permission
        if (!has_permission('cases', '', 'edit')) {
            access_denied('cases');
        }
        
        $file = $this->db->where('id', $id)->get(db_prefix() . 'files')->row();
    
        if (!$file) {
            show_404();
        }
    
        if ($this->input->post()) {
            $tag         = $this->input->post('document_tag');
            $invoice_id  = $this->input->post('invoice_id');
            $customer_id = $this->input->post('customer_id');
            $contact_id  = $this->input->post('contact_id');
            $relation_type = $this->input->post('relation_type');
            $rel_id      = ($relation_type === 'invoice') ? $invoice_id : $customer_id;
            $contact     = ($relation_type === 'contact' && !empty($contact_id)) ? $contact_id : 0;
    
            // 🧾 Update base info
            $update = [
                'rel_type'   => $relation_type === 'customer' ? 'client' : $relation_type,
                'rel_id'     => $rel_id,
                'tag'        => $tag,
                'contact_id' => $contact,
            ];
    
            $this->db->where('id', $id)->update(db_prefix() . 'files', $update);
    
            // 🔁 Replace file (if uploaded)
            if (!empty($_FILES['document']['name'])) {
                $config['upload_path']   = './uploads/documents/';
                $config['allowed_types'] = 'pdf|doc|docx|jpg|png';
                $config['max_size']      = 2048;
                $this->load->library('upload', $config);
    
                if ($this->upload->do_upload('document')) {
                    $upload_data = $this->upload->data();
                    $new_file    = $upload_data['file_name'];
    
                    // Delete old file
                    $old_path = FCPATH . 'uploads/documents/' . $file->file_name;
                    if (file_exists($old_path)) {
                        @unlink($old_path);
                    }
    
                    $this->db->where('id', $id)->update(db_prefix() . 'files', [
                        'file_name' => $new_file,
                        'filetype'  => $upload_data['file_type'],
                    ]);
    
                    $this->cases_model->log_document_activity([
                        'staff_id'    => get_staff_user_id(),
                        'document_id' => $id,
                        'rel_id'      => $rel_id,
                        'rel_type'    => $update['rel_type'],
                        'message'     => 'Replaced document file with <strong>' . $new_file . '</strong>',
                    ]);
                }
            }
    
            // 📘 Log metadata update
            $relation_name = $this->resolve_relation_name($update['rel_type'], $rel_id, $contact);
            $this->cases_model->log_document_activity([
                'staff_id'    => get_staff_user_id(),
                'document_id' => $id,
                'rel_id'      => $rel_id,
                'rel_type'    => $update['rel_type'],
                'message'     => 'Updated metadata for document: <strong>' . $file->file_name . '</strong> (Tag: ' . $tag . ') for <em>' . $relation_name . '</em>',
            ]);
    
            set_alert('success', _l('document_updated_successfully'));
            redirect(admin_url('cases/documents/search'));
        }
    
        $data['file']      = $file;
        $data['customers'] = $this->db->get(db_prefix().'clients')->result();
        $data['title']     = _l('edit_document');
        $this->load->view('documents/edit_form', $data);
    }
    

    private function resolve_relation_name($rel_type, $rel_id, $contact_id = null) {
        if (!empty($contact_id)) {
            $contact = $this->db->select('firstname, lastname, userid')
                ->where('id', $contact_id)
                ->get(db_prefix() . 'contacts')->row();
    
            if ($contact) {
                $client = $this->db->select('company')
                    ->where('userid', $contact->userid)
                    ->get(db_prefix() . 'clients')->row();
    
                return 'Contact: ' . $contact->firstname . ' ' . $contact->lastname .
                       ($client ? ' (' . $client->company . ')' : '');
            }
    
            return 'Contact #' . $contact_id;
        }
    
        if ($rel_type === 'client') {
            $client = $this->db->select('company')
                ->where('userid', $rel_id)
                ->get(db_prefix() . 'clients')->row();
    
            return $client ? 'Customer: ' . $client->company : 'Client #' . $rel_id;
        }
    
        if ($rel_type === 'invoice') {
            $invoice = $this->db->select('formatted_number')
                ->where('id', $rel_id)
                ->get(db_prefix() . 'invoices')->row();
    
            return $invoice ? 'Invoice #' . $invoice->formatted_number : 'Invoice #' . $rel_id;
        }
    
        return ucfirst($rel_type) . ' #' . $rel_id;
    }
    

                /**
             * Get consultations by client
             */
            public function get_consultations_by_client() {
                $client_id = $this->input->post('customer_id');
                $options = '<option value="">' . _l('select_consultation') . '</option>';

                if ($client_id) {
                    $this->db->where('client_id', $client_id);
                    $consultations = $this->db->get(db_prefix() . 'case_consultations')->result_array();
                    
                    foreach ($consultations as $consultation) {
                        $tag = !empty($consultation['tag']) ? $consultation['tag'] : 'Consultation #' . $consultation['id'];
                        $date = date('d-m-Y', strtotime($consultation['date_added']));
                        $options .= '<option value="' . $consultation['id'] . '">' . $tag . ' (' . $date . ')</option>';
                    }
                }

                echo $options;
            }

            /**
             * Get cases by client
             */
            public function get_cases_by_client() {
                $client_id = $this->input->post('customer_id');
                $options = '<option value="">' . _l('select_case') . '</option>';

                if ($client_id) {
                    $this->db->where('client_id', $client_id);
                    $cases = $this->db->get(db_prefix() . 'cases')->result_array();
                    
                    foreach ($cases as $case) {
                        $title = !empty($case['case_title']) ? $case['case_title'] : 'Case #' . $case['id'];
                        $number = !empty($case['case_number']) ? $case['case_number'] : '';
                        $options .= '<option value="' . $case['id'] . '">' . $title . ' (' . $number . ')</option>';
                    }
                }

                echo $options;
            }

            /**
             * Get hearings by case
             */
            public function get_hearings_by_case() {
                $case_id = $this->input->post('case_id');
                $options = '<option value="">' . _l('select_hearing') . '</option>';

                if ($case_id) {
                    $this->db->where('case_id', $case_id);
                    $this->db->order_by('date', 'DESC');
                    $hearings = $this->db->get(db_prefix() . 'hearings')->result_array();
                    
                    foreach ($hearings as $hearing) {
                        $date = date('d-m-Y', strtotime($hearing['date']));
                        $purpose = !empty($hearing['hearing_purpose']) ? $hearing['hearing_purpose'] : 'Hearing #' . $hearing['id'];
                        $options .= '<option value="' . $hearing['id'] . '">' . $date . ' - ' . $purpose . '</option>';
                    }
                }

                echo $options;
            }

    /**
     * Get recent document activity for dashboard
     */
    public function get_recent_activity() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        try {
            $activities = $this->cases_model->get_recent_document_activities(5);
            echo json_encode([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error loading document activities'
            ]);
        }
    }
    
}
