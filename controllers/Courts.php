<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Courts extends AdminController
{
    public function manage_courts()
    {
        if (!is_admin()) {
            access_denied();
        }

        $data['title'] = 'Manage Courts';
        $data['courts'] = $this->db->get('tblcourts')->result_array(); // previously tblcourt_establishments
        $this->load->view('cases/courts/manage_courts', $data);
    }


    public function add_court()
    {
        if (!is_admin()) {
            access_denied();
        }

        $data = [
            'name'      => $this->input->post('name', true),
            'hierarchy' => $this->input->post('hierarchy', true),
            'location'  => $this->input->post('location', true),
            'status'    => $this->input->post('status', true)
        ];

        $this->db->insert('tblcourts', $data);
        set_alert('success', 'Court added');
        redirect(admin_url('cases/courts/manage_courts'));
    }

    public function edit_court($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $data = [
                'name'      => $this->input->post('name', true),
                'hierarchy' => $this->input->post('hierarchy', true),
                'location'  => $this->input->post('location', true),
                'status'    => $this->input->post('status', true)
            ];

            $this->db->where('id', $id);
            $this->db->update('tblcourts', $data);
            
            set_alert('success', 'Court updated successfully');
            redirect(admin_url('cases/courts/manage_courts'));
        }

        $court = $this->db->where('id', $id)->get('tblcourts')->row_array();
        
        if (!$court) {
            show_404();
        }

        $data['title'] = 'Edit Court';
        $data['court'] = $court;
        
        $this->load->view('cases/courts/edit_court', $data);
    }

    public function delete_court($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        // Check if court is used in any court rooms
        $this->db->where('court_id', $id);
        $roomCount = $this->db->count_all_results('tblcourt_rooms');

        // Check if court is used in any cases
        $this->db->where('court_id', $id);
        $caseCount = $this->db->count_all_results('tblcases');

        if ($roomCount > 0 || $caseCount > 0) {
            set_alert('warning', 'Cannot delete court. It is associated with rooms or cases.');
        } else {
            $this->db->where('id', $id);
            $this->db->delete('tblcourts');
            set_alert('success', 'Court deleted successfully');
        }

        redirect(admin_url('cases/courts/manage_courts'));
    }

    public function manage_rooms()
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->db->select('r.*, c.name as court_name');
        $this->db->from('tblcourt_rooms r');
        $this->db->join('tblcourts c', 'c.id = r.court_id');
        $rooms = $this->db->get()->result_array();

        $courts = $this->db->get('tblcourts')->result_array();

        $data['title']  = 'Manage Court Rooms';
        $data['rooms']  = $rooms;
        $data['courts'] = $courts;

        $this->load->view('cases/courts/manage_courtrooms', $data);
    }


    public function add_room()
    {
        if (!is_admin()) {
            access_denied();
        }

        $data = [
            'court_id' => $this->input->post('court_id', true),
            'court_no' => $this->input->post('court_no', true),
            'judge_name' => $this->input->post('judge_name', true),
            'from_date' => $this->input->post('from_date', true),
            'to_date' => $this->input->post('to_date', true),
            'type' => $this->input->post('type', true),
            'bench_type' => $this->input->post('bench_type', true),
            'status' => $this->input->post('status', true),
        ];

        $this->db->insert('tblcourt_rooms', $data);
        set_alert('success', 'Court Room added');
        redirect(admin_url('cases/courts/manage_rooms'));
    }

    public function edit_room($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $data = [
                'court_id' => $this->input->post('court_id', true),
                'court_no' => $this->input->post('court_no', true),
                'judge_name' => $this->input->post('judge_name', true),
                'from_date' => $this->input->post('from_date', true),
                'to_date' => $this->input->post('to_date', true),
                'type' => $this->input->post('type', true),
                'bench_type' => $this->input->post('bench_type', true),
                'status' => $this->input->post('status', true),
            ];

            $this->db->where('id', $id);
            $this->db->update('tblcourt_rooms', $data);
            
            set_alert('success', 'Court Room updated successfully');
            redirect(admin_url('cases/courts/manage_rooms'));
        }

        // Get room data for editing
        $this->db->where('id', $id);
        $room = $this->db->get('tblcourt_rooms')->row_array();
        
        if (!$room) {
            show_404();
        }

        // Get all courts for dropdown
        $courts = $this->db->get('tblcourts')->result_array();

        $data['title'] = 'Edit Court Room';
        $data['room'] = $room;
        $data['courts'] = $courts;
        
        $this->load->view('cases/courts/edit_room', $data);
    }

    public function delete_room($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        // Check if room is used in any cases
        $this->db->where('court_room_id', $id);
        $caseCount = $this->db->count_all_results('tblcases');

        // Check if room is used in any hearings (assuming a relationship exists)
        $hasHearings = false;
        if ($this->db->table_exists('tblhearings')) {
            $this->db->where('court_room_id', $id);
            $hearingCount = $this->db->count_all_results('tblhearings');
            $hasHearings = ($hearingCount > 0);
        }

        if ($caseCount > 0 || $hasHearings) {
            set_alert('warning', 'Cannot delete court room. It is associated with cases or hearings.');
        } else {
            $this->db->where('id', $id);
            $this->db->delete('tblcourt_rooms');
            set_alert('success', 'Court Room deleted successfully');
        }

        redirect(admin_url('cases/courts/manage_rooms'));
    }

    public function get_all_courts()
    {
        $courts = $this->db
            ->where('status', 'Active')
            ->get('tblcourts')
            ->result_array();

        echo json_encode([
            'success' => true,
            'data' => $courts
        ]);
    }


    public function get_rooms_by_court($id)
    {
        $this->db->where('court_id', $id);
        $this->db->where('status', 'Active');
        $rooms = $this->db->get('tblcourt_rooms')->result_array();

        echo json_encode(['success' => true, 'data' => $rooms]);
    }
}