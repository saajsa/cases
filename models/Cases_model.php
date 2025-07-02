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
    try {
        $this->db->select('
            c.id,
            c.consultation_id,
            c.case_title,
            c.case_number,
            c.date_filed,
            c.date_created,
            cl.company as client_name,
            CONCAT(COALESCE(co.firstname, ""), " ", COALESCE(co.lastname, "")) as contact_name,
            ct.name as court_name,
            cr.court_no,
            cr.judge_name,
            CONCAT("Court ", COALESCE(cr.court_no, "N/A"), " - Hon\'ble ", COALESCE(cr.judge_name, "TBD")) as court_room_info,
            CONCAT(COALESCE(ct.name, "Court"), " - Court ", COALESCE(cr.court_no, "N/A")) as court_display,
            CONCAT("CONS-", COALESCE(c.consultation_id, "N/A")) as consultation_reference
        ');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts co', 'co.id = c.contact_id', 'left');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->join(db_prefix() . 'case_consultations cc', 'cc.id = c.consultation_id', 'left');
        $this->db->order_by('c.id', 'desc');
        
        $query = $this->db->get();
        
        if (!$query) {
            log_message('error', 'Database error in get_all_cases_with_details: ' . print_r($this->db->error(), true));
            return [];
        }
        
        $result = $query->result_array();
        
        // Process results to handle any null values or formatting issues
        if (!empty($result)) {
            foreach ($result as &$row) {
                // Make sure to handle cases where court information might be missing
                if (empty($row['court_display']) || $row['court_display'] === 'Court - Court N/A') {
                    $row['court_display'] = 'Court not specified';
                }
                
                // Ensure consultation reference is formatted
                if (empty($row['consultation_reference']) || $row['consultation_reference'] === 'CONS-N/A') {
                    $row['consultation_reference'] = 'CONS-' . ($row['consultation_id'] ?: 'N/A');
                }
                
                // Clean up contact name
                $row['contact_name'] = trim($row['contact_name']);
                if ($row['contact_name'] === ' ') {
                    $row['contact_name'] = '';
                }
            }
        }
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_all_cases_with_details: ' . $e->getMessage());
        return [];
    }
}

    /**
     * Get cases for a specific client with enhanced details including next hearing date
     * @param int $client_id
     * @return array
     */
    public function get_client_cases_with_details($client_id)
    {
        try {
            $this->db->select('
                c.id,
                c.consultation_id,
                c.case_title,
                c.case_number,
                c.date_filed,
                c.date_created,
                c.client_id,
                c.court_room_id,
                cl.company as client_name,
                CONCAT(COALESCE(co.firstname, ""), " ", COALESCE(co.lastname, "")) as contact_name,
                ct.name as court_name,
                cr.court_no,
                cr.judge_name,
                CASE 
                    WHEN cr.court_no IS NOT NULL AND cr.judge_name IS NOT NULL 
                    THEN CONCAT("Court ", cr.court_no, " - Hon\'ble ", cr.judge_name)
                    WHEN ct.name IS NOT NULL 
                    THEN ct.name
                    ELSE "Court not specified"
                END as court_display,
                (SELECT MIN(h.hearing_date) 
                 FROM ' . db_prefix() . 'hearings h 
                 WHERE h.case_id = c.id 
                 AND h.hearing_date >= CURDATE()
                 AND (h.status IS NULL OR h.status != "cancelled")
                 ORDER BY h.hearing_date ASC 
                 LIMIT 1) as next_hearing_date
            ');
            $this->db->from(db_prefix() . 'cases c');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
            $this->db->join(db_prefix() . 'contacts co', 'co.id = c.contact_id', 'left');
            $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
            $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
            $this->db->where('c.client_id', $client_id);
            $this->db->order_by('c.id', 'desc');
            
            $query = $this->db->get();
            
            if (!$query) {
                log_message('error', 'Database error in get_client_cases_with_details: ' . print_r($this->db->error(), true));
                return [];
            }
            
            $result = $query->result_array();
            
            // Add document and hearing counts for each case
            foreach ($result as &$case) {
                $case['document_count'] = $this->get_case_documents_count($case['id']);
                $case['hearing_count'] = $this->get_case_hearing_count($case['id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_client_cases_with_details: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get hearing count for a specific case
     * @param int $case_id
     * @return int
     */
    public function get_case_hearing_count($case_id)
    {
        try {
            $this->db->where('case_id', $case_id);
            return $this->db->count_all_results(db_prefix() . 'hearings');
        } catch (Exception $e) {
            log_message('error', 'Error getting hearing count: ' . $e->getMessage());
            return 0;
        }
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

    // ===============================
    // PHASE 5: CONTEXTUAL DOCUMENT LINKING
    // ===============================

    /**
     * Get contextual document suggestions based on case details
     * @param int $case_id Case ID
     * @param string $document_type Type of document being uploaded
     * @return array Suggested relationships
     */
    public function get_contextual_document_suggestions($case_id, $document_type = null)
    {
        $suggestions = [];
        
        try {
            // Get case details with related entities
            $this->db->select('c.*, cl.company as client_name, cl.userid as client_id');
            $this->db->from(db_prefix() . 'cases c');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
            $this->db->where('c.id', $case_id);
            $case = $this->db->get()->row();
            
            if (!$case) {
                return $suggestions;
            }
            
            // Get related hearings
            $this->db->select('id, date, time, hearing_purpose, status');
            $this->db->from(db_prefix() . 'hearings');
            $this->db->where('case_id', $case_id);
            $this->db->where('status !=', 'Cancelled');
            $this->db->order_by('date', 'DESC');
            $hearings = $this->db->get()->result();
            
            // Get related consultations
            $this->db->select('id, tag, date_added, phase');
            $this->db->from(db_prefix() . 'case_consultations');
            $this->db->where('client_id', $case->client_id);
            $this->db->order_by('date_added', 'DESC');
            $consultations = $this->db->get()->result();
            
            // Build suggestions based on document type
            $suggestions = [
                'case' => [
                    'id' => $case->id,
                    'title' => $case->case_title,
                    'number' => $case->case_number,
                    'client' => $case->client_name,
                    'suggested' => true,
                    'reason' => 'Current case context'
                ],
                'hearings' => [],
                'consultations' => [],
                'smart_suggestions' => []
            ];
            
            // Add hearing suggestions
            foreach ($hearings as $hearing) {
                $suggestions['hearings'][] = [
                    'id' => $hearing->id,
                    'date' => $hearing->date,
                    'time' => $hearing->time,
                    'purpose' => $hearing->hearing_purpose,
                    'status' => $hearing->status,
                    'suggested' => $hearing->status === 'Scheduled',
                    'reason' => $hearing->status === 'Scheduled' ? 'Upcoming hearing' : 'Related hearing'
                ];
            }
            
            // Add consultation suggestions
            foreach ($consultations as $consultation) {
                $suggestions['consultations'][] = [
                    'id' => $consultation->id,
                    'tag' => $consultation->tag,
                    'date' => $consultation->date_added,
                    'phase' => $consultation->phase,
                    'suggested' => $consultation->phase === 'litigation',
                    'reason' => $consultation->phase === 'litigation' ? 'Litigation consultation' : 'Related consultation'
                ];
            }
            
            // Smart suggestions based on document type
            if ($document_type) {
                $suggestions['smart_suggestions'] = $this->get_smart_document_suggestions($case_id, $document_type, $hearings, $consultations);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting contextual suggestions: ' . $e->getMessage());
        }
        
        return $suggestions;
    }

    /**
     * Get smart document suggestions based on document type and case context
     */
    private function get_smart_document_suggestions($case_id, $document_type, $hearings, $consultations)
    {
        $suggestions = [];
        
        // Determine file type from filename/extension
        $doc_type_lower = strtolower($document_type);
        
        // Smart suggestions based on file type
        if (strpos($doc_type_lower, 'order') !== false || strpos($doc_type_lower, 'judgment') !== false) {
            // Court orders/judgments should link to recent hearings
            $recent_hearing = reset($hearings);
            if ($recent_hearing) {
                $suggestions[] = [
                    'type' => 'hearing',
                    'id' => $recent_hearing->id,
                    'title' => 'Link to recent hearing (' . date('d-M-Y', strtotime($recent_hearing->date)) . ')',
                    'confidence' => 90,
                    'reason' => 'Court orders typically relate to recent hearings'
                ];
            }
        }
        
        if (strpos($doc_type_lower, 'petition') !== false || strpos($doc_type_lower, 'application') !== false) {
            // Petitions should link to litigation consultations
            foreach ($consultations as $consultation) {
                if ($consultation->phase === 'litigation') {
                    $suggestions[] = [
                        'type' => 'consultation',
                        'id' => $consultation->id,
                        'title' => 'Link to litigation consultation (' . $consultation->tag . ')',
                        'confidence' => 85,
                        'reason' => 'Petitions often result from litigation consultations'
                    ];
                    break;
                }
            }
        }
        
        if (strpos($doc_type_lower, 'evidence') !== false || strpos($doc_type_lower, 'exhibit') !== false) {
            // Evidence should link to upcoming hearings
            foreach ($hearings as $hearing) {
                if ($hearing->status === 'Scheduled' && strtotime($hearing->date) > time()) {
                    $suggestions[] = [
                        'type' => 'hearing',
                        'id' => $hearing->id,
                        'title' => 'Link to upcoming hearing (' . date('d-M-Y', strtotime($hearing->date)) . ')',
                        'confidence' => 95,
                        'reason' => 'Evidence documents are prepared for upcoming hearings'
                    ];
                    break;
                }
            }
        }
        
        return $suggestions;
    }

    /**
     * Create contextual document links between related entities
     */
    public function create_document_link($document_id, $link_type, $link_id, $metadata = [])
    {
        try {
            // Validate link exists and is accessible
            if (!$this->validate_document_link($document_id, $link_type, $link_id)) {
                return false;
            }
            
            // Create link record
            $link_data = [
                'document_id' => $document_id,
                'link_type' => $link_type,
                'link_id' => $link_id,
                'created_by' => get_staff_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                'metadata' => json_encode($metadata)
            ];
            
            // Check if document_links table exists, create if not
            if (!$this->db->table_exists(db_prefix() . 'document_links')) {
                $this->create_document_links_table();
            }
            
            $this->db->insert(db_prefix() . 'document_links', $link_data);
            $link_id = $this->db->insert_id();
            
            // Log the link creation
            $this->log_document_activity([
                'staff_id' => get_staff_user_id(),
                'document_id' => $document_id,
                'rel_id' => $link_id,
                'rel_type' => 'document_link',
                'message' => "Created contextual link to {$link_type} #{$link_id}"
            ]);
            
            return $link_id;
            
        } catch (Exception $e) {
            log_message('error', 'Error creating document link: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate that a document link is valid and accessible
     */
    private function validate_document_link($document_id, $link_type, $link_id)
    {
        // Validate document exists
        $this->db->where('id', $document_id);
        if ($this->db->count_all_results(db_prefix() . 'files') === 0) {
            return false;
        }
        
        // Validate link target exists based on type
        switch ($link_type) {
            case 'case':
                $this->db->where('id', $link_id);
                return $this->db->count_all_results(db_prefix() . 'cases') > 0;
                
            case 'hearing':
                $this->db->where('id', $link_id);
                return $this->db->count_all_results(db_prefix() . 'hearings') > 0;
                
            case 'consultation':
                $this->db->where('id', $link_id);
                return $this->db->count_all_results(db_prefix() . 'case_consultations') > 0;
                
            case 'client':
                $this->db->where('userid', $link_id);
                return $this->db->count_all_results(db_prefix() . 'clients') > 0;
                
            default:
                return false;
        }
    }

    /**
     * Create document_links table if it doesn't exist
     */
    private function create_document_links_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . db_prefix() . "document_links (
            id int(11) NOT NULL AUTO_INCREMENT,
            document_id int(11) NOT NULL,
            link_type varchar(50) NOT NULL,
            link_id int(11) NOT NULL,
            created_by int(11) NOT NULL,
            created_at datetime NOT NULL,
            metadata text NULL,
            PRIMARY KEY (id),
            KEY document_id (document_id),
            KEY link_type_id (link_type, link_id),
            KEY created_by (created_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->db->query($sql);
    }

    /**
     * Get contextual links for a document
     */
    public function get_document_links($document_id)
    {
        if (!$this->db->table_exists(db_prefix() . 'document_links')) {
            return [];
        }
        
        $this->db->select('dl.*, 
                          CASE dl.link_type
                            WHEN "case" THEN c.case_title
                            WHEN "hearing" THEN CONCAT("Hearing: ", DATE_FORMAT(h.date, "%d-%m-%Y"))
                            WHEN "consultation" THEN CONCAT("Consultation: ", con.tag)
                            WHEN "client" THEN cl.company
                            ELSE dl.link_type
                          END as link_title');
        $this->db->from(db_prefix() . 'document_links dl');
        $this->db->join(db_prefix() . 'cases c', 'c.id = dl.link_id AND dl.link_type = "case"', 'left');
        $this->db->join(db_prefix() . 'hearings h', 'h.id = dl.link_id AND dl.link_type = "hearing"', 'left');
        $this->db->join(db_prefix() . 'case_consultations con', 'con.id = dl.link_id AND dl.link_type = "consultation"', 'left');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = dl.link_id AND dl.link_type = "client"', 'left');
        $this->db->where('dl.document_id', $document_id);
        $this->db->order_by('dl.created_at', 'DESC');
        
        return $this->db->get()->result();
    }

    // ===============================
    // PHASE 5: ADVANCED SEARCH INTEGRATION
    // ===============================

    /**
     * Advanced multi-criteria document search with relevance scoring
     */
    public function advanced_document_search($criteria)
    {
        try {
            $this->db->select('f.*, 
                              CASE f.rel_type
                                WHEN "case" THEN c.case_title
                                WHEN "hearing" THEN CONCAT("Hearing: ", DATE_FORMAT(h.date, "%d-%m-%Y"), " - ", h.hearing_purpose)
                                WHEN "consultation" THEN CONCAT("Consultation: ", con.tag)
                                WHEN "client" THEN cl.company
                                WHEN "invoice" THEN CONCAT("Invoice: ", inv.formatted_number)
                                ELSE f.rel_type
                              END as context_info,
                              cl.company as client_name,
                              c.case_number,
                              0 as relevance_score');
                              
            $this->db->from(db_prefix() . 'files f');
            $this->db->join(db_prefix() . 'cases c', 'c.id = f.rel_id AND f.rel_type = "case"', 'left');
            $this->db->join(db_prefix() . 'hearings h', 'h.id = f.rel_id AND f.rel_type = "hearing"', 'left');
            $this->db->join(db_prefix() . 'case_consultations con', 'con.id = f.rel_id AND f.rel_type = "consultation"', 'left');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = f.rel_id AND f.rel_type = "client" OR cl.userid = c.client_id', 'left');
            $this->db->join(db_prefix() . 'invoices inv', 'inv.id = f.rel_id AND f.rel_type = "invoice"', 'left');
            
            // Apply search criteria
            $where_conditions = [];
            
            // Text search
            if (!empty($criteria['search_text'])) {
                $search_text = $this->db->escape_like_str($criteria['search_text']);
                $this->db->group_start();
                $this->db->like('f.file_name', $search_text);
                $this->db->or_like('f.tag', $search_text);
                $this->db->or_like('c.case_title', $search_text);
                $this->db->or_like('c.case_number', $search_text);
                $this->db->or_like('cl.company', $search_text);
                $this->db->or_like('h.hearing_purpose', $search_text);
                $this->db->or_like('con.tag', $search_text);
                $this->db->group_end();
            }
            
            // Client filter
            if (!empty($criteria['client_id'])) {
                $this->db->group_start();
                $this->db->where('c.client_id', $criteria['client_id']);
                $this->db->or_where('f.rel_type = "client" AND f.rel_id', $criteria['client_id']);
                $this->db->or_where('con.client_id', $criteria['client_id']);
                $this->db->group_end();
            }
            
            // Document type filter
            if (!empty($criteria['document_type'])) {
                $this->db->where('f.rel_type', $criteria['document_type']);
            }
            
            // File type filter
            if (!empty($criteria['file_type'])) {
                $this->db->like('f.filetype', $criteria['file_type']);
            }
            
            // Date range filter
            if (!empty($criteria['date_from'])) {
                $this->db->where('DATE(f.dateadded) >=', $criteria['date_from']);
            }
            if (!empty($criteria['date_to'])) {
                $this->db->where('DATE(f.dateadded) <=', $criteria['date_to']);
            }
            
            // Case status filter
            if (!empty($criteria['case_status'])) {
                $this->db->where('c.status', $criteria['case_status']);
            }
            
            // Hearing status filter
            if (!empty($criteria['hearing_status'])) {
                $this->db->where('h.status', $criteria['hearing_status']);
            }
            
            $this->db->order_by('f.dateadded', 'DESC');
            
            // Apply limit
            $limit = !empty($criteria['limit']) ? (int)$criteria['limit'] : 50;
            $this->db->limit($limit);
            
            $results = $this->db->get()->result();
            
            // Calculate relevance scores
            if (!empty($criteria['search_text'])) {
                $results = $this->calculate_search_relevance($results, $criteria['search_text']);
            }
            
            return $results;
            
        } catch (Exception $e) {
            log_message('error', 'Error in advanced document search: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate relevance scores for search results
     */
    private function calculate_search_relevance($results, $search_text)
    {
        $search_terms = explode(' ', strtolower($search_text));
        
        foreach ($results as &$result) {
            $score = 0;
            $searchable_content = strtolower(
                $result->file_name . ' ' . 
                $result->tag . ' ' . 
                $result->context_info . ' ' .
                $result->client_name . ' ' .
                $result->case_number
            );
            
            foreach ($search_terms as $term) {
                $term = trim($term);
                if (empty($term)) continue;
                
                // Exact matches in filename get highest score
                if (strpos(strtolower($result->file_name), $term) !== false) {
                    $score += 10;
                }
                
                // Matches in tags get high score
                if (strpos(strtolower($result->tag), $term) !== false) {
                    $score += 8;
                }
                
                // Matches in case info get medium score
                if (strpos(strtolower($result->context_info), $term) !== false) {
                    $score += 5;
                }
                
                // Matches in client name get medium score
                if (strpos(strtolower($result->client_name), $term) !== false) {
                    $score += 5;
                }
                
                // General content matches get low score
                if (strpos($searchable_content, $term) !== false) {
                    $score += 1;
                }
            }
            
            $result->relevance_score = $score;
        }
        
        // Sort by relevance score (highest first)
        usort($results, function($a, $b) {
            return $b->relevance_score - $a->relevance_score;
        });
        
        return $results;
    }

    /**
     * Get search suggestions based on partial input
     */
    public function get_search_suggestions($partial_text, $limit = 10)
    {
        $suggestions = [];
        
        if (strlen($partial_text) < 2) {
            return $suggestions;
        }
        
        try {
            $search_text = $this->db->escape_like_str($partial_text);
            
            // Get suggestions from different sources
            $sources = [
                'clients' => "SELECT DISTINCT company as suggestion, 'Client' as type FROM " . db_prefix() . "clients WHERE company LIKE '%{$search_text}%'",
                'cases' => "SELECT DISTINCT case_title as suggestion, 'Case' as type FROM " . db_prefix() . "cases WHERE case_title LIKE '%{$search_text}%' OR case_number LIKE '%{$search_text}%'",
                'files' => "SELECT DISTINCT tag as suggestion, 'Document Tag' as type FROM " . db_prefix() . "files WHERE tag LIKE '%{$search_text}%' AND tag != ''",
                'hearings' => "SELECT DISTINCT hearing_purpose as suggestion, 'Hearing' as type FROM " . db_prefix() . "hearings WHERE hearing_purpose LIKE '%{$search_text}%'"
            ];
            
            foreach ($sources as $source => $query) {
                $results = $this->db->query($query . " LIMIT " . $limit)->result();
                foreach ($results as $result) {
                    if (!empty($result->suggestion)) {
                        $suggestions[] = [
                            'text' => $result->suggestion,
                            'type' => $result->type,
                            'source' => $source
                        ];
                    }
                }
            }
            
            // Remove duplicates and limit results
            $suggestions = array_unique($suggestions, SORT_REGULAR);
            return array_slice($suggestions, 0, $limit);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting search suggestions: ' . $e->getMessage());
            return [];
        }
    }

}
