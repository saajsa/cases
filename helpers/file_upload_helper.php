<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Secure File Upload Helper for Cases Module
 * Provides comprehensive file upload security and validation
 */

if (!function_exists('cases_secure_file_upload')) {
    /**
     * Securely handle file upload with comprehensive validation
     * @param array $file $_FILES array element
     * @param array $config Upload configuration
     * @return array Result with 'success', 'file_path', 'error' keys
     */
    function cases_secure_file_upload($file, $config = []) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        // Default configuration
        $default_config = [
            'upload_path' => FCPATH . 'uploads/cases/',
            'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt'],
            'allowed_mimes' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'text/plain'
            ],
            'max_size' => 10485760, // 10MB
            'max_filename_length' => 100,
            'encrypt_name' => true,
            'scan_virus' => false,
            'quarantine_suspicious' => true
        ];
        
        $config = array_merge($default_config, $config);
        
        $result = [
            'success' => false,
            'file_path' => '',
            'original_name' => '',
            'final_name' => '',
            'error' => ''
        ];
        
        try {
            // Basic file validation
            $validation = cases_validate_file_upload($file, $config['allowed_mimes'], $config['max_size']);
            if (!$validation['valid']) {
                $result['error'] = $validation['error'];
                return $result;
            }
            
            // Validate file extension
            $original_name = $file['name'];
            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $config['allowed_types'])) {
                $result['error'] = 'File type not allowed: ' . $extension;
                cases_log_security_event('Blocked file upload - invalid extension', [
                    'filename' => $original_name,
                    'extension' => $extension
                ], 'warning');
                return $result;
            }
            
            // Validate filename length
            if (strlen($original_name) > $config['max_filename_length']) {
                $result['error'] = 'Filename too long (max ' . $config['max_filename_length'] . ' characters)';
                return $result;
            }
            
            // Check for executable files disguised as documents
            if (cases_is_executable_file($file['tmp_name'])) {
                $result['error'] = 'File appears to be executable and is not allowed';
                cases_log_security_event('Blocked executable file upload', [
                    'filename' => $original_name,
                    'mime_detected' => mime_content_type($file['tmp_name'])
                ], 'error');
                return $result;
            }
            
            // Create upload directory if it doesn't exist
            if (!file_exists($config['upload_path'])) {
                if (!mkdir($config['upload_path'], 0755, true)) {
                    $result['error'] = 'Failed to create upload directory';
                    return $result;
                }
            }
            
            // Generate secure filename
            if ($config['encrypt_name']) {
                $final_name = cases_generate_secure_filename($original_name, $extension);
            } else {
                $final_name = cases_sanitize_filename($original_name);
            }
            
            $file_path = $config['upload_path'] . $final_name;
            
            // Check if file already exists
            if (file_exists($file_path)) {
                $final_name = cases_generate_unique_filename($config['upload_path'], $final_name);
                $file_path = $config['upload_path'] . $final_name;
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                $result['error'] = 'Failed to move uploaded file';
                return $result;
            }
            
            // Set secure permissions
            chmod($file_path, 0644);
            
            // Additional security checks on uploaded file
            if (!cases_verify_file_integrity($file_path, $extension)) {
                unlink($file_path); // Delete potentially malicious file
                $result['error'] = 'File failed security verification';
                cases_log_security_event('File failed integrity check', [
                    'filename' => $original_name,
                    'final_name' => $final_name
                ], 'error');
                return $result;
            }
            
            // Virus scanning (if enabled and available)
            if ($config['scan_virus'] && function_exists('clamav_scan_file')) {
                $scan_result = clamav_scan_file($file_path);
                if ($scan_result !== '') {
                    unlink($file_path);
                    $result['error'] = 'File failed virus scan';
                    cases_log_security_event('File failed virus scan', [
                        'filename' => $original_name,
                        'scan_result' => $scan_result
                    ], 'error');
                    return $result;
                }
            }
            
            // Log successful upload
            cases_log_security_event('File uploaded successfully', [
                'original_name' => $original_name,
                'final_name' => $final_name,
                'size' => $file['size'],
                'mime_type' => mime_content_type($file_path)
            ], 'info');
            
            $result['success'] = true;
            $result['file_path'] = $file_path;
            $result['original_name'] = $original_name;
            $result['final_name'] = $final_name;
            
        } catch (Exception $e) {
            $result['error'] = 'Upload failed: ' . $e->getMessage();
            log_message('error', 'File upload error: ' . $e->getMessage());
        }
        
        return $result;
    }
}

if (!function_exists('cases_generate_secure_filename')) {
    /**
     * Generate a secure filename
     * @param string $original_name Original filename
     * @param string $extension File extension
     * @return string Secure filename
     */
    function cases_generate_secure_filename($original_name, $extension) {
        // Generate random filename to prevent directory traversal and predictable names
        $random_name = bin2hex(random_bytes(16));
        $timestamp = time();
        $user_id = get_staff_user_id() ?? 'anonymous';
        
        return $timestamp . '_' . $user_id . '_' . $random_name . '.' . $extension;
    }
}

if (!function_exists('cases_sanitize_filename')) {
    /**
     * Sanitize filename while preserving readability
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    function cases_sanitize_filename($filename) {
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple consecutive underscores
        $filename = preg_replace('/_{2,}/', '_', $filename);
        
        // Remove leading/trailing underscores and dots
        $filename = trim($filename, '_.');
        
        // Ensure filename isn't empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }
        
        return $filename;
    }
}

if (!function_exists('cases_generate_unique_filename')) {
    /**
     * Generate unique filename if file already exists
     * @param string $upload_path Upload directory path
     * @param string $filename Proposed filename
     * @return string Unique filename
     */
    function cases_generate_unique_filename($upload_path, $filename) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $counter = 1;
        
        while (file_exists($upload_path . $filename)) {
            $filename = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }
        
        return $filename;
    }
}

if (!function_exists('cases_is_executable_file')) {
    /**
     * Check if file is potentially executable
     * @param string $file_path Path to file
     * @return bool True if file appears executable
     */
    function cases_is_executable_file($file_path) {
        // Read first few bytes to check for executable signatures
        $handle = fopen($file_path, 'rb');
        if (!$handle) {
            return false;
        }
        
        $header = fread($handle, 4);
        fclose($handle);
        
        // Check for common executable file signatures
        $executable_signatures = [
            "\x4D\x5A",     // PE/EXE (Windows)
            "\x7F\x45\x4C\x46", // ELF (Linux)
            "\xFE\xED\xFA\xCE", // Mach-O (macOS)
            "\xFE\xED\xFA\xCF", // Mach-O 64-bit
            "\xCA\xFE\xBA\xBE", // Java class
            "#!/bin/",      // Shell script
            "#!/usr/",      // Shell script
            "<?php",        // PHP script
            "<script",      // JavaScript/HTML
        ];
        
        foreach ($executable_signatures as $signature) {
            if (strpos($header, $signature) === 0) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('cases_verify_file_integrity')) {
    /**
     * Verify file integrity based on extension and content
     * @param string $file_path Path to uploaded file
     * @param string $extension Expected file extension
     * @return bool True if file passes integrity checks
     */
    function cases_verify_file_integrity($file_path, $extension) {
        $mime_type = mime_content_type($file_path);
        
        // Define expected MIME types for each extension
        $mime_map = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'txt' => ['text/plain', 'text/x-plain'],
        ];
        
        if (!isset($mime_map[$extension])) {
            return false;
        }
        
        // Check if detected MIME type matches expected types
        if (!in_array($mime_type, $mime_map[$extension])) {
            cases_log_security_event('MIME type mismatch', [
                'file_path' => $file_path,
                'expected_extension' => $extension,
                'detected_mime' => $mime_type,
                'expected_mimes' => $mime_map[$extension]
            ], 'warning');
            return false;
        }
        
        // Additional checks for specific file types
        switch ($extension) {
            case 'pdf':
                return cases_verify_pdf_integrity($file_path);
            case 'jpg':
            case 'jpeg':
            case 'png':
                return cases_verify_image_integrity($file_path);
            default:
                return true;
        }
    }
}

if (!function_exists('cases_verify_pdf_integrity')) {
    /**
     * Verify PDF file integrity
     * @param string $file_path Path to PDF file
     * @return bool True if PDF is valid
     */
    function cases_verify_pdf_integrity($file_path) {
        $handle = fopen($file_path, 'rb');
        if (!$handle) {
            return false;
        }
        
        // Check PDF header
        $header = fread($handle, 4);
        if ($header !== '%PDF') {
            fclose($handle);
            return false;
        }
        
        // Check for PDF EOF marker
        fseek($handle, -10, SEEK_END);
        $footer = fread($handle, 10);
        fclose($handle);
        
        return strpos($footer, '%%EOF') !== false;
    }
}

if (!function_exists('cases_verify_image_integrity')) {
    /**
     * Verify image file integrity
     * @param string $file_path Path to image file
     * @return bool True if image is valid
     */
    function cases_verify_image_integrity($file_path) {
        // Use getimagesize to verify image
        $image_info = @getimagesize($file_path);
        
        if ($image_info === false) {
            return false;
        }
        
        // Check for reasonable image dimensions
        if ($image_info[0] <= 0 || $image_info[1] <= 0) {
            return false;
        }
        
        // Check for suspiciously large images (potential DoS)
        if ($image_info[0] > 10000 || $image_info[1] > 10000) {
            cases_log_security_event('Suspiciously large image uploaded', [
                'file_path' => $file_path,
                'width' => $image_info[0],
                'height' => $image_info[1]
            ], 'warning');
            return false;
        }
        
        return true;
    }
}