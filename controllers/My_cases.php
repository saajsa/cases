<?php

defined('BASEPATH') or exit('No direct script access allowed');

class My_cases extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        
        // Basic authentication check
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }
    }

    public function index()
    {
        // Pass data to the view
        $this->data([
            'title' => 'My Cases',
            'cases' => [], // Empty for now
            'client_id' => get_client_user_id()
        ]);
        
        // Set page title
        $this->title('My Cases');
        
        // The view name
        $this->view('my_cases_list');
        
        // Render the layout/view
        $this->layout();
    }
}