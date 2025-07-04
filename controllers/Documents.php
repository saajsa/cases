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
        $this->load->view('admin/documents/documents_index', $data);
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
        return $this->load->view('admin/documents/documents_upload_form', $data);
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

        case 'customer':      // New case for customer type
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
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => _l('no_file_selected')]);
            exit;
        }
        set_alert('danger', _l('no_file_selected'));
        return redirect(admin_url('cases/documents/upload'));
    }

    // Ensure upload directory exists
    $upload_path = './uploads/documents/';
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    $config = [
        'upload_path'   => $upload_path,
        'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png|txt',
        'max_size'      => 20048,
    ];
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('document')) {
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $this->upload->display_errors('', '')]);
            exit;
        }
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

    if ($this->input->is_ajax_request()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => _l('document_uploaded_successfully'),
            'file_id' => $fileId,
            'file_name' => $u['file_name']
        ]);
        exit;
    }
    
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
        return $this->load->view('admin/documents/documents_search_form', $data);
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
    $this->load->view('admin/documents/documents_search_results', $data);
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
        $this->load->view('admin/documents/documents_edit_form', $data);
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

    // ===============================
    // PHASE 5: CONTEXTUAL DOCUMENT LINKING
    // ===============================

    /**
     * Get contextual suggestions for document linking
     */
    public function get_contextual_suggestions() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        try {
            $case_id = $this->input->post('case_id');
            $document_type = $this->input->post('document_type');
            
            if (!$case_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Case ID is required'
                ]);
                return;
            }
            
            $suggestions = $this->cases_model->get_contextual_document_suggestions($case_id, $document_type);
            
            echo json_encode([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error getting contextual suggestions'
            ]);
        }
    }

    /**
     * Create contextual document link
     */
    public function create_contextual_link() {
        if (!has_permission('cases', '', 'edit')) {
            access_denied('cases');
        }
        
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        try {
            $document_id = $this->input->post('document_id');
            $link_type = $this->input->post('link_type');
            $link_id = $this->input->post('link_id');
            $metadata = $this->input->post('metadata') ?: [];
            
            if (!$document_id || !$link_type || !$link_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
                return;
            }
            
            $result = $this->cases_model->create_document_link($document_id, $link_type, $link_id, $metadata);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'link_id' => $result,
                    'message' => 'Contextual link created successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create contextual link'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error creating contextual link'
            ]);
        }
    }

    /**
     * Get document links
     */
    public function get_document_links($document_id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        try {
            $links = $this->cases_model->get_document_links($document_id);
            
            echo json_encode([
                'success' => true,
                'links' => $links
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error getting document links'
            ]);
        }
    }

    // ===============================
    // PHASE 5: ADVANCED SEARCH INTEGRATION
    // ===============================

    /**
     * Advanced document search with multiple criteria and relevance scoring
     */
    public function advanced_search() {
        if (!$this->input->post()) {
            // Show advanced search form
            $data['clients'] = $this->db->get(db_prefix().'clients')->result();
            $data['title'] = _l('advanced_document_search');
            return $this->load->view('admin/documents/documents_advanced_search_form', $data);
        }
        
        // Process advanced search
        $criteria = [
            'search_text' => $this->input->post('search_text'),
            'client_id' => $this->input->post('client_id'),
            'document_type' => $this->input->post('document_type'),
            'file_type' => $this->input->post('file_type'),
            'date_from' => $this->input->post('date_from'),
            'date_to' => $this->input->post('date_to'),
            'case_status' => $this->input->post('case_status'),
            'hearing_status' => $this->input->post('hearing_status'),
            'limit' => $this->input->post('limit') ?: 50
        ];
        
        $results = $this->cases_model->advanced_document_search($criteria);
        
        $data['results'] = $results;
        $data['criteria'] = $criteria;
        $data['title'] = _l('advanced_search_results');
        
        $this->load->view('admin/documents/documents_advanced_search_results', $data);
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function get_search_suggestions() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        try {
            $partial_text = $this->input->get('q');
            $limit = $this->input->get('limit') ?: 10;
            
            if (strlen($partial_text) < 2) {
                echo json_encode([
                    'success' => true,
                    'suggestions' => []
                ]);
                return;
            }
            
            $suggestions = $this->cases_model->get_search_suggestions($partial_text, $limit);
            
            echo json_encode([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error getting search suggestions'
            ]);
        }
    }

    // ===============================
    // PHASE 5: WORKFLOW ENHANCEMENTS
    // ===============================

    /**
     * Smart document upload with automatic categorization
     */
    public function smart_upload() {
        if (!has_permission('cases', '', 'create')) {
            access_denied('cases');
        }
        
        if (!$this->input->post()) {
            // Show smart upload form
            $data['customers'] = $this->db->get(db_prefix().'clients')->result();
            $data['title'] = _l('smart_document_upload');
            return $this->load->view('admin/documents/documents_smart_upload_form', $data);
        }

        // Process smart upload with automatic categorization
        $upload_result = $this->process_smart_upload();
        
        if ($upload_result['success']) {
            set_alert('success', $upload_result['message']);
            redirect(admin_url('cases/documents'));
        } else {
            set_alert('danger', $upload_result['message']);
            redirect(admin_url('cases/documents/smart_upload'));
        }
    }

    /**
     * Process smart upload with file analysis and auto-categorization
     */
    private function process_smart_upload() {
        try {
            // Basic upload validation
            if (empty($_FILES['document']['name'])) {
                return ['success' => false, 'message' => _l('no_file_selected')];
            }

            // Analyze file name for smart categorization
            $filename = $_FILES['document']['name'];
            $category_analysis = $this->analyze_document_category($filename);
            
            // Get suggested relationships based on analysis
            if (!empty($this->input->post('case_id'))) {
                $suggestions = $this->cases_model->get_contextual_document_suggestions(
                    $this->input->post('case_id'), 
                    $filename
                );
                
                // Auto-select best suggestion if confidence is high
                if (!empty($suggestions['smart_suggestions'])) {
                    foreach ($suggestions['smart_suggestions'] as $suggestion) {
                        if ($suggestion['confidence'] >= 90) {
                            // Auto-link to high-confidence suggestion
                            $category_analysis['suggested_link'] = $suggestion;
                            break;
                        }
                    }
                }
            }
            
            // Proceed with regular upload process
            $upload_config = [
                'upload_path' => './uploads/documents/',
                'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
                'max_size' => 20048,
            ];
            
            $this->load->library('upload', $upload_config);
            
            if (!$this->upload->do_upload('document')) {
                return ['success' => false, 'message' => $this->upload->display_errors('', '')];
            }
            
            $upload_data = $this->upload->data();
            
            // Determine relationship based on smart analysis
            $rel_type = $this->input->post('doc_owner_type') ?: $category_analysis['suggested_type'];
            $rel_id = $this->get_relation_id_from_form($rel_type);
            
            // Insert file record
            $file_data = [
                'rel_id' => $rel_id,
                'rel_type' => $rel_type,
                'file_name' => $upload_data['file_name'],
                'filetype' => $upload_data['file_type'],
                'tag' => $this->input->post('document_tag') ?: $category_analysis['suggested_tag'],
                'staffid' => get_staff_user_id(),
                'dateadded' => date('Y-m-d H:i:s'),
            ];
            
            $this->db->insert(db_prefix() . 'files', $file_data);
            $file_id = $this->db->insert_id();
            
            // Create contextual links if suggested
            if (!empty($category_analysis['suggested_link'])) {
                $this->cases_model->create_document_link(
                    $file_id,
                    $category_analysis['suggested_link']['type'],
                    $category_analysis['suggested_link']['id'],
                    ['auto_linked' => true, 'confidence' => $category_analysis['suggested_link']['confidence']]
                );
            }
            
            // Log activity
            $this->cases_model->log_document_activity([
                'staff_id' => get_staff_user_id(),
                'document_id' => $file_id,
                'rel_id' => $rel_id,
                'rel_type' => $rel_type,
                'message' => 'Smart uploaded document: <strong>' . $upload_data['file_name'] . '</strong>' . 
                           ($category_analysis['confidence'] > 80 ? ' (Auto-categorized)' : '')
            ]);
            
            return [
                'success' => true, 
                'message' => _l('document_uploaded_successfully') . 
                           ($category_analysis['confidence'] > 80 ? ' with smart categorization' : ''),
                'file_id' => $file_id,
                'analysis' => $category_analysis
            ];
            
        } catch (Exception $e) {
            log_message('error', 'Smart upload error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()];
        }
    }

    /**
     * Analyze document filename and content for smart categorization
     */
    private function analyze_document_category($filename) {
        $analysis = [
            'suggested_type' => 'case',
            'suggested_tag' => '',
            'confidence' => 0,
            'reasoning' => []
        ];
        
        $filename_lower = strtolower($filename);
        
        // Pattern matching for different document types
        $patterns = [
            'petition' => ['petition', 'application', 'writ', 'plea'],
            'order' => ['order', 'judgment', 'decree', 'ruling'],
            'evidence' => ['evidence', 'exhibit', 'proof', 'document'],
            'notice' => ['notice', 'summons', 'citation', 'service'],
            'affidavit' => ['affidavit', 'sworn', 'statement', 'declaration'],
            'contract' => ['contract', 'agreement', 'deed', 'lease']
        ];
        
        foreach ($patterns as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($filename_lower, $keyword) !== false) {
                    $analysis['suggested_tag'] = ucfirst($category) . ' Document';
                    $analysis['confidence'] += 20;
                    $analysis['reasoning'][] = "Filename contains '$keyword'";
                }
            }
        }
        
        // Date pattern analysis
        if (preg_match('/\d{4}[-_]\d{2}[-_]\d{2}/', $filename)) {
            $analysis['confidence'] += 10;
            $analysis['reasoning'][] = "Contains date pattern";
        }
        
        // Court case number pattern
        if (preg_match('/\b\d+\/\d+\b/', $filename)) {
            $analysis['confidence'] += 15;
            $analysis['reasoning'][] = "Contains case number pattern";
        }
        
        // File extension analysis
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($extension === 'pdf') {
            $analysis['confidence'] += 5;
            $analysis['reasoning'][] = "PDF format typical for legal documents";
        }
        
        return $analysis;
    }

    /**
     * Get relation ID based on form input and relation type
     */
    private function get_relation_id_from_form($rel_type) {
        switch ($rel_type) {
            case 'case':
                return (int)$this->input->post('case_id');
            case 'hearing':
                return (int)$this->input->post('hearing_id');
            case 'consultation':
                return (int)$this->input->post('consultation_id');
            case 'client':
                return (int)$this->input->post('customer_id');
            case 'invoice':
                return (int)$this->input->post('invoice_id');
            default:
                return (int)$this->input->post('customer_id');
        }
    }

    // ===============================
    // PHASE 5: REPORTING INTEGRATION
    // ===============================

    /**
     * Generate document analytics report
     */
    public function analytics_report() {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }
        
        $data['title'] = _l('document_analytics_report');
        $data['analytics'] = $this->get_document_analytics();
        
        $this->load->view('admin/documents/documents_analytics_report', $data);
    }

    /**
     * Get comprehensive document analytics
     */
    private function get_document_analytics() {
        $analytics = [];
        
        try {
            // Document upload trends (last 12 months)
            $analytics['upload_trends'] = $this->get_upload_trends();
            
            // Document type distribution
            $analytics['type_distribution'] = $this->get_document_type_distribution();
            
            // Client document statistics
            $analytics['client_stats'] = $this->get_client_document_stats();
            
            // Case document completion rates
            $analytics['case_completion'] = $this->get_case_document_completion();
            
            // File type analytics
            $analytics['file_types'] = $this->get_file_type_analytics();
            
            // Activity patterns
            $analytics['activity_patterns'] = $this->get_activity_patterns();
            
        } catch (Exception $e) {
            log_message('error', 'Error generating document analytics: ' . $e->getMessage());
        }
        
        return $analytics;
    }

    /**
     * Get document upload trends for the last 12 months
     */
    private function get_upload_trends() {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month_start = date('Y-m-01', strtotime("-$i months"));
            $month_end = date('Y-m-t', strtotime("-$i months"));
            $month_label = date('M Y', strtotime("-$i months"));
            
            $this->db->where('DATE(dateadded) >=', $month_start);
            $this->db->where('DATE(dateadded) <=', $month_end);
            $count = $this->db->count_all_results(db_prefix() . 'files');
            
            $trends[] = [
                'month' => $month_label,
                'count' => $count,
                'date' => $month_start
            ];
        }
        
        return $trends;
    }

    /**
     * Get document type distribution
     */
    private function get_document_type_distribution() {
        $this->db->select('rel_type, COUNT(*) as count');
        $this->db->from(db_prefix() . 'files');
        $this->db->group_by('rel_type');
        $this->db->order_by('count', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get client document statistics
     */
    private function get_client_document_stats() {
        $this->db->select('cl.company, COUNT(f.id) as document_count');
        $this->db->from(db_prefix() . 'files f');
        $this->db->join(db_prefix() . 'cases c', 'c.id = f.rel_id AND f.rel_type = "case"', 'left');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id OR (f.rel_type = "client" AND cl.userid = f.rel_id)', 'left');
        $this->db->where('cl.company IS NOT NULL');
        $this->db->group_by('cl.userid');
        $this->db->order_by('document_count', 'DESC');
        $this->db->limit(10);
        
        return $this->db->get()->result();
    }

    /**
     * Get case document completion rates
     */
    private function get_case_document_completion() {
        $this->db->select('c.case_title, c.case_number, COUNT(f.id) as document_count, c.status');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->join(db_prefix() . 'files f', 'f.rel_type = "case" AND f.rel_id = c.id', 'left');
        $this->db->group_by('c.id');
        $this->db->order_by('document_count', 'DESC');
        $this->db->limit(20);
        
        return $this->db->get()->result();
    }

    /**
     * Get file type analytics
     */
    private function get_file_type_analytics() {
        $this->db->select('filetype, COUNT(*) as count');
        $this->db->from(db_prefix() . 'files');
        $this->db->group_by('filetype');
        $this->db->order_by('count', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get activity patterns
     */
    private function get_activity_patterns() {
        // Get activity by day of week
        $this->db->select('DAYNAME(dateadded) as day_name, COUNT(*) as count');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('dateadded >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->group_by('DAYOFWEEK(dateadded)');
        $this->db->order_by('DAYOFWEEK(dateadded)');
        
        $daily_patterns = $this->db->get()->result();
        
        // Get activity by hour
        $this->db->select('HOUR(dateadded) as hour, COUNT(*) as count');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('dateadded >=', date('Y-m-d', strtotime('-7 days')));
        $this->db->group_by('HOUR(dateadded)');
        $this->db->order_by('hour');
        
        $hourly_patterns = $this->db->get()->result();
        
        return [
            'daily' => $daily_patterns,
            'hourly' => $hourly_patterns
        ];
    }
    
}
