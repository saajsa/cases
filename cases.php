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
    $CI->app_menu->add_sidebar_children_item('cases', [
        'slug'     => 'document_manager',
        'name'     => _l('document_manager'),
        'href'     => admin_url('cases/documents'),
        'position' => 4,
    ]);

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

    // Admin only items
    if (is_admin()) {
        // Submenu: Courts
        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'courts',
            'name'     => 'Courts',
            'href'     => admin_url('cases/courts/manage_courts'),
            'position' => 7,
        ]);
        
        // Submenu: Court Rooms
        $CI->app_menu->add_sidebar_children_item('cases', [
            'slug'     => 'court_rooms',
            'name'     => 'Court Rooms',
            'href'     => admin_url('cases/courts/manage_rooms'),
            'position' => 8,
        ]);
    }
}

// Register module-specific permissions
hooks()->add_action('admin_init', 'cases_permissions');

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