<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cases Module CSS Framework Helper
 * Provides consistent CSS loading for the Cases module
 */

if (!function_exists('load_cases_css')) {
    /**
     * Load Cases CSS Framework
     * @param array $components - Specific components to load ['buttons', 'cards', 'forms', 'tables', 'modals', 'status']
     * @param string $view - Specific view styles to load ['caseboard', 'manage', 'hearings', 'courts']
     * @return string CSS links HTML
     */
    function load_cases_css($components = ['all'], $view = null) {
        // Get the correct module assets URL
        $css_base_url = base_url('modules/cases/assets/css/');
        $css_links = [];
        
        // Always load the main framework first
        $css_links[] = '<link rel="stylesheet" href="' . $css_base_url . 'cases-framework.css?v=' . time() . '">';
        
        // Load specific components
        $available_components = ['buttons', 'cards', 'forms', 'tables', 'modals', 'status'];
        
        foreach ($available_components as $component) {
            if (in_array('all', $components) || in_array($component, $components)) {
                $component_file = $component === 'status' ? 'status-badges.css' : $component . '.css';
                $css_links[] = '<link rel="stylesheet" href="' . $css_base_url . 'components/' . $component_file . '?v=' . time() . '">';
            }
        }
        
        // Load view-specific styles
        if ($view) {
            $view_file = $css_base_url . 'views/' . $view . '.css';
            $css_links[] = '<link rel="stylesheet" href="' . $view_file . '?v=' . time() . '">';
        }
        
        return implode("\n", $css_links);
    }
}

if (!function_exists('cases_page_wrapper_start')) {
    /**
     * Start Cases page wrapper with consistent structure
     * @param string $title - Page title
     * @param string $subtitle - Page subtitle
     * @param array $actions - Page action buttons
     * @return string HTML
     */
    function cases_page_wrapper_start($title, $subtitle = '', $actions = []) {
        $html = '<div class="cases-module">';
        $html .= '<div id="wrapper">';
        $html .= '<div class="content">';
        
        // Page header
        $html .= '<div class="cases-page-header">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-8">';
        $html .= '<h1>' . htmlspecialchars($title) . '</h1>';
        
        if ($subtitle) {
            $html .= '<div class="subtitle">' . htmlspecialchars($subtitle) . '</div>';
        }
        
        $html .= '</div>';
        $html .= '<div class="col-md-4">';
        $html .= '<div class="cases-flex cases-flex-end cases-flex-wrap">';
        
        // Add action buttons
        foreach ($actions as $action) {
            $class = isset($action['class']) ? $action['class'] : 'cases-btn';
            $href = isset($action['href']) ? 'href="' . $action['href'] . '"' : '';
            $onclick = isset($action['onclick']) ? 'onclick="' . $action['onclick'] . '"' : '';
            $target = isset($action['target']) ? 'target="' . $action['target'] . '"' : '';
            $data_attrs = '';
            
            // Handle data attributes (for modals, etc.)
            if (isset($action['data'])) {
                foreach ($action['data'] as $key => $value) {
                    $data_attrs .= ' data-' . $key . '="' . htmlspecialchars($value) . '"';
                }
            }
            
            if (isset($action['href'])) {
                $html .= '<a ' . $href . ' ' . $target . ' class="' . $class . '"' . $data_attrs . '>';
            } else {
                $html .= '<button ' . $onclick . ' class="' . $class . '"' . $data_attrs . '>';
            }
            
            if (isset($action['icon'])) {
                $html .= '<i class="' . $action['icon'] . '"></i> ';
            }
            
            $html .= htmlspecialchars($action['text']);
            
            if (isset($action['href'])) {
                $html .= '</a>';
            } else {
                $html .= '</button>';
            }
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('cases_page_wrapper_end')) {
    /**
     * End Cases page wrapper
     * @return string HTML
     */
    function cases_page_wrapper_end() {
        return '</div></div></div>';
    }
}

if (!function_exists('cases_section_start')) {
    /**
     * Start a content section
     * @param string $title - Section title
     * @param array $options - Section options
     * @return string HTML
     */
    function cases_section_start($title = '', $options = []) {
        $class = isset($options['class']) ? $options['class'] : 'cases-content-section';
        $id = isset($options['id']) ? 'id="' . $options['id'] . '"' : '';
        
        $html = '<div class="' . $class . '" ' . $id . '>';
        
        if ($title) {
            $header_class = isset($options['header_class']) ? $options['header_class'] : 'cases-section-title';
            $html .= '<h3 class="' . $header_class . '">' . htmlspecialchars($title) . '</h3>';
        }
        
        return $html;
    }
}

if (!function_exists('cases_section_end')) {
    /**
     * End a content section
     * @return string HTML
     */
    function cases_section_end() {
        return '</div>';
    }
}

if (!function_exists('cases_status_badge')) {
    /**
     * Generate a status badge
     * @param string $status - Status text
     * @param string $type - Badge type
     * @param array $options - Additional options
     * @return string HTML
     */
    function cases_status_badge($status, $type = 'default', $options = []) {
        $size = isset($options['size']) ? 'cases-status-badge-' . $options['size'] : '';
        $class = 'cases-status-badge cases-status-' . strtolower($type) . ' ' . $size;
        
        if (isset($options['pulse']) && $options['pulse']) {
            $class .= ' cases-status-pulse';
        }
        
        return '<span class="' . trim($class) . '">' . htmlspecialchars($status) . '</span>';
    }
}

if (!function_exists('cases_button')) {
    /**
     * Generate a Cases button
     * @param string $text - Button text
     * @param array $options - Button options
     * @return string HTML
     */
    function cases_button($text, $options = []) {
        $type = isset($options['type']) ? $options['type'] : 'default';
        $size = isset($options['size']) ? 'cases-btn-' . $options['size'] : '';
        $class = 'cases-btn cases-btn-' . $type . ' ' . $size;
        
        if (isset($options['block']) && $options['block']) {
            $class .= ' cases-btn-block';
        }
        
        if (isset($options['icon']) && $options['icon']) {
            $class .= ' cases-btn-icon';
        }
        
        if (isset($options['loading']) && $options['loading']) {
            $class .= ' cases-btn-loading';
        }
        
        $attributes = [];
        
        if (isset($options['href'])) {
            $tag = 'a';
            $attributes[] = 'href="' . $options['href'] . '"';
        } else {
            $tag = 'button';
            $type_attr = isset($options['button_type']) ? $options['button_type'] : 'button';
            $attributes[] = 'type="' . $type_attr . '"';
        }
        
        if (isset($options['onclick'])) {
            $attributes[] = 'onclick="' . $options['onclick'] . '"';
        }
        
        if (isset($options['id'])) {
            $attributes[] = 'id="' . $options['id'] . '"';
        }
        
        if (isset($options['disabled']) && $options['disabled']) {
            $attributes[] = 'disabled';
        }
        
        // Handle data attributes
        if (isset($options['data'])) {
            foreach ($options['data'] as $key => $value) {
                $attributes[] = 'data-' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        $attributes[] = 'class="' . trim($class) . '"';
        
        $html = '<' . $tag . ' ' . implode(' ', $attributes) . '>';
        
        if (isset($options['icon'])) {
            $html .= '<i class="' . $options['icon'] . '"></i> ';
        }
        
        $html .= htmlspecialchars($text);
        $html .= '</' . $tag . '>';
        
        return $html;
    }
}

if (!function_exists('cases_action_button')) {
    /**
     * Generate an action button (smaller, for tables/cards)
     * @param string $text - Button text
     * @param array $options - Button options
     * @return string HTML
     */
    function cases_action_button($text, $options = []) {
        $type = isset($options['type']) ? $options['type'] : 'default';
        $class = 'cases-action-btn cases-btn-' . $type;
        
        $attributes = [];
        
        if (isset($options['href'])) {
            $tag = 'a';
            $attributes[] = 'href="' . $options['href'] . '"';
        } else {
            $tag = 'button';
            $attributes[] = 'type="button"';
        }
        
        if (isset($options['onclick'])) {
            $attributes[] = 'onclick="' . $options['onclick'] . '"';
        }
        
        if (isset($options['title'])) {
            $attributes[] = 'title="' . htmlspecialchars($options['title']) . '"';
        }
        
        // Handle data attributes
        if (isset($options['data'])) {
            foreach ($options['data'] as $key => $value) {
                $attributes[] = 'data-' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        $attributes[] = 'class="' . $class . '"';
        
        $html = '<' . $tag . ' ' . implode(' ', $attributes) . '>';
        $html .= htmlspecialchars($text);
        $html .= '</' . $tag . '>';
        
        return $html;
    }
}

if (!function_exists('cases_empty_state')) {
    /**
     * Generate an empty state
     * @param string $title - Empty state title
     * @param string $message - Empty state message
     * @param array $options - Additional options
     * @return string HTML
     */
    function cases_empty_state($title, $message = '', $options = []) {
        $icon = isset($options['icon']) ? $options['icon'] : 'fas fa-inbox';
        
        $html = '<div class="cases-empty-state">';
        $html .= '<i class="' . $icon . '"></i>';
        $html .= '<h5>' . htmlspecialchars($title) . '</h5>';
        
        if ($message) {
            $html .= '<p>' . htmlspecialchars($message) . '</p>';
        }
        
        if (isset($options['action'])) {
            $action = $options['action'];
            $html .= cases_button($action['text'], $action);
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('cases_count_badge')) {
    /**
     * Generate a count badge
     * @param string|int $count - Count number
     * @return string HTML
     */
    function cases_count_badge($count) {
        return '<span class="cases-count-badge">' . htmlspecialchars($count) . '</span>';
    }
}

if (!function_exists('cases_loading_state')) {
    /**
     * Generate a loading state
     * @param string $message - Loading message
     * @return string HTML
     */
    function cases_loading_state($message = 'Loading...') {
        $html = '<div class="cases-loading-state">';
        $html .= '<div class="cases-loading-spinner">';
        $html .= '<i class="fas fa-spinner fa-spin"></i>';
        $html .= '</div>';
        $html .= '<p>' . htmlspecialchars($message) . '</p>';
        $html .= '</div>';
        
        return $html;
    }
}