<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Documents_model extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    // ✅ Log activity for file actions (upload, delete, etc.)
    public function log_activity(array $data)
    {
        $insert = [
            'staff_id'    => $data['staff_id'] ?? null,
            'document_id' => $data['document_id'] ?? null,
            'rel_id'      => $data['rel_id'] ?? null,
            'rel_type'    => $data['rel_type'] ?? null,
            'message'     => $data['message'],
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('tblfile_activity_log', $insert);
    }

    // ✅ Fetch recent activity logs
    public function get_recent_activities($limit = 10)
    {
        $this->db->select('l.*, CONCAT(s.firstname, " ", s.lastname) AS staff_name');
        $this->db->from('tblfile_activity_log l');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = l.staff_id', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    // Search for client-based documents by looking up the client (using company name)
    public function searchByClient($searchTerm) {
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

    // Search for invoice-based documents by looking up the invoice (using formatted_number or id)
    public function searchByInvoice($searchTerm) {
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

    // Get invoices related to a given customer
    public function get_invoices_by_customer($customer_id) {
        $customer_id = (int)$customer_id;
        $this->db->where('clientid', $customer_id);
        $this->db->order_by('datecreated', 'DESC');
        $query = $this->db->get(db_prefix() . 'invoices');

        log_message('debug', 'Invoices query: ' . $this->db->last_query());
        
        return $query->result();
    }

    // Get contacts associated with a customer
    public function get_contacts_by_customer($customer_id) {
        $this->db->where('userid', $customer_id);
        return $this->db->get(db_prefix() . 'contacts')->result();
    }

/**
 * Smart document search – honours the UI “Search Type” choice.
 *
 * @param array $filters  (all raw POST data from the form is fine)
 * @return array
 */
public function search_documents(array $filters = [])
{
    $f = db_prefix().'files';            // tblfiles alias
    $h = db_prefix().'hearings';         // tblhearings
    $c = db_prefix().'cases';            // tblcases

    $this->db->from("$f f");

    /* ───────────── TAG filter (works in every mode) ───────────── */
    if (!empty($filters['document_tag'])) {
        $this->db->like('f.tag', $filters['document_tag']);
    }

    /* ───────────── Route by requested search_type ───────────── */
    $type = $filters['search_type'] ?? 'all';

    switch ($type) {

    /* ============================================================
       HEARING  ─ show only that single hearing’s files
       ============================================================ */
    case 'hearing':
        if (empty($filters['hearing_id'])) {    // nothing chosen ⇒ empty set
            $this->db->where('1=0'); break;
        }
        $this->db->where([
            'f.rel_type' => 'hearing',
            'f.rel_id'   => (int) $filters['hearing_id'],
        ]);
        break;

    /* ============================================================
       CASE  ─ include the case-level files + every file of its
               hearings. (No client fallback here.)
       ============================================================ */
    case 'case':
        if (empty($filters['case_id'])) { $this->db->where('1=0'); break; }

        $caseId = (int) $filters['case_id'];
        $this->db->group_start()   // ( … )
                 ->where(['f.rel_type' => 'case', 'f.rel_id' => $caseId])
                 ->or_where("f.rel_type = 'hearing'
                             AND f.rel_id IN (SELECT id FROM $h WHERE case_id = $caseId)", null, false)
                 ->group_end();
        break;

    /* ============================================================
       CONSULTATION | INVOICE | CONTACT  ─ plain one-to-one match
       ============================================================ */
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

    /* ============================================================
       CUSTOMER  ─ everything the client owns:
                   • files linked straight to the client record
                   • all case-level files
                   • all hearing-level files of those cases
       ============================================================ */
    case 'customer':
        if (empty($filters['customer_id'])) { $this->db->where('1=0'); break; }

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
                 ->group_end();
        break;

    /* ============================================================
       ALL  ─ combine *every* specific ID supplied; if none, return
              the whole table (optionally tag-filtered).
       ============================================================ */
    case 'all':
    default:
        $this->applySpecificFilters($filters, $h, $c);   // helper below
        break;
    }

    return $this->db->get()->result();
}

/**
 * Helper for “search_type = all” – adds OR-clauses for any specific
 * IDs present, and falls back to the broad customer logic only if
 * no other filters were provided.
 */
private function applySpecificFilters(array $f, string $h, string $c): void
{
    $has = false;   // did we add at least one specific clause?

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

    /* If NOTHING else was specified, fall back to the customer-wide
       grab-all so the “Client only” filter still works. */
    if (!$has && !empty($f['customer_id'])) {
        $cid = (int) $f['customer_id'];
        $this->db->or_group_start()
                 ->where(['f.rel_type'=>'client','f.rel_id'=>$cid])
                 ->or_where("f.rel_type='case' 
                             AND f.rel_id IN (SELECT id FROM $c WHERE client_id=$cid)", null, false)
                 ->or_where("f.rel_type='hearing'
                             AND f.rel_id IN (SELECT h.id
                                              FROM $h h
                                              JOIN $c c ON c.id=h.case_id
                                              WHERE c.client_id=$cid)", null,false)
                 ->group_end();
    }
}



    
    
    
    
    
    
    
}
