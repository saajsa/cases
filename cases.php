<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Cases
Description: Manage legal cases, consultations, courts, courtrooms, litigation tracking, and appointment booking.
Version: 1.1.0
Requires at least: 2.3.*
*/

define('CASES_MODULE_NAME', 'cases');

// Register module activation hook
register_activation_hook(CASES_MODULE_NAME, 'cases_module_activation_hook');

function cases_module_activation_hook() {
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

// Register language files
register_language_files(CASES_MODULE_NAME, [CASES_MODULE_NAME]);

// Add admin menu item and submenus
hooks()->add_action('admin_init', 'cases_module_init_menu_items');

function cases_module_init_menu_items() {
    $CI = &get_instance();

    // Main Cases menu
    $CI->app_menu->add_sidebar_menu_item('cases', [
        'name'     => _l('Cases'),
        'href'     => admin_url('cases'),
        'icon'     => 'fa fa-briefcase',
        'position' => 4,
    ]);

    // Submenu: Dashboard
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'cases_dashboard',
        'name'     => 'Cases Dashboard',
        'href'     => admin_url('cases/caseboard'),
        'position' => 0,
    ]);

    // Submenu: Consultations & Cases
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'manage_cases_and_consultations',
        'name'     => 'Consultations & Cases',
        'href'     => admin_url('cases'),
        'position' => 1,
    ]);

    // Submenu: Appointments
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'appointments',
        'name'     => 'Appointments',
        'href'     => admin_url('cases/appointments'),
        'position' => 2,
    ]);

    // Submenu: Appointment Calendar
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'appointment_calendar',
        'name'     => 'Calendar',
        'href'     => admin_url('cases/calendar'),
        'position' => 3,
    ]);

    // Submenu: Courts
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'courts',
        'name'     => 'Courts',
        'href'     => admin_url('cases/courts/manage_courts'),
        'position' => 4,
    ]);

    // Submenu: Court Rooms
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'court_rooms',
        'name'     => 'Court Rooms',
        'href'     => admin_url('cases/courts/manage_rooms'),
        'position' => 5,
    ]);

    // Submenu: Cause List
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'cases-causelist',
        'name'     => 'Cause List',
        'href'     => admin_url('cases/hearings/causelist'),
        'position' => 6,
    ]);

    // Appointment Management Items (Admin only)
    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'appointment_settings',
            'name'     => 'Appointment Settings',
            'href'     => admin_url('cases/appointments/settings'),
            'position' => 7,
        ]);

        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'appointment_services',
            'name'     => 'Services Management',
            'href'     => admin_url('cases/appointments/services'),
            'position' => 8,
        ]);

        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'staff_availability',
            'name'     => 'Staff Availability',
            'href'     => admin_url('cases/appointments/availability'),
            'position' => 9,
        ]);
    }
}

// Register module-specific permissions
hooks()->add_action('admin_init', 'cases_permissions');

function cases_permissions() {
    // Cases module permissions
    $cases_capabilities = [
        'capabilities' => [
            'view'   => _l('permission_view') . ' (' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ]
    ];
    register_staff_capabilities('cases', $cases_capabilities, _l('Cases'));

    // Appointments module permissions
    $appointment_capabilities = [
        'capabilities' => [
            'view'              => _l('permission_view') . ' (' . _l('permission_global') . ')',
            'create'            => _l('permission_create'),
            'edit'              => _l('permission_edit'),
            'delete'            => _l('permission_delete'),
            'manage_settings'   => 'Manage Appointment Settings',
            'manage_services'   => 'Manage Services',
            'manage_availability' => 'Manage Staff Availability',
            'view_calendar'     => 'View Appointment Calendar',
            'book_for_clients'  => 'Book Appointments for Clients',
            'cancel_appointments' => 'Cancel Appointments',
            'reschedule_appointments' => 'Reschedule Appointments',
            'view_reports'      => 'View Appointment Reports',
        ]
    ];
    register_staff_capabilities('appointments', $appointment_capabilities, _l('Appointments'));

    // Court management permissions
    $court_capabilities = [
        'capabilities' => [
            'view'   => _l('permission_view') . ' (' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ]
    ];
    register_staff_capabilities('courts', $court_capabilities, _l('Courts'));

    // Hearings permissions
    $hearings_capabilities = [
        'capabilities' => [
            'view'         => _l('permission_view') . ' (' . _l('permission_global') . ')',
            'create'       => _l('permission_create'),
            'edit'         => _l('permission_edit'),
            'delete'       => _l('permission_delete'),
            'view_causelist' => 'View Cause List',
            'manage_schedule' => 'Manage Hearing Schedule',
        ]
    ];
    register_staff_capabilities('hearings', $hearings_capabilities, _l('Hearings'));
}

// Add custom hooks for appointment system
hooks()->add_action('after_invoice_payment_added', 'handle_appointment_payment');
hooks()->add_action('invoice_status_changed', 'handle_appointment_invoice_status_change');

/**
 * Handle appointment payment when invoice payment is added
 */
function handle_appointment_payment($data) {
    $CI = &get_instance();
    
    if (!$CI->db->table_exists(db_prefix() . 'appointments')) {
        return;
    }
    
    $invoice_id = $data['invoiceid'];
    
    // Check if this invoice is linked to an appointment
    $CI->db->where('invoice_id', $invoice_id);
    $CI->db->or_where('booking_invoice_id', $invoice_id);
    $appointment = $CI->db->get(db_prefix() . 'appointments')->row_array();
    
    if ($appointment) {
        // Load appointments model if available
        if (file_exists(APPPATH . 'modules/cases/models/Appointments_model.php')) {
            $CI->load->model('cases/Appointments_model', 'appointments_model');
            
            // Update appointment payment status
            $CI->db->select('SUM(amount) as total_paid');
            $CI->db->where('invoiceid', $invoice_id);
            $payments = $CI->db->get(db_prefix() . 'invoicepaymentrecords')->row_array();
            
            $paid_amount = $payments['total_paid'] ?? 0;
            
            // Get invoice total
            $CI->db->where('id', $invoice_id);
            $invoice = $CI->db->get(db_prefix() . 'invoices')->row_array();
            
            $payment_status = 'unpaid';
            if ($paid_amount > 0) {
                if ($paid_amount >= $invoice['total']) {
                    $payment_status = 'paid';
                    // Auto-confirm appointment if payment is complete
                    if ($appointment['status'] == 'pending') {
                        $CI->appointments_model->update_appointment($appointment['id'], [
                            'status' => 'confirmed',
                            'payment_status' => $payment_status,
                            'paid_amount' => $paid_amount
                        ]);
                    }
                } else {
                    $payment_status = 'partial';
                }
            }
            
            // Update appointment payment status
            $CI->appointments_model->update_appointment($appointment['id'], [
                'payment_status' => $payment_status,
                'paid_amount' => $paid_amount
            ]);
        }
    }
}

/**
 * Handle appointment invoice status changes
 */
function handle_appointment_invoice_status_change($data) {
    $CI = &get_instance();
    
    if (!$CI->db->table_exists(db_prefix() . 'appointments')) {
        return;
    }
    
    $invoice_id = $data['invoice_id'];
    $status = $data['status'];
    
    // Check if this invoice is linked to an appointment
    $CI->db->where('invoice_id', $invoice_id);
    $CI->db->or_where('booking_invoice_id', $invoice_id);
    $appointment = $CI->db->get(db_prefix() . 'appointments')->row_array();
    
    if ($appointment) {
        // Handle different invoice statuses
        switch ($status) {
            case 2: // Paid
                if ($appointment['status'] == 'pending') {
                    // Auto-confirm appointment
                    $CI->db->where('id', $appointment['id']);
                    $CI->db->update(db_prefix() . 'appointments', [
                        'status' => 'confirmed',
                        'payment_status' => 'paid'
                    ]);
                }
                break;
                
            case 5: // Cancelled
                if ($appointment['status'] != 'completed') {
                    // Cancel appointment if invoice is cancelled
                    $CI->db->where('id', $appointment['id']);
                    $CI->db->update(db_prefix() . 'appointments', [
                        'status' => 'cancelled',
                        'payment_status' => 'cancelled',
                        'cancellation_reason' => 'Invoice cancelled'
                    ]);
                }
                break;
        }
    }
}

// Add dashboard widgets for appointments
hooks()->add_action('admin_init', 'register_appointment_dashboard_widgets');

function register_appointment_dashboard_widgets() {
    if (function_exists('add_dashboard_widget') && is_admin()) {
        add_dashboard_widget('appointments_today', 'appointments_today_widget');
        add_dashboard_widget('appointments_upcoming', 'appointments_upcoming_widget');
    }
}

/**
 * Today's appointments widget
 */
function appointments_today_widget() {
    $CI = &get_instance();
    
    if (!$CI->db->table_exists(db_prefix() . 'appointments')) {
        return '';
    }
    
    $today = date('Y-m-d');
    
    $CI->db->select('COUNT(*) as total');
    $CI->db->where('appointment_date', $today);
    $CI->db->where('status !=', 'cancelled');
    $total_today = $CI->db->get(db_prefix() . 'appointments')->row()->total;
    
    $CI->db->select('COUNT(*) as completed');
    $CI->db->where('appointment_date', $today);
    $CI->db->where('status', 'completed');
    $completed_today = $CI->db->get(db_prefix() . 'appointments')->row()->completed;
    
    $widget = '<div class="widget-drilldown">';
    $widget .= '<div class="widget-drilldown-item">';
    $widget .= '<h1 class="tw-text-lg tw-font-semibold tw-text-neutral-700">' . $total_today . '</h1>';
    $widget .= '<p class="text-muted">Today\'s Appointments</p>';
    $widget .= '</div>';
    $widget .= '<div class="widget-drilldown-item">';
    $widget .= '<h1 class="tw-text-lg tw-font-semibold tw-text-success">' . $completed_today . '</h1>';
    $widget .= '<p class="text-muted">Completed</p>';
    $widget .= '</div>';
    $widget .= '</div>';
    
    return $widget;
}

/**
 * Upcoming appointments widget
 */
function appointments_upcoming_widget() {
    $CI = &get_instance();
    
    if (!$CI->db->table_exists(db_prefix() . 'appointments')) {
        return '';
    }
    
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $next_week = date('Y-m-d', strtotime('+7 days'));
    
    $CI->db->select('COUNT(*) as upcoming');
    $CI->db->where('appointment_date >=', $tomorrow);
    $CI->db->where('appointment_date <=', $next_week);
    $CI->db->where('status !=', 'cancelled');
    $upcoming_count = $CI->db->get(db_prefix() . 'appointments')->row()->upcoming;
    
    $widget = '<div class="widget-drilldown">';
    $widget .= '<div class="widget-drilldown-item">';
    $widget .= '<h1 class="tw-text-lg tw-font-semibold tw-text-info">' . $upcoming_count . '</h1>';
    $widget .= '<p class="text-muted">Next 7 Days</p>';
    $widget .= '</div>';
    $widget .= '</div>';
    
    return $widget;
}

?>