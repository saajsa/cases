<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * UI Components Helper for Cases Module
 * Provides standardized UI components with consistent styling
 */

if (!function_exists('cases_form_select')) {
    /**
     * Generate standardized select dropdown
     * @param string $name Field name attribute
     * @param array $options Array of options (value => label)
     * @param mixed $selected Currently selected value
     * @param array $attributes Additional HTML attributes
     * @return string HTML select element
     */
    function cases_form_select($name, $options = [], $selected = null, $attributes = []) {
        // Default attributes
        $default_attrs = [
            'class' => 'cases-form-select',
            'id' => $name
        ];
        
        // Merge with provided attributes
        $attrs = array_merge($default_attrs, $attributes);
        
        // Build attributes string
        $attr_string = '';
        foreach ($attrs as $key => $value) {
            if ($key === 'class') {
                // Ensure cases-form-select is always included
                $classes = explode(' ', $value);
                if (!in_array('cases-form-select', $classes)) {
                    $classes[] = 'cases-form-select';
                }
                $value = implode(' ', array_unique($classes));
            }
            $attr_string .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }
        
        // Build select element
        $html = '<select name="' . htmlspecialchars($name, ENT_QUOTES) . '"' . $attr_string . '>';
        
        // Add options
        foreach ($options as $value => $label) {
            $selected_attr = ($value == $selected) ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars($value, ENT_QUOTES) . '"' . $selected_attr . '>';
            $html .= htmlspecialchars($label, ENT_QUOTES);
            $html .= '</option>';
        }
        
        $html .= '</select>';
        
        return $html;
    }
}

if (!function_exists('cases_form_select_with_search')) {
    /**
     * Generate searchable select dropdown with enhanced UI
     * @param string $name Field name attribute
     * @param array $options Array of options (value => label)
     * @param mixed $selected Currently selected value
     * @param array $config Configuration options
     * @return string HTML with select and JavaScript for search functionality
     */
    function cases_form_select_with_search($name, $options = [], $selected = null, $config = []) {
        $default_config = [
            'placeholder' => 'Select an option...',
            'search_placeholder' => 'Search...',
            'no_results_text' => 'No results found',
            'class' => 'cases-form-select-search',
            'allow_clear' => true,
            'multiple' => false
        ];
        
        $config = array_merge($default_config, $config);
        
        // Generate unique ID
        $unique_id = $name . '_' . uniqid();
        
        // Build the enhanced select
        $html = '<div class="cases-select-wrapper" data-select-id="' . $unique_id . '">';
        
        // Hidden input to store the actual value
        $html .= '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES) . '" id="' . $unique_id . '_value" value="' . htmlspecialchars($selected ?? '', ENT_QUOTES) . '">';
        
        // Display element
        $selected_label = '';
        if ($selected && isset($options[$selected])) {
            $selected_label = $options[$selected];
        }
        
        $html .= '<div class="cases-select-display" id="' . $unique_id . '_display">';
        $html .= '<span class="cases-select-value">' . ($selected_label ?: $config['placeholder']) . '</span>';
        $html .= '<i class="fas fa-chevron-down cases-select-arrow"></i>';
        $html .= '</div>';
        
        // Dropdown container
        $html .= '<div class="cases-select-dropdown" id="' . $unique_id . '_dropdown" style="display: none;">';
        
        // Search input
        $html .= '<div class="cases-select-search">';
        $html .= '<input type="text" class="cases-form-control cases-form-control-sm" placeholder="' . htmlspecialchars($config['search_placeholder'], ENT_QUOTES) . '">';
        $html .= '</div>';
        
        // Options list
        $html .= '<div class="cases-select-options" id="' . $unique_id . '_options">';
        
        if ($config['allow_clear']) {
            $html .= '<div class="cases-select-option" data-value="">';
            $html .= '<span class="cases-option-label">Clear selection</span>';
            $html .= '</div>';
        }
        
        foreach ($options as $value => $label) {
            $selected_class = ($value == $selected) ? ' cases-option-selected' : '';
            $html .= '<div class="cases-select-option' . $selected_class . '" data-value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
            $html .= '<span class="cases-option-label">' . htmlspecialchars($label, ENT_QUOTES) . '</span>';
            $html .= '</div>';
        }
        
        $html .= '</div></div></div>';
        
        // Add JavaScript for functionality
        $html .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const wrapper = document.querySelector(\'[data-select-id="' . $unique_id . '"]\');
            if (wrapper) {
                setupCasesSelect(wrapper, ' . json_encode($config) . ');
            }
        });
        </script>';
        
        return $html;
    }
}

if (!function_exists('cases_status_select')) {
    /**
     * Generate standardized status dropdown with color-coded options
     * @param string $name Field name
     * @param mixed $selected Current value
     * @param array $statuses Available statuses
     * @param array $attributes Additional attributes
     * @return string HTML select element
     */
    function cases_status_select($name, $selected = null, $statuses = [], $attributes = []) {
        if (empty($statuses)) {
            // Default case statuses
            $statuses = [
                'active' => 'Active',
                'on_hold' => 'On Hold', 
                'completed' => 'Completed',
                'dismissed' => 'Dismissed',
                'settled' => 'Settled',
                'transferred' => 'Transferred'
            ];
        }
        
        $attributes['class'] = ($attributes['class'] ?? '') . ' cases-status-select';
        
        return cases_form_select($name, $statuses, $selected, $attributes);
    }
}

if (!function_exists('cases_hearing_status_select')) {
    /**
     * Generate hearing status dropdown
     * @param string $name Field name
     * @param mixed $selected Current value
     * @param array $attributes Additional attributes
     * @return string HTML select element
     */
    function cases_hearing_status_select($name, $selected = null, $attributes = []) {
        $statuses = [
            'Scheduled' => 'Scheduled',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
            'Adjourned' => 'Adjourned',
            'Postponed' => 'Postponed',
            'Cancelled' => 'Cancelled',
            'No Appearance' => 'No Appearance'
        ];
        
        $attributes['class'] = ($attributes['class'] ?? '') . ' cases-hearing-status-select';
        
        return cases_form_select($name, $statuses, $selected, $attributes);
    }
}

if (!function_exists('cases_priority_select')) {
    /**
     * Generate priority dropdown
     * @param string $name Field name
     * @param mixed $selected Current value
     * @param array $attributes Additional attributes
     * @return string HTML select element
     */
    function cases_priority_select($name, $selected = null, $attributes = []) {
        $priorities = [
            'low' => 'Low Priority',
            'medium' => 'Medium Priority',
            'high' => 'High Priority',
            'urgent' => 'Urgent'
        ];
        
        $attributes['class'] = ($attributes['class'] ?? '') . ' cases-priority-select';
        
        return cases_form_select($name, $priorities, $selected, $attributes);
    }
}

if (!function_exists('cases_form_input')) {
    /**
     * Generate standardized form input
     * @param string $type Input type
     * @param string $name Field name
     * @param mixed $value Current value
     * @param array $attributes Additional attributes
     * @return string HTML input element
     */
    function cases_form_input($type, $name, $value = null, $attributes = []) {
        $default_attrs = [
            'class' => 'cases-form-control',
            'id' => $name,
            'type' => $type,
            'name' => $name
        ];
        
        if ($value !== null) {
            $default_attrs['value'] = $value;
        }
        
        $attrs = array_merge($default_attrs, $attributes);
        
        // Ensure cases-form-control is always included
        if (isset($attrs['class'])) {
            $classes = explode(' ', $attrs['class']);
            if (!in_array('cases-form-control', $classes)) {
                $classes[] = 'cases-form-control';
            }
            $attrs['class'] = implode(' ', array_unique($classes));
        }
        
        $attr_string = '';
        foreach ($attrs as $key => $attr_value) {
            $attr_string .= ' ' . $key . '="' . htmlspecialchars($attr_value, ENT_QUOTES) . '"';
        }
        
        return '<input' . $attr_string . '>';
    }
}

if (!function_exists('cases_form_textarea')) {
    /**
     * Generate standardized textarea
     * @param string $name Field name
     * @param string $value Current value
     * @param array $attributes Additional attributes
     * @return string HTML textarea element
     */
    function cases_form_textarea($name, $value = '', $attributes = []) {
        $default_attrs = [
            'class' => 'cases-form-control cases-textarea',
            'id' => $name,
            'name' => $name,
            'rows' => '4'
        ];
        
        $attrs = array_merge($default_attrs, $attributes);
        
        $attr_string = '';
        foreach ($attrs as $key => $attr_value) {
            if ($key !== 'value') {
                $attr_string .= ' ' . $key . '="' . htmlspecialchars($attr_value, ENT_QUOTES) . '"';
            }
        }
        
        return '<textarea' . $attr_string . '>' . htmlspecialchars($value, ENT_QUOTES) . '</textarea>';
    }
}