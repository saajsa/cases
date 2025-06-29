<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cases_model extends App_Model
{
    public function add_consultation($data)
    {
        // Load security helper
        $this->load->helper('modules/cases/helpers/security_helper');
        
        // Validate required fields
        if (empty($data['client_id']) || empty($data['note'])) {
            log_message('error', 'Missing required fields for consultation');
            return false;
        }
        
        // Validate and sanitize input data
        $validated_data = [];
        
        // Validate client_id
        $validated_data['client_id'] = cases_validate_integer($data['client_id'], 1);
        if ($validated_data['client_id'] === false) {
            log_message('error', 'Invalid client_id for consultation');
            return false;
        }
        
        // Validate contact_id (optional)
        if (!empty($data['contact_id'])) {
            $validated_data['contact_id'] = cases_validate_integer($data['contact_id'], 1);
            if ($validated_data['contact_id'] === false) {
                log_message('error', 'Invalid contact_id for consultation');
                return false;
            }
        }
        
        // Sanitize text fields
        $validated_data['note'] = cases_sanitize_string($data['note'], 5000, false);
        if (empty($validated_data['note'])) {
            log_message('error', 'Invalid or empty note for consultation');
            return false;
        }
        
        $validated_data['tag'] = cases_sanitize_string($data['tag'] ?? '', 100, false);
        
        // Validate phase
        $allowed_phases = ['consultation', 'litigation'];
        $validated_data['phase'] = in_array($data['phase'] ?? 'consultation', $allowed_phases) 
            ? $data['phase'] : 'consultation';
        
        // Add system fields
        $validated_data['staff_id'] = get_staff_user_id();
        $validated_data['date_added'] = date('Y-m-d H:i:s');

        try {
            $this->db->insert(db_prefix().'case_consultations', $validated_data);

            if ($this->db->affected_rows() > 0) {
                $consultation_id = $this->db->insert_id();
                cases_log_security_event('Consultation added', ['consultation_id' => $consultation_id], 'info');
                
                // Log consultation creation activity
                $this->log_document_activity([
                    'rel_id' => $consultation_id,
                    'rel_type' => 'consultation',
                    'message' => 'Consultation created: <strong>' . ($validated_data['tag'] ?: 'New Consultation') . '</strong>',
                    'staff_id' => get_staff_user_id()
                ]);
                
                return $consultation_id;
            }
        } catch (Exception $e) {
            log_message('error', 'Database error adding consultation: ' . $e->getMessage());
            cases_log_security_event('Failed consultation insertion', ['error' => $e->getMessage()], 'error');
        }

        log_message('error', 'Failed to add consultation: ' . json_encode($validated_data));
        return false;
    }

    public function get_consultations()
    {
        $accessible_consultation_ids = cases_get_accessible_resources('consultation', 'view');
        
        if (empty($accessible_consultation_ids)) {
            return [];
        }

        $query = $this->db->select(
                            'cc.id, cc.client_id, cc.contact_id, cc.tag, cc.note, cc.date_added, cc.phase,
                             c.company AS client_name, ct.firstname, ct.lastname')
                        ->from(db_prefix().'case_consultations AS cc')
                        ->join(db_prefix().'clients AS c', 'c.userid = cc.client_id', 'left')
                        ->join(db_prefix().'contacts AS ct', 'ct.id = cc.contact_id', 'left')
                        ->where_in('cc.id', $accessible_consultation_ids)
                        ->get();

        if (!$query) {
            log_message('error', 'DB Error (consultations): ' . $this->db->error()['message']);
            return [];
        }

        return $query->result_array();
    }

    public function get_all_cases()
    {
        $accessible_case_ids = cases_get_accessible_resources('case', 'view');
        
        if (empty($accessible_case_ids)) {
            return [];
        }

        $query = $this->db->select('c.*, 
                                    r.court_no, 
                                    r.judge_name, 
                                    ct.name as court_name')
                          ->from(db_prefix().'cases AS c')
                          ->join(db_prefix().'court_rooms AS r', 'r.id = c.court_room_id', 'left')
                          ->join(db_prefix().'courts AS ct', 'ct.id = r.court_id', 'left')
                          ->where_in('c.id', $accessible_case_ids)
                          ->get();

        if (!$query) {
            log_message('error', 'DB Error (cases): ' . $this->db->error()['message']);
            return [];
        }

        $results = $query->result_array();

        // Optional: format a display field for the frontend
        foreach ($results as &$row) {
            $row['court_display'] = 'Court ' . $row['court_no'] . ' – Hon\'ble ' . $row['judge_name'] . ', ' . $row['court_name'];
        }

        return $results;
    }



    public function create_case($data)
    {
        if (empty($data['consultation_id']) || !$this->get_consultation_by_id($data['consultation_id'])) {
            log_message('error', 'Invalid consultation ID provided for case creation.');
            return false;
        }

        $data['created_by'] = get_staff_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix().'cases', $data);

        if ($this->db->affected_rows() > 0) {
            $case_id = $this->db->insert_id();
            
            // Log case creation activity
            $this->log_document_activity([
                'rel_id' => $case_id,
                'rel_type' => 'case',
                'message' => 'Case created: <strong>' . ($data['case_title'] ?? 'New Case') . '</strong>',
                'staff_id' => get_staff_user_id()
            ]);
            
            return $case_id;
        }

        log_message('error', 'Failed to create case: ' . json_encode($data));
        return false;
    }

    public function get_note_by_id($id)
    {
        if (!is_numeric($id)) return null;
        return $this->db->get_where(db_prefix().'case_consultations', ['id' => $id])->row_array();
    }

    public function get_consultation_by_id($id)
    {
        if (!is_numeric($id)) return null;
        return $this->db->get_where(db_prefix().'case_consultations', ['id' => $id])->row_array();
    }

    public function update_phase($id, $phase)
    {
        if (!is_numeric($id) || empty($phase)) return false;

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'case_consultations', ['phase' => $phase]);

        return $this->db->affected_rows() > 0;
    }

    public function update_consultation($id, $data)
    {
        if (!is_numeric($id)) return false;

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'case_consultations', $data);

        $affected = $this->db->affected_rows() > 0;
        
        if ($affected) {
            // Log consultation update activity
            $consultation = $this->get_consultation_by_id($id);
            $this->log_document_activity([
                'rel_id' => $id,
                'rel_type' => 'consultation',
                'message' => 'Consultation updated: <strong>' . ($consultation['tag'] ?? 'Consultation #' . $id) . '</strong>',
                'staff_id' => get_staff_user_id()
            ]);
        }

        return $affected;
    }

    public function delete_consultation($id)
    {
        if (!is_numeric($id)) return false;

        $consultation = $this->get_consultation_by_id($id);
        if ($consultation && $consultation['phase'] === PHASE_LITIGATION) {
            log_message('error', 'Attempted to delete consultation already upgraded to litigation.');
            return false;
        }

        $this->db->delete(db_prefix().'case_consultations', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }


    public function count_all()
    {
        return $this->db->count_all_results(db_prefix() . 'cases');
    }

    public function count_by_status($status_label)
    {
        $this->db->where('status_label', $status_label); // adjust to match your status system
        return $this->db->count_all_results(db_prefix() . 'cases');
    }

    public function count_pending_hearings()
    {
        $this->db->where('hearing_status', 'Pending'); // assuming you track hearings
        return $this->db->count_all_results(db_prefix() . 'case_hearings');
    }

    public function get_consultations_with_names()
{
    $this->db->select('
        ' . db_prefix() . 'case_consultations.id,
        ' . db_prefix() . 'case_consultations.client_id,
        ' . db_prefix() . 'case_consultations.contact_id,
        ' . db_prefix() . 'case_consultations.tag,
        ' . db_prefix() . 'case_consultations.note,
        ' . db_prefix() . 'case_consultations.date_added,
        ' . db_prefix() . 'case_consultations.phase,
        ' . db_prefix() . 'clients.company as client_name,
        CONCAT(' . db_prefix() . 'contacts.firstname, " ", ' . db_prefix() . 'contacts.lastname) as contact_name
    ');
    $this->db->from(db_prefix() . 'case_consultations');
    $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'case_consultations.client_id', 'left');
    $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.id = ' . db_prefix() . 'case_consultations.contact_id', 'left');
    $this->db->order_by(db_prefix() . 'case_consultations.id', 'desc');
    
    return $this->db->get()->result_array();
}

public function get_all_cases_with_details()
{
    $this->db->select('
        ' . db_prefix() . 'cases.id,
        ' . db_prefix() . 'cases.consultation_id,
        ' . db_prefix() . 'cases.case_title,
        ' . db_prefix() . 'cases.case_number,
        ' . db_prefix() . 'cases.date_filed,
        ' . db_prefix() . 'cases.date_created,
        ' . db_prefix() . 'clients.company as client_name,
        CONCAT(' . db_prefix() . 'contacts.firstname, " ", ' . db_prefix() . 'contacts.lastname) as contact_name,
        tblcourts.name as court_name,
        tblcourt_rooms.court_no,
        tblcourt_rooms.judge_name,
        CONCAT("Court ", tblcourt_rooms.court_no, " - Hon\'ble ", tblcourt_rooms.judge_name) as court_room_info,
        CONCAT(tblcourts.name, " - Court ", tblcourt_rooms.court_no) as court_display,
        CONCAT("CONS-", ' . db_prefix() . 'cases.consultation_id) as consultation_reference
    ');
    $this->db->from(db_prefix() . 'cases');
    $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'cases.client_id', 'left');
    $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.id = ' . db_prefix() . 'cases.contact_id', 'left');
    $this->db->join('tblcourt_rooms', 'tblcourt_rooms.id = ' . db_prefix() . 'cases.court_room_id', 'left');
    $this->db->join('tblcourts', 'tblcourts.id = tblcourt_rooms.court_id', 'left');
    $this->db->join(db_prefix() . 'case_consultations', db_prefix() . 'case_consultations.id = ' . db_prefix() . 'cases.consultation_id', 'left');
    $this->db->order_by(db_prefix() . 'cases.id', 'desc');
    
    $result = $this->db->get()->result_array();
    
    // Process results to handle any null values or formatting issues
    foreach ($result as &$row) {
        // Make sure to handle cases where court information might be missing
        if (empty($row['court_display'])) {
            $row['court_display'] = 'Court not specified';
        }
        
        // Ensure consultation reference is formatted
        if (empty($row['consultation_reference'])) {
            $row['consultation_reference'] = 'CONS-' . $row['consultation_id'];
        }
    }
    
    return $result;
}

    // ========================================================================
    // DOCUMENT MANAGEMENT METHODS - Integrated from Documents Module
    // ========================================================================

    /**
     * Log activity for file actions (upload, delete, etc.)
     * Enhanced version with case-specific context
     */
    public function log_document_activity(array $data)
    {
        $insert = [
            'staff_id'    => $data['staff_id'] ?? get_staff_user_id(),
            'document_id' => $data['document_id'] ?? null,
            'rel_id'      => $data['rel_id'] ?? null,
            'rel_type'    => $data['rel_type'] ?? null,
            'message'     => $data['message'],
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('tblfile_activity_log', $insert);
        
        // Also log to main activity log for cases if it's case-related
        if (in_array($data['rel_type'] ?? '', ['case', 'hearing', 'consultation'])) {
            $this->log_case_activity($data);
        }
    }

    /**
     * Log general case activity (for integration with existing cases workflow)
     */
    private function log_case_activity(array $data)
    {
        // Extract case information for activity log
        $case_id = null;
        $description = $data['message'];
        
        switch ($data['rel_type']) {
            case 'case':
                $case_id = $data['rel_id'];
                break;
            case 'hearing':
                // Get case_id from hearing
                $hearing = $this->db->select('case_id')->where('id', $data['rel_id'])->get(db_prefix() . 'hearings')->row();
                $case_id = $hearing ? $hearing->case_id : null;
                break;
            case 'consultation':
                // Get case_id from consultation if converted to case
                $case = $this->db->select('id')->where('consultation_id', $data['rel_id'])->get(db_prefix() . 'cases')->row();
                $case_id = $case ? $case->id : null;
                break;
        }
        
        if ($case_id) {
            // Log to general activity log if needed
            // This can be enhanced based on your existing activity logging system
            log_message('info', "Document activity for case {$case_id}: " . strip_tags($description));
        }
    }

    /**
     * Fetch recent document activity logs
     */
    public function get_recent_document_activities($limit = 10)
    {
        $this->db->select('l.*, CONCAT(s.firstname, " ", s.lastname) AS staff_name');
        $this->db->from('tblfile_activity_log l');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = l.staff_id', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    /**
     * Get recent document activities for a specific case
     */
    public function get_case_document_activities($case_id, $limit = 20)
    {
        $this->db->select('l.*, CONCAT(s.firstname, " ", s.lastname) AS staff_name');
        $this->db->from('tblfile_activity_log l');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = l.staff_id', 'left');
        
        // Get activities for case and its hearings
        $this->db->group_start();
        $this->db->where(['l.rel_type' => 'case', 'l.rel_id' => $case_id]);
        $this->db->or_where("l.rel_type = 'hearing' AND l.rel_id IN (SELECT id FROM " . db_prefix() . "hearings WHERE case_id = $case_id)");
        $this->db->group_end();
        
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    /**
     * Search for client-based documents by company name
     */
    public function search_documents_by_client($searchTerm) 
    {
        $this->db->like('company', $searchTerm);
        $client = $this->db->get(db_prefix().'clients')->row();
        if ($client) {
            $clientId = $client->userid;
            $this->db->where('rel_type', 'client');
            $this->db->where('rel_id', $clientId);
            return $this->db->get(db_prefix().'files')->result();
        }
        return array();
    }

    /**
     * Search for invoice-based documents
     */
    public function search_documents_by_invoice($searchTerm) 
    {
        $this->db->or_like('formatted_number', $searchTerm);
        $invoice = $this->db->get(db_prefix().'invoices')->row();
        if ($invoice) {
            $invoiceId = $invoice->id;
            $this->db->where('rel_type', 'invoice');
            $this->db->where('rel_id', $invoiceId);
            return $this->db->get(db_prefix().'files')->result();
        }
        return array();
    }

    /**
     * Get invoices related to a given customer
     */
    public function get_invoices_by_customer($customer_id) 
    {
        $customer_id = (int)$customer_id;
        $this->db->where('clientid', $customer_id);
        $this->db->order_by('datecreated', 'DESC');
        return $this->db->get(db_prefix() . 'invoices')->result();
    }

    /**
     * Get contacts associated with a customer
     */
    public function get_contacts_by_customer($customer_id) 
    {
        $this->db->where('userid', $customer_id);
        return $this->db->get(db_prefix() . 'contacts')->result();
    }

    /**
     * Advanced document search with legal practice context
     * Enhanced version that integrates with cases workflow
     */
    public function search_documents(array $filters = [])
    {
        $f = db_prefix().'files';            // tblfiles alias
        $h = db_prefix().'hearings';         // tblhearings
        $c = db_prefix().'cases';            // tblcases
        $con = db_prefix().'case_consultations'; // consultations

        $this->db->select('f.*, 
                          CASE 
                            WHEN f.rel_type = "case" THEN CONCAT("Case: ", c.case_title)
                            WHEN f.rel_type = "hearing" THEN CONCAT("Hearing: ", DATE_FORMAT(h.date, "%d-%m-%Y"))
                            WHEN f.rel_type = "consultation" THEN CONCAT("Consultation: ", con.tag)
                            WHEN f.rel_type = "client" THEN CONCAT("Client: ", cl.company)
                            ELSE f.rel_type
                          END as context_info');
        $this->db->from("$f f");
        
        // Join tables for context information
        $this->db->join("$c c", "c.id = f.rel_id AND f.rel_type = 'case'", 'left');
        $this->db->join("$h h", "h.id = f.rel_id AND f.rel_type = 'hearing'", 'left');
        $this->db->join("$con con", "con.id = f.rel_id AND f.rel_type = 'consultation'", 'left');
        $this->db->join(db_prefix().'clients cl', "cl.userid = f.rel_id AND f.rel_type = 'client'", 'left');

        // TAG filter (works in every mode)
        if (!empty($filters['document_tag'])) {
            $this->db->like('f.tag', $filters['document_tag']);
        }

        // Route by requested search_type
        $type = $filters['search_type'] ?? 'all';

        switch ($type) {
            case 'hearing':
                if (empty($filters['hearing_id'])) {
                    $this->db->where('1=0'); 
                    break;
                }
                $this->db->where([
                    'f.rel_type' => 'hearing',
                    'f.rel_id'   => (int) $filters['hearing_id'],
                ]);
                break;

            case 'case':
                if (empty($filters['case_id'])) { 
                    $this->db->where('1=0'); 
                    break; 
                }

                $caseId = (int) $filters['case_id'];
                $this->db->group_start()
                         ->where(['f.rel_type' => 'case', 'f.rel_id' => $caseId])
                         ->or_where("f.rel_type = 'hearing'
                                     AND f.rel_id IN (SELECT id FROM $h WHERE case_id = $caseId)", null, false)
                         ->group_end();
                break;

            case 'consultation':
                $filters['consultation_id']
                    ? $this->db->where(['f.rel_type'=>'consultation','f.rel_id'=>(int)$filters['consultation_id']])
                    : $this->db->where('1=0');
                break;

            case 'invoice':
                $filters['invoice_id']
                    ? $this->db->where(['f.rel_type'=>'invoice','f.rel_id'=>(int)$filters['invoice_id']])
                    : $this->db->where('1=0');
                break;

            case 'contact':
                $filters['contact_id']
                    ? $this->db->where('f.contact_id', (int)$filters['contact_id'])
                    : $this->db->where('1=0');
                break;

            case 'customer':
                if (empty($filters['customer_id'])) { 
                    $this->db->where('1=0'); 
                    break; 
                }

                $clientId = (int) $filters['customer_id'];
                $this->db->group_start()
                         ->where(['f.rel_type'=>'client','f.rel_id'=>$clientId])
                         ->or_where("f.rel_type='case'
                                     AND f.rel_id IN (SELECT id FROM $c WHERE client_id=$clientId)", null, false)
                         ->or_where("f.rel_type='hearing'
                                     AND f.rel_id IN (SELECT h.id
                                                       FROM $h h
                                                       JOIN $c c ON c.id = h.case_id
                                                       WHERE c.client_id = $clientId)", null, false)
                         ->or_where("f.rel_type='consultation'
                                     AND f.rel_id IN (SELECT id FROM $con WHERE client_id=$clientId)", null, false)
                         ->group_end();
                break;

            case 'all':
            default:
                $this->apply_document_filters($filters, $h, $c, $con);
                break;
        }

        $this->db->order_by('f.dateadded', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Helper for "search_type = all" with enhanced legal practice filters
     */
    private function apply_document_filters(array $f, string $h, string $c, string $con): void
    {
        $has = false;

        if (!empty($f['hearing_id'])) {
            $this->db->or_where(['f.rel_type'=>'hearing','f.rel_id'=>(int)$f['hearing_id']]);
            $has = true;
        }
        if (!empty($f['case_id'])) {
            $id = (int) $f['case_id'];
            $this->db->or_group_start()
                     ->where(['f.rel_type'=>'case','f.rel_id'=>$id])
                     ->or_where("f.rel_type='hearing' AND f.rel_id IN (SELECT id FROM $h WHERE case_id=$id)", null,false)
                     ->group_end();
            $has = true;
        }
        if (!empty($f['consultation_id'])) {
            $this->db->or_where(['f.rel_type'=>'consultation','f.rel_id'=>(int)$f['consultation_id']]);
            $has = true;
        }
        if (!empty($f['invoice_id'])) {
            $this->db->or_where(['f.rel_type'=>'invoice','f.rel_id'=>(int)$f['invoice_id']]);
            $has = true;
        }
        if (!empty($f['contact_id'])) {
            $this->db->or_where('f.contact_id', (int)$f['contact_id']);
            $has = true;
        }

        // Enhanced customer-wide search including consultations
        if (!$has && !empty($f['customer_id'])) {
            $cid = (int) $f['customer_id'];
            $this->db->or_group_start()
                     ->where(['f.rel_type'=>'client','f.rel_id'=>$cid])
                     ->or_where("f.rel_type='case' 
                                 AND f.rel_id IN (SELECT id FROM $c WHERE client_id=$cid)", null, false)
                     ->or_where("f.rel_type='hearing'
                                 AND f.rel_id IN (SELECT h.id
                                                  FROM $h h
                                                  JOIN $c cc ON cc.id=h.case_id
                                                  WHERE cc.client_id=$cid)", null,false)
                     ->or_where("f.rel_type='consultation'
                                 AND f.rel_id IN (SELECT id FROM $con WHERE client_id=$cid)", null, false)
                     ->group_end();
        }
    }

    /**
     * Get documents count for a specific case
     */
    public function get_case_documents_count($case_id)
    {
        $this->db->where('rel_type', 'case');
        $this->db->where('rel_id', $case_id);
        $case_docs = $this->db->count_all_results(db_prefix() . 'files');
        
        // Also count hearing documents
        $this->db->where("rel_type = 'hearing' AND rel_id IN (SELECT id FROM " . db_prefix() . "hearings WHERE case_id = ?)", $case_id);
        $hearing_docs = $this->db->count_all_results(db_prefix() . 'files');
        
        return $case_docs + $hearing_docs;
    }

    /**
     * Get documents for a specific case (including hearing documents)
     */
    public function get_case_documents($case_id, $limit = null)
    {
        $this->db->select('f.*, 
                          CASE 
                            WHEN f.rel_type = "case" THEN "Case Document"
                            WHEN f.rel_type = "hearing" THEN CONCAT("Hearing: ", DATE_FORMAT(h.date, "%d-%m-%Y"))
                            ELSE f.rel_type
                          END as document_context');
        $this->db->from(db_prefix() . 'files f');
        $this->db->join(db_prefix() . 'hearings h', 'h.id = f.rel_id AND f.rel_type = "hearing"', 'left');
        
        $this->db->group_start();
        $this->db->where(['f.rel_type' => 'case', 'f.rel_id' => $case_id]);
        $this->db->or_where("f.rel_type = 'hearing' AND f.rel_id IN (SELECT id FROM " . db_prefix() . "hearings WHERE case_id = ?)", $case_id);
        $this->db->group_end();
        
        $this->db->order_by('f.dateadded', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Enhanced method to get consultations with document counts
     */
    public function get_consultations_with_document_info()
    {
        $this->db->select('
            con.*,
            cl.company as client_name,
            CONCAT(ct.firstname, " ", ct.lastname) as contact_name,
            COUNT(f.id) as document_count
        ');
        $this->db->from(db_prefix() . 'case_consultations con');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = con.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.id = con.contact_id', 'left');
        $this->db->join(db_prefix() . 'files f', 'f.rel_type = "consultation" AND f.rel_id = con.id', 'left');
        $this->db->group_by('con.id');
        $this->db->order_by('con.date_added', 'DESC');
        
        return $this->db->get()->result();
    }

}
