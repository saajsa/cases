<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Cases
Description: Manage legal cases, consultations, courts, courtrooms, and litigation tracking.
Version: 1.0.0
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

// Load module helpers and assets
hooks()->add_action('admin_init', 'cases_module_init');

function cases_module_init() {
    $CI = &get_instance();
    
    // Load the CSS framework helper manually since it's in module directory
    $helper_path = FCPATH . 'modules/cases/helpers/cases_css_helper.php';
    if (file_exists($helper_path)) {
        require_once($helper_path);
    }
}

// SAFE MENU STRUCTURE - Basic but working
hooks()->add_action('admin_init', 'cases_module_init_menu_items');

function cases_module_init_menu_items() {
    $CI = &get_instance();

    // Main Cases menu - IMPROVED NAME
    $CI->app_menu->add_sidebar_menu_item('cases', [
        'name'     => 'Legal Practice',
        'href'     => admin_url('cases'),
        'icon'     => 'fa fa-balance-scale',
        'position' => 4,
    ]);

    // Submenu: Dashboard
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'cases_dashboard',
        'name'     => 'Dashboard',
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

    // Submenu: Hearings
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'hearings_management',
        'name'     => 'Hearings',
        'href'     => admin_url('cases/hearings'),
        'position' => 2,
    ]);

    // Submenu: Cause List
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'cases-causelist',
        'name'     => 'Daily Cause List',
        'href'     => admin_url('cases/hearings/causelist'),
        'position' => 3,
    ]);

    // Submenu: Document Management
  //  $CI->app_menu->add_sidebar_children_item('cases', [
   //     'slug'     => 'document_manager',
    //    'name'     => _l('document_manager'),
    //    'href'     => admin_url('cases/documents'),
    //    'position' => 4,
    // ]);

    // Submenu: Upload Documents
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'upload_documents',
        'name'     => _l('upload_document'),
        'href'     => admin_url('cases/documents/upload'),
        'position' => 5,
    ]);

    // Submenu: Search Documents
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'search_documents',
        'name'     => _l('search_documents'),
        'href'     => admin_url('cases/documents/search'),
        'position' => 6,
    ]);

    // Phase 5: Advanced Document Features
    // Submenu: Smart Upload
  //  $CI->app_menu->add_sidebar_children_item('cases', [
   //     'slug'     => 'smart_upload_documents',
     //   'name'     => _l('smart_document_upload'),
     //   'href'     => admin_url('cases/documents/smart_upload'),
     //   'position' => 7,
    //]);

    // Submenu: Advanced Search
   // $CI->app_menu->add_sidebar_children_item('cases', [
     //   'slug'     => 'advanced_search_documents',
       // 'name'     => _l('advanced_document_search'),
       // 'href'     => admin_url('cases/documents/advanced_search'),
        // 'position' => 8,
    //]);

    // Submenu: Document Analytics
    //$CI->app_menu->add_sidebar_children_item('cases', [
      //  'slug'     => 'document_analytics',
        //'name'     => _l('document_analytics_report'),
       // 'href'     => admin_url('cases/documents/analytics_report'),
       // 'position' => 9,
    //]);

    // Admin only items
    if (is_admin()) {
        // Submenu: Courts
        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'courts',
            'name'     => 'Courts',
            'href'     => admin_url('cases/courts/manage_courts'),
            'position' => 10,
        ]);
        
        // Submenu: Court Rooms
        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'court_rooms',
            'name'     => 'Court Rooms',
            'href'     => admin_url('cases/courts/manage_rooms'),
            'position' => 11,
        ]);
    }
}

// Register module-specific permissions
hooks()->add_action('admin_init', 'cases_permissions');

// Add client area menu for cases
hooks()->add_action('clients_init', 'cases_client_area_init');

function cases_permissions() {
    $capabilities = [
        'capabilities' => [
            'view'   => _l('permission_view') . ' (' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ]
    ];
    register_staff_capabilities('cases', $capabilities, _l('Legal Practice & Documents'));
}

function cases_client_area_init() {
    if (is_client_logged_in()) {
        // Main dashboard menu item
        add_theme_menu_item('my_legal_dashboard', [
            'name'     => 'Legal Dashboard',
            'href'     => site_url('cases/Client'),
            'icon'     => 'fa fa-tachometer',
            'position' => 50,
        ]);
        
        // Sub-menu for Cases & Hearings  
        add_theme_menu_item('my_cases_hearings', [
            'name'     => 'Cases & Hearings',
            'href'     => site_url('cases/Client'),
            'icon'     => 'fa fa-balance-scale',
            'position' => 51,
        ]);
        
        // Sub-menu for Consultations
        add_theme_menu_item('my_consultations', [
            'name'     => 'Consultations',
            'href'     => site_url('cases/Client/consultations'),
            'icon'     => 'fa fa-comments',
            'position' => 52,
        ]);
    }
}