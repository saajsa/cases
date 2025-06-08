<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cases_model extends App_Model
{
    public function add_consultation($data)
    {
        $data['staff_id'] = get_staff_user_id();
        $data['date_added'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix().'case_consultations', $data);

        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }

        log_message('error', 'Failed to add consultation: ' . json_encode($data));
        return false;
    }

    public function get_consultations()
    {
        $query = $this->db->select(
                            'cc.id, cc.client_id, cc.contact_id, cc.tag, cc.note, cc.date_added, cc.phase,
                             c.company AS client_name, ct.firstname, ct.lastname')
                        ->from(db_prefix().'case_consultations AS cc')
                        ->join(db_prefix().'clients AS c', 'c.userid = cc.client_id', 'left')
                        ->join(db_prefix().'contacts AS ct', 'ct.id = cc.contact_id', 'left')
                        ->get();

        if (!$query) {
            log_message('error', 'DB Error (consultations): ' . $this->db->error()['message']);
            return [];
        }

        return $query->result_array();
    }

    public function get_all_cases()
    {
    $query = $this->db->select('c.*, 
                                r.court_no, 
                                r.judge_name, 
                                ct.name as court_name')
                      ->from(db_prefix().'cases AS c')
                      ->join(db_prefix().'court_rooms AS r', 'r.id = c.court_room_id', 'left')
                      ->join(db_prefix().'courts AS ct', 'ct.id = r.court_id', 'left')
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
            return $this->db->insert_id();
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

        return $this->db->affected_rows() > 0;
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

}
