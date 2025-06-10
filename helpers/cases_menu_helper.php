<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cases Module Menu Helper
 * Provides enhanced navigation and menu management for the Cases module
 */

if (!function_exists('render_cases_navigation')) {
    /**
     * Render context-aware navigation for cases module
     * @param string $current_page Current page identifier
     * @param array $options Navigation options
     * @return string HTML navigation
     */
    function render_cases_navigation($current_page = '', $options = []) {
        $CI = &get_instance();
        
        $navigation_items = get_user_accessible_menu_items();
        $html = '<div class="cases-navigation">';
        
        // Add breadcrumb navigation
        if (!empty($options['show_breadcrumbs'])) {
            $html .= render_cases_breadcrumbs($current_page);
        }
        
        // Add quick action bar
        if (!empty($options['show_quick_actions'])) {
            $html .= render_cases_quick_actions($current_page);
        }
        
        // Add tab navigation for multi-section pages
        if (!empty($options['tabs'])) {
            $html .= render_cases_tabs($options['tabs'], $current_page);
        }
        
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('render_cases_breadcrumbs')) {
    /**
     * Render breadcrumb navigation specific to cases module
     * @param string $current_page Current page identifier
     * @return string HTML breadcrumbs
     */
    function render_cases_breadcrumbs($current_page) {
        $breadcrumb_config = [
            'dashboard' => [
                ['Legal Practice', admin_url('cases/caseboard')]
            ],
            'consultations' => [
                ['Legal Practice', admin_url('cases/caseboard')],
                ['Consultations', '']
            ],
            'cases' => [
                ['Legal Practice', admin_url('cases/caseboard')],
                ['Active Cases', '']
            ],
            'hearings' => [
                ['Legal Practice', admin_url('cases/caseboard')],
                ['Hearings', '']
            ],
            'causelist' => [
                ['Legal Practice', admin_url('cases/caseboard')],
                ['Hearings', admin_url('cases/hearings')],
                ['Daily Cause List', '']
            ],
            'case_details' => [
                ['Legal Practice', admin_url('cases/caseboard')],
                ['Active Cases', admin_url('cases?tab=cases')],
                ['Case Details', '']
            ]
        ];
        
        if (!isset($breadcrumb_config[$current_page])) {
            return '';
        }
        
        $html = '<nav class="cases-breadcrumb" style="margin-bottom: 20px;">';
        $html .= '<ol class="breadcrumb" style="background: var(--cases-bg-tertiary); border: 1px solid var(--cases-border); margin-bottom: 0;">';
        
        $breadcrumbs = $breadcrumb_config[$current_page];
        $total = count($breadcrumbs);
        
        foreach ($breadcrumbs as $index => $crumb) {
            $is_last = ($index === $total - 1);
            
            if ($is_last || empty($crumb[1])) {
                $html .= '<li class="active">' . htmlspecialchars($crumb[0]) . '</li>';
            } else {
                $html .= '<li><a href="' . $crumb[1] . '">' . htmlspecialchars($crumb[0]) . '</a></li>';
            }
        }
        
        $html .= '</ol></nav>';
        return $html;
    }
}

if (!function_exists('render_cases_quick_actions')) {
    /**
     * Render context-aware quick action buttons
     * @param string $current_page Current page identifier
     * @return string HTML quick actions
     */
    function render_cases_quick_actions($current_page) {
        $actions = [];
        
        // Define context-specific actions
        switch ($current_page) {
            case 'dashboard':
                $actions = [
                    ['Add Consultation', admin_url('cases?new_consultation=1'), 'fa fa-plus', 'primary'],
                    ['Today\'s Hearings', admin_url('cases/hearings/causelist'), 'fa fa-calendar-day', 'info'],
                    ['View All Cases', admin_url('cases?tab=cases'), 'fa fa-briefcase', 'default']
                ];
                break;
                
            case 'consultations':
                $actions = [
                    ['New Consultation', '#', 'fa fa-plus', 'primary', 'data-toggle="modal" data-target="#consultationModal"'],
                    ['View Dashboard', admin_url('cases/caseboard'), 'fa fa-tachometer-alt', 'default']
                ];
                break;
                
            case 'cases':
                $actions = [
                    ['Add Hearing', admin_url('cases/hearings/add'), 'fa fa-gavel', 'success'],
                    ['Upload Documents', admin_url('documents/upload'), 'fa fa-upload', 'info'],
                    ['View Dashboard', admin_url('cases/caseboard'), 'fa fa-tachometer-alt', 'default']
                ];
                break;
                
            case 'hearings':
                $actions = [
                    ['Schedule Hearing', admin_url('cases/hearings/add'), 'fa fa-plus', 'primary'],
                    ['Today\'s Cause List', admin_url('cases/hearings/causelist'), 'fa fa-calendar-day', 'warning'],
                    ['Print Cause List', 'javascript:window.print()', 'fa fa-print', 'default']
                ];
                break;
                
            case 'causelist':
                $actions = [
                    ['Schedule Hearing', admin_url('cases/hearings/add'), 'fa fa-plus', 'primary'],
                    ['All Hearings', admin_url('cases/hearings'), 'fa fa-list', 'default'],
                    ['Print List', 'javascript:window.print()', 'fa fa-print', 'success']
                ];
                break;
        }
        
        if (empty($actions)) {
            return '';
        }
        
        $html = '<div class="cases-quick-actions" style="margin-bottom: 20px; text-align: right;">';
        
        foreach ($actions as $action) {
            $name = $action[0];
            $href = $action[1];
            $icon = $action[2];
            $type = $action[3];
            $extra_attrs = isset($action[4]) ? $action[4] : '';
            
            $html .= '<a href="' . $href . '" class="cases-btn cases-btn-' . $type . '" style="margin-left: 8px;" ' . $extra_attrs . '>';
            $html .= '<i class="' . $icon . '"></i> ' . htmlspecialchars($name);
            $html .= '</a>';
        }
        
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('render_cases_tabs')) {
    /**
     * Render tab navigation for multi-section pages
     * @param array $tabs Tab configuration
     * @param string $current_page Current page identifier
     * @return string HTML tabs
     */
    function render_cases_tabs($tabs, $current_page) {
        $html = '<div class="cases-tabs-container">';
        $html .= '<ul class="nav nav-tabs cases-nav-tabs" style="margin-bottom: 30px;">';
        
        foreach ($tabs as $tab_id => $tab_config) {
            $is_active = ($tab_id === $current_page);
            $active_class = $is_active ? ' active' : '';
            $badge = isset($tab_config['badge']) ? ' <span class="badge">' . $tab_config['badge'] . '</span>' : '';
            
            $html .= '<li class="' . $active_class . '">';
            $html .= '<a href="' . $tab_config['href'] . '" data-tab="' . $tab_id . '">';
            $html .= '<i class="' . $tab_config['icon'] . '"></i> ';
            $html .= htmlspecialchars($tab_config['name']);
            $html .= $badge;
            $html .= '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('get_cases_menu_stats')) {
    /**
     * Get real-time statistics for menu badges
     * @return array Statistics for menu items
     */
    function get_cases_menu_stats() {
        $CI = &get_instance();
        $stats = [];
        
        try {
            // Only calculate stats if user has permission
            if (!has_permission('cases', '', 'view')) {
                return $stats;
            }
            
            $today = date('Y-m-d');
            
            // Pending consultations (not yet converted to litigation)
            $CI->db->where('phase', 'consultation');
            $stats['pending_consultations'] = $CI->db->count_all_results(db_prefix() . 'case_consultations');
            
            // Active cases
            $stats['active_cases'] = $CI->db->count_all_results(db_prefix() . 'cases');
            
            // Today's hearings (both scheduled for today and next_date is today)
            $CI->db->group_start();
            $CI->db->where('DATE(date)', $today);
            $CI->db->or_where('DATE(next_date)', $today);
            $CI->db->group_end();
            $stats['today_hearings'] = $CI->db->count_all_results(db_prefix() . 'hearings');
            
            // Upcoming hearings (next 7 days)
            $next_week = date('Y-m-d', strtotime('+7 days'));
            $CI->db->where('date >=', $today);
            $CI->db->where('date <=', $next_week);
            $CI->db->where('status !=', 'Completed');
            $CI->db->where('status !=', 'Cancelled');
            $stats['upcoming_hearings'] = $CI->db->count_all_results(db_prefix() . 'hearings');
            
            // Overdue hearings (past due but not completed)
            $CI->db->where('date <', $today);
            $CI->db->where('status', 'Scheduled');
            $stats['overdue_hearings'] = $CI->db->count_all_results(db_prefix() . 'hearings');
            
            // Recent consultations (last 7 days)
            $week_ago = date('Y-m-d', strtotime('-7 days'));
            $CI->db->where('date_added >=', $week_ago);
            $stats['recent_consultations'] = $CI->db->count_all_results(db_prefix() . 'case_consultations');
            
        } catch (Exception $e) {
            log_message('error', 'Error calculating cases menu stats: ' . $e->getMessage());
        }
        
        return $stats;
    }
}

if (!function_exists('render_cases_sidebar_widget')) {
    /**
     * Render a sidebar widget for cases overview
     * @param array $options Widget options
     * @return string HTML widget
     */
    function render_cases_sidebar_widget($options = []) {
        if (!has_permission('cases', '', 'view')) {
            return '';
        }
        
        $stats = get_cases_menu_stats();
        $title = $options['title'] ?? 'Legal Practice Overview';
        $show_actions = $options['show_actions'] ?? true;
        
        $html = '<div class="panel panel-default cases-sidebar-widget">';
        $html .= '<div class="panel-heading">';
        $html .= '<h4 class="panel-title"><i class="fa fa-balance-scale"></i> ' . htmlspecialchars($title) . '</h4>';
        $html .= '</div>';
        $html .= '<div class="panel-body">';
        
        // Quick stats
        $html .= '<div class="row">';
        
        if (isset($stats['pending_consultations'])) {
            $html .= '<div class="col-xs-6 text-center">';
            $html .= '<div class="stat-item">';
            $html .= '<div class="stat-number text-info">' . $stats['pending_consultations'] . '</div>';
            $html .= '<div class="stat-label">Consultations</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        if (isset($stats['active_cases'])) {
            $html .= '<div class="col-xs-6 text-center">';
            $html .= '<div class="stat-item">';
            $html .= '<div class="stat-number text-primary">' . $stats['active_cases'] . '</div>';
            $html .= '<div class="stat-label">Active Cases</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        if (isset($stats['today_hearings']) && $stats['today_hearings'] > 0) {
            $html .= '<hr style="margin: 15px 0;">';
            $html .= '<div class="alert alert-warning text-center" style="margin-bottom: 15px;">';
            $html .= '<i class="fa fa-calendar-day"></i> ';
            $html .= '<strong>' . $stats['today_hearings'] . ' hearing(s) today</strong>';
            $html .= '</div>';
        }
        
        if (isset($stats['overdue_hearings']) && $stats['overdue_hearings'] > 0) {
            $html .= '<div class="alert alert-danger text-center" style="margin-bottom: 15px;">';
            $html .= '<i class="fa fa-exclamation-triangle"></i> ';
            $html .= '<strong>' . $stats['overdue_hearings'] . ' overdue hearing(s)</strong>';
            $html .= '</div>';
        }
        
        // Quick actions
        if ($show_actions) {
            $html .= '<div class="widget-actions">';
            $html .= '<a href="' . admin_url('cases/caseboard') . '" class="btn btn-default btn-block btn-sm">';
            $html .= '<i class="fa fa-tachometer-alt"></i> Dashboard';
            $html .= '</a>';
            
            if (has_permission('cases', '', 'create')) {
                $html .= '<a href="' . admin_url('cases?new_consultation=1') . '" class="btn btn-primary btn-block btn-sm" style="margin-top: 5px;">';
                $html .= '<i class="fa fa-plus"></i> New Consultation';
                $html .= '</a>';
            }
            
            $html .= '<a href="' . admin_url('cases/hearings/causelist') . '" class="btn btn-info btn-block btn-sm" style="margin-top: 5px;">';
            $html .= '<i class="fa fa-calendar-day"></i> Today\'s Cause List';
            $html .= '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('add_cases_admin_dashboard_widget')) {
    /**
     * Add cases widget to admin dashboard
     */
    function add_cases_admin_dashboard_widget() {
        if (!has_permission('cases', '', 'view')) {
            return;
        }
        
        // Register dashboard widget
        hooks()->add_action('admin_init', function() {
            if (function_exists('add_dashboard_widget')) {
                add_dashboard_widget('cases_overview', 'Legal Practice', function() {
                    return render_cases_sidebar_widget([
                        'title' => 'Legal Practice',
                        'show_actions' => true
                    ]);
                });
            }
        });
    }
}

if (!function_exists('render_cases_context_menu')) {
    /**
     * Render context-sensitive menu for cases
     * @param string $context Current context (case, consultation, hearing)
     * @param array $data Context data
     * @return string HTML context menu
     */
    function render_cases_context_menu($context, $data = []) {
        $html = '<div class="cases-context-menu dropdown">';
        $html .= '<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">';
        $html .= '<i class="fa fa-ellipsis-v"></i> Actions <span class="caret"></span>';
        $html .= '</button>';
        $html .= '<ul class="dropdown-menu dropdown-menu-right">';
        
        switch ($context) {
            case 'case':
                $case_id = $data['id'] ?? 0;
                $html .= '<li><a href="' . admin_url('cases/details?id=' . $case_id) . '">';
                $html .= '<i class="fa fa-eye"></i> View Details</a></li>';
                
                if (has_permission('cases', '', 'edit')) {
                    $html .= '<li><a href="' . admin_url('cases/hearings/add?case_id=' . $case_id) . '">';
                    $html .= '<i class="fa fa-gavel"></i> Add Hearing</a></li>';
                }
                
                $html .= '<li><a href="' . admin_url('documents/upload') . '" onclick="setCaseContext(' . $case_id . ')">';
                $html .= '<i class="fa fa-upload"></i> Upload Document</a></li>';
                
                $html .= '<li class="divider"></li>';
                $html .= '<li><a href="#" onclick="generateCaseReport(' . $case_id . ')">';
                $html .= '<i class="fa fa-file-pdf"></i> Generate Report</a></li>';
                break;
                
            case 'consultation':
                $consultation_id = $data['id'] ?? 0;
                $phase = $data['phase'] ?? 'consultation';
                
                $html .= '<li><a href="#" onclick="viewConsultationNote(' . $consultation_id . ')">';
                $html .= '<i class="fa fa-eye"></i> View Notes</a></li>';
                
                if (has_permission('cases', '', 'edit')) {
                    $html .= '<li><a href="#" onclick="editConsultation(' . $consultation_id . ')">';
                    $html .= '<i class="fa fa-edit"></i> Edit</a></li>';
                }
                
                if ($phase === 'consultation' && has_permission('cases', '', 'create')) {
                    $html .= '<li class="divider"></li>';
                    $html .= '<li><a href="#" onclick="upgradeToLitigation(' . $consultation_id . ')">';
                    $html .= '<i class="fa fa-arrow-up"></i> Upgrade to Case</a></li>';
                }
                break;
                
            case 'hearing':
                $hearing_id = $data['id'] ?? 0;
                $status = $data['status'] ?? 'Scheduled';
                
                $html .= '<li><a href="' . admin_url('cases/hearings/edit/' . $hearing_id) . '">';
                $html .= '<i class="fa fa-edit"></i> Edit Details</a></li>';
                
                if ($status !== 'Completed') {
                    $html .= '<li><a href="' . admin_url('cases/hearings/quick_update/' . $hearing_id) . '">';
                    $html .= '<i class="fa fa-clock"></i> Quick Update</a></li>';
                }
                
                $html .= '<li class="divider"></li>';
                $html .= '<li><a href="#" onclick="addHearingDocument(' . $hearing_id . ')">';
                $html .= '<i class="fa fa-paperclip"></i> Add Document</a></li>';
                break;
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('render_cases_status_indicator')) {
    /**
     * Render status indicator with tooltip
     * @param string $status Status value
     * @param string $type Type of status (case, hearing, consultation)
     * @param array $options Additional options
     * @return string HTML status indicator
     */
    function render_cases_status_indicator($status, $type = 'case', $options = []) {
        $status_config = [
            'case' => [
                'active' => ['class' => 'success', 'icon' => 'fa fa-check-circle', 'text' => 'Active'],
                'on_hold' => ['class' => 'warning', 'icon' => 'fa fa-pause-circle', 'text' => 'On Hold'],
                'closed' => ['class' => 'default', 'icon' => 'fa fa-times-circle', 'text' => 'Closed'],
                'dismissed' => ['class' => 'danger', 'icon' => 'fa fa-ban', 'text' => 'Dismissed']
            ],
            'hearing' => [
                'scheduled' => ['class' => 'info', 'icon' => 'fa fa-calendar', 'text' => 'Scheduled'],
                'completed' => ['class' => 'success', 'icon' => 'fa fa-check', 'text' => 'Completed'],
                'adjourned' => ['class' => 'warning', 'icon' => 'fa fa-clock', 'text' => 'Adjourned'],
                'cancelled' => ['class' => 'danger', 'icon' => 'fa fa-times', 'text' => 'Cancelled']
            ],
            'consultation' => [
                'consultation' => ['class' => 'info', 'icon' => 'fa fa-comments', 'text' => 'Consultation'],
                'litigation' => ['class' => 'success', 'icon' => 'fa fa-briefcase', 'text' => 'Litigation']
            ]
        ];
        
        $status_key = strtolower($status);
        $config = $status_config[$type][$status_key] ?? [
            'class' => 'default',
            'icon' => 'fa fa-question-circle',
            'text' => ucfirst($status)
        ];
        
        $show_icon = $options['show_icon'] ?? true;
        $show_text = $options['show_text'] ?? true;
        $tooltip = $options['tooltip'] ?? '';
        
        $html = '<span class="label label-' . $config['class'] . '"';
        if ($tooltip) {
            $html .= ' title="' . htmlspecialchars($tooltip) . '" data-toggle="tooltip"';
        }
        $html .= '>';
        
        if ($show_icon) {
            $html .= '<i class="' . $config['icon'] . '"></i> ';
        }
        
        if ($show_text) {
            $html .= htmlspecialchars($config['text']);
        }
        
        $html .= '</span>';
        
        return $html;
    }
}

if (!function_exists('render_cases_priority_indicator')) {
    /**
     * Render priority indicator
     * @param string $priority Priority level (high, medium, low)
     * @param array $options Display options
     * @return string HTML priority indicator
     */
    function render_cases_priority_indicator($priority, $options = []) {
        $priority_config = [
            'high' => ['class' => 'danger', 'icon' => 'fa fa-exclamation-triangle', 'text' => 'High Priority'],
            'medium' => ['class' => 'warning', 'icon' => 'fa fa-exclamation-circle', 'text' => 'Medium Priority'],
            'low' => ['class' => 'info', 'icon' => 'fa fa-info-circle', 'text' => 'Low Priority']
        ];
        
        $priority_key = strtolower($priority);
        if (!isset($priority_config[$priority_key])) {
            return '';
        }
        
        $config = $priority_config[$priority_key];
        $size = $options['size'] ?? 'sm';
        $show_text = $options['show_text'] ?? false;
        
        $html = '<span class="label label-' . $config['class'] . '">';
        $html .= '<i class="' . $config['icon'] . '"></i>';
        
        if ($show_text) {
            $html .= ' ' . htmlspecialchars($config['text']);
        }
        
        $html .= '</span>';
        
        return $html;
    }
}

if (!function_exists('render_cases_notification_center')) {
    /**
     * Render notification center for cases module
     * @return string HTML notification center
     */
    function render_cases_notification_center() {
        if (!has_permission('cases', '', 'view')) {
            return '';
        }
        
        $stats = get_cases_menu_stats();
        $notifications = [];
        
        // Check for urgent notifications
        if (isset($stats['today_hearings']) && $stats['today_hearings'] > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fa fa-calendar-day',
                'message' => $stats['today_hearings'] . ' hearing(s) scheduled for today',
                'action' => ['text' => 'View Cause List', 'href' => admin_url('cases/hearings/causelist')]
            ];
        }
        
        if (isset($stats['overdue_hearings']) && $stats['overdue_hearings'] > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'fa fa-exclamation-triangle',
                'message' => $stats['overdue_hearings'] . ' overdue hearing(s) need attention',
                'action' => ['text' => 'Review Hearings', 'href' => admin_url('cases/hearings')]
            ];
        }
        
        if (empty($notifications)) {
            return '';
        }
        
        $html = '<div class="cases-notification-center">';
        
        foreach ($notifications as $notification) {
            $html .= '<div class="alert alert-' . $notification['type'] . ' alert-dismissible">';
            $html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            $html .= '<i class="' . $notification['icon'] . '"></i> ';
            $html .= htmlspecialchars($notification['message']);
            
            if (isset($notification['action'])) {
                $html .= ' <a href="' . $notification['action']['href'] . '" class="alert-link">';
                $html .= htmlspecialchars($notification['action']['text']);
                $html .= '</a>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

// Auto-initialize menu enhancements
if (function_exists('add_action')) {
    add_action('admin_init', 'add_cases_admin_dashboard_widget');
}