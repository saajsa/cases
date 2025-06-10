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

    // Admin only items
    if (is_admin()) {
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
    register_staff_capabilities('cases', $capabilities, _l('Cases'));
}

// Add simple menu badges with better error handling
hooks()->add_action('admin_init', 'add_simple_cases_badges');

function add_simple_cases_badges() {
    // Only add badges if user has permission
    if (!has_permission('cases', '', 'view')) {
        return;
    }

    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add a small delay to ensure menu is loaded
        setTimeout(function() {
            try {
                // Enhanced fetch with better error handling
                fetch("' . admin_url('cases/get_menu_stats') . '", {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json"
                    },
                    credentials: "same-origin"
                })
                .then(function(response) {
                    console.log("Badge response status:", response.status);
                    
                    if (!response.ok) {
                        throw new Error("HTTP " + response.status);
                    }
                    
                    // Check content type
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        console.warn("Non-JSON response for badges:", contentType);
                        return response.text().then(function(text) {
                            console.error("Badge response text:", text.substring(0, 300));
                            throw new Error("Invalid response format");
                        });
                    }
                    
                    return response.json();
                })
                .then(function(data) {
                    console.log("Badge data received:", data);
                    
                    if (data && data.success) {
                        if (data.consultations > 0) {
                            addBadge("manage_cases_and_consultations", data.consultations, "warning");
                        }
                        if (data.today_hearings > 0) {
                            addBadge("cases-causelist", data.today_hearings, "danger");
                        }
                    } else {
                        console.warn("Badge request failed:", data);
                    }
                })
                .catch(function(error) {
                    console.warn("Badge loading failed:", error.message);
                    // Silently fail - badges are not critical
                });
            } catch (error) {
                console.warn("Badge script error:", error.message);
            }
        }, 1000); // 1 second delay
    });
    
    function addBadge(slug, count, type) {
        try {
            var menu = document.querySelector(\'[data-slug="\' + slug + \'"]\');
            if (menu && count > 0) {
                // Check if badge already exists
                var existingBadge = menu.querySelector(".badge");
                if (!existingBadge) {
                    menu.innerHTML += \' <span class="badge badge-\' + type + \'">\' + count + \'</span>\';
                } else {
                    existingBadge.textContent = count;
                }
            }
        } catch (error) {
            console.warn("Error adding badge:", error.message);
        }
    }
    </script>';
}

// Add hook to ensure proper CSS/JS loading
hooks()->add_action('app_admin_head', 'cases_admin_head');

function cases_admin_head() {
    // Only load on cases module pages
    $CI = &get_instance();
    
    if ($CI->router->class !== 'cases') {
        return;
    }
    
    // Add module-specific CSS and JS
    echo '<link rel="stylesheet" href="' . base_url('modules/cases/assets/css/cases-framework.css') . '?v=' . time() . '">';
    echo '<style>
        .cases-debug-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 9999;
            max-width: 300px;
            display: none;
        }
        .cases-debug-toggle {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: #007cba;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            z-index: 10000;
        }
    </style>';
    
    // Add debug toggle if in development
    if (ENVIRONMENT === 'development' || isset($_GET['debug'])) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var debugBtn = document.createElement("button");
            debugBtn.className = "cases-debug-toggle";
            debugBtn.textContent = "Debug";
            debugBtn.onclick = function() {
                window.open("' . admin_url('cases/debug') . '", "_blank");
            };
            document.body.appendChild(debugBtn);
        });
        </script>';
    }
}

// Enhanced error logging hook
hooks()->add_action('show_404', 'cases_handle_404');

function cases_handle_404() {
    $CI = &get_instance();
    
    // Check if this is a cases module 404
    if (strpos($CI->uri->uri_string(), 'cases/') !== false) {
        log_message('error', 'Cases module 404: ' . $CI->uri->uri_string());
        
        // In development, show more details
        if (ENVIRONMENT === 'development') {
            echo '<div style="background: #f8f8f8; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
            echo '<h3>Cases Module Debug Info</h3>';
            echo '<p><strong>Requested URI:</strong> ' . $CI->uri->uri_string() . '</p>';
            echo '<p><strong>Controller:</strong> ' . $CI->router->class . '</p>';
            echo '<p><strong>Method:</strong> ' . $CI->router->method . '</p>';
            echo '<p><strong>Available Methods:</strong></p>';
            
            if (class_exists('Cases')) {
                $methods = get_class_methods('Cases');
                echo '<ul>';
                foreach ($methods as $method) {
                    if (!in_array($method, ['__construct', '__destruct']) && !strpos($method, '_')) {
                        echo '<li>' . $method . '</li>';
                    }
                }
                echo '</ul>';
            }
            
            echo '<p><a href="' . admin_url('cases/debug') . '">Run Full Debug</a></p>';
            echo '</div>';
        }
    }
}