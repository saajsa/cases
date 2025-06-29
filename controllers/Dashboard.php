<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dashboard Controller
 * Handles all dashboard-related data and analytics for the Cases module
 */
class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cases_model');
        $this->load->helper('cases/rate_limiter');
        $this->load->helper('cases/security');
        
        // Set JSON header for AJAX requests
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
        }
    }

    /**
     * Main dashboard view
     */
    public function index()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        // This would render the main dashboard page if needed
        // For now, we're using the caseboard in Cases controller
        redirect(admin_url('cases/caseboard'));
    }

    /**
     * Get comprehensive dashboard statistics
     */
    public function get_stats()
    {
        try {
            if (!has_permission('cases', '', 'view')) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Access denied'
                ]);
                return;
            }

            // Enforce rate limits
            cases_enforce_rate_limit('get_stats', 30, 300); // 30 requests per 5 minutes

            $today = date('Y-m-d');
            $this_month_start = date('Y-m-01');
            $last_month_start = date('Y-m-01', strtotime('-1 month'));
            $last_month_end = date('Y-m-t', strtotime('-1 month'));
            $this_week_start = date('Y-m-d', strtotime('monday this week'));
            
            $stats = [];
            
            // === CORE COUNTS ===
            
            // Total active cases
            $stats['total_cases'] = $this->db->count_all_results(db_prefix() . 'cases');
            
            // Total consultations
            $stats['total_consultations'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
            
            // Consultation phase breakdown
            $this->db->where('phase', 'consultation');
            $stats['consultation_phase'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
            
            $this->db->where('phase', 'litigation');
            $stats['litigation_phase'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
            
            // === HEARING STATISTICS ===
            
            // Today's hearings
            $this->db->group_start();
            $this->db->where('DATE(date)', $today);
            $this->db->or_where('DATE(next_date)', $today);
            $this->db->group_end();
            $this->db->where('status !=', 'Cancelled');
            $stats['today_hearings'] = $this->db->count_all_results(db_prefix() . 'hearings');
            
            // Upcoming hearings (next 7 days)
            $next_week = date('Y-m-d', strtotime('+7 days'));
            $this->db->where('date >=', $today);
            $this->db->where('date <=', $next_week);
            $this->db->where('status !=', 'Completed');
            $this->db->where('status !=', 'Cancelled');
            $stats['upcoming_hearings'] = $this->db->count_all_results(db_prefix() . 'hearings');
            
            // Overdue hearings (past due but not completed)
            $this->db->where('date <', $today);
            $this->db->where('status', 'Scheduled');
            $stats['overdue_hearings'] = $this->db->count_all_results(db_prefix() . 'hearings');
            
            // Completed hearings this month
            $this->db->where('date >=', $this_month_start);
            $this->db->where('status', 'Completed');
            $stats['completed_hearings_month'] = $this->db->count_all_results(db_prefix() . 'hearings');
            
            // === GROWTH METRICS ===
            
            // New consultations this month vs last month
            $this->db->where('date_added >=', $this_month_start);
            $stats['consultations_this_month'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
            
            $this->db->where('date_added >=', $last_month_start);
            $this->db->where('date_added <=', $last_month_end);
            $consultations_last_month = $this->db->count_all_results(db_prefix() . 'case_consultations');
            
            $stats['consultations_growth'] = $consultations_last_month > 0 ? 
                round((($stats['consultations_this_month'] - $consultations_last_month) / $consultations_last_month) * 100, 1) : 0;
            
            // New cases this month vs last month
            $this->db->where('date_created >=', $this_month_start);
            $stats['cases_this_month'] = $this->db->count_all_results(db_prefix() . 'cases');
            
            $this->db->where('date_created >=', $last_month_start);
            $this->db->where('date_created <=', $last_month_end);
            $cases_last_month = $this->db->count_all_results(db_prefix() . 'cases');
            
            $stats['cases_growth'] = $cases_last_month > 0 ? 
                round((($stats['cases_this_month'] - $cases_last_month) / $cases_last_month) * 100, 1) : 0;
            
            // === RECENT ACTIVITY ===
            
            // Recent consultations (this week)
            $this->db->where('date_added >=', $this_week_start);
            $stats['recent_consultations'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
            
            // === SUCCESS METRICS ===
            
            // Calculate success rate based on completed vs total hearings
            $total_completed = $this->db->where('status', 'Completed')->count_all_results(db_prefix() . 'hearings');
            $total_hearings = $this->db->count_all(db_prefix() . 'hearings');
            
            $stats['success_rate'] = $total_hearings > 0 ? round(($total_completed / $total_hearings) * 100, 1) : 0;
            
            // === REVENUE DATA (if available) ===
            $stats = array_merge($stats, $this->calculateRevenue($this_month_start, $last_month_start, $last_month_end));
            
            // === ADDITIONAL METRICS ===
            $stats = array_merge($stats, $this->calculateAdditionalMetrics());
            
            // Response: return comprehensive stats
            echo json_encode([
                'success' => true,
                'stats' => $stats,
                'generated_at' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error in Dashboard::get_stats: ' . $e->getMessage());
            $this->outputError($e->getMessage());
        }
        exit;
    }

    /**
     * Get priority items that need immediate attention
     */
    public function get_priority_items()
    {
        try {
            if (!has_permission('cases', '', 'view')) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Access denied'
                ]);
                return;
            }

            // Enforce rate limits
            cases_enforce_rate_limit('get_priority_items', 20, 300); // 20 requests per 5 minutes

            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $next_week = date('Y-m-d', strtotime('+7 days'));
            
            $priority_items = [];
            
            // === TODAY'S HEARINGS ===
            $this->db->select('h.*, c.case_title, c.case_number, cl.company as client_name');
            $this->db->from(db_prefix() . 'hearings h');
            $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
            $this->db->group_start();
            $this->db->where('DATE(h.date)', $today);
            $this->db->or_where('DATE(h.next_date)', $today);
            $this->db->group_end();
            $this->db->where('h.status !=', 'Cancelled');
            $this->db->where('h.status !=', 'Completed');
            $this->db->order_by('h.time', 'ASC');
            
            $today_hearings = $this->db->get()->result_array();
            
            foreach ($today_hearings as $hearing) {
                $priority_items[] = [
                    'id' => $hearing['id'],
                    'type' => 'hearing',
                    'title' => $hearing['case_title'] ?: 'Hearing',
                    'subtitle' => $hearing['case_number'] ?: '',
                    'client' => $hearing['client_name'] ?: 'Unknown Client',
                    'date' => $hearing['date'],
                    'time' => $hearing['time'],
                    'status' => 'today',
                    'priority' => 'high',
                    'description' => $hearing['description'] ?: '',
                    'action_url' => admin_url('cases/hearings/quick_update/' . $hearing['id'])
                ];
            }
            
            // === OVERDUE HEARINGS ===
            $this->db->select('h.*, c.case_title, c.case_number, cl.company as client_name');
            $this->db->from(db_prefix() . 'hearings h');
            $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
            $this->db->where('h.date <', $today);
            $this->db->where('h.status', 'Scheduled');
            $this->db->order_by('h.date', 'DESC');
            $this->db->limit(5);
            
            $overdue_hearings = $this->db->get()->result_array();
            
            foreach ($overdue_hearings as $hearing) {
                $priority_items[] = [
                    'id' => $hearing['id'],
                    'type' => 'overdue_hearing',
                    'title' => $hearing['case_title'] ?: 'Overdue Hearing',
                    'subtitle' => $hearing['case_number'] ?: '',
                    'client' => $hearing['client_name'] ?: 'Unknown Client',
                    'date' => $hearing['date'],
                    'time' => $hearing['time'],
                    'status' => 'overdue',
                    'priority' => 'high',
                    'description' => 'Hearing was scheduled for ' . date('d M Y', strtotime($hearing['date'])),
                    'action_url' => admin_url('cases/hearings/quick_update/' . $hearing['id'])
                ];
            }
            
            // === UNPAID INVOICES (if table exists) ===
            if ($this->db->table_exists(db_prefix() . 'invoices')) {
                $this->db->select('i.*, cl.company as client_name');
                $this->db->from(db_prefix() . 'invoices i');
                $this->db->join(db_prefix() . 'clients cl', 'cl.userid = i.clientid', 'left');
                $this->db->where('i.status', 1); // Unpaid
                $this->db->where('i.duedate <', $today); // Overdue
                $this->db->order_by('i.duedate', 'ASC');
                $this->db->limit(3);
                
                $overdue_invoices = $this->db->get()->result_array();
                
                foreach ($overdue_invoices as $invoice) {
                    $priority_items[] = [
                        'id' => $invoice['id'],
                        'type' => 'overdue_payment',
                        'title' => 'Overdue Invoice Payment',
                        'subtitle' => $this->formatInvoiceNumber($invoice),
                        'client' => $invoice['client_name'] ?: 'Unknown Client',
                        'date' => $invoice['duedate'],
                        'amount' => number_format($invoice['total'], 2),
                        'currency' => $this->getInvoiceCurrency($invoice),
                        'status' => 'overdue',
                        'priority' => 'medium',
                        'description' => 'Due: ' . date('d M Y', strtotime($invoice['duedate'])),
                        'action_url' => admin_url('invoices/list_invoices/' . $invoice['id'])
                    ];
                }
            }
            
            // === UPCOMING DEADLINES ===
            // You can add more priority items here like:
            // - Court filing deadlines
            // - Document submission deadlines
            // - Follow-up consultations
            
            // Sort by priority and date
            usort($priority_items, function($a, $b) {
                $priority_order = ['high' => 3, 'medium' => 2, 'low' => 1];
                $a_priority = $priority_order[$a['priority']] ?? 1;
                $b_priority = $priority_order[$b['priority']] ?? 1;
                
                if ($a_priority === $b_priority) {
                    return strtotime($a['date']) - strtotime($b['date']);
                }
                return $b_priority - $a_priority;
            });
            
            echo json_encode([
                'success' => true,
                'items' => array_slice($priority_items, 0, 10), // Limit to top 10
                'count' => count($priority_items),
                'generated_at' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error in Dashboard::get_priority_items: ' . $e->getMessage());
            $this->outputError($e->getMessage());
        }
        exit;
    }

    /**
     * Get recent activity feed
     */
    public function get_recent_activity()
    {
        try {
            if (!has_permission('cases', '', 'view')) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Access denied'
                ]);
                return;
            }

            $activities = [];
            $limit_per_type = 5;
            
            // === RECENT HEARINGS ===
            $this->db->select('h.*, c.case_title, c.case_number, "hearing" as activity_type');
            $this->db->from(db_prefix() . 'hearings h');
            $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
            $this->db->order_by('h.created_at', 'DESC');
            $this->db->limit($limit_per_type);
            
            $recent_hearings = $this->db->get()->result_array();
            
            foreach ($recent_hearings as $hearing) {
                $activities[] = [
                    'type' => 'hearing',
                    'icon' => 'gavel',
                    'title' => 'Hearing ' . ($hearing['status'] === 'Completed' ? 'completed' : 'scheduled'),
                    'description' => ($hearing['case_title'] ?: 'Case') . ' - ' . ($hearing['case_number'] ?: 'No case number'),
                    'time' => $this->timeAgo($hearing['created_at']),
                    'date' => $hearing['created_at'],
                    'url' => admin_url('cases/hearings/edit/' . $hearing['id'])
                ];
            }
            
            // === RECENT CASES ===
            $this->db->select('c.*, cl.company as client_name, "case" as activity_type');
            $this->db->from(db_prefix() . 'cases c');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
            $this->db->order_by('c.date_created', 'DESC');
            $this->db->limit($limit_per_type);
            
            $recent_cases = $this->db->get()->result_array();
            
            foreach ($recent_cases as $case) {
                $activities[] = [
                    'type' => 'case',
                    'icon' => 'briefcase',
                    'title' => 'New case registered',
                    'description' => ($case['case_title'] ?: 'Case') . ' for ' . ($case['client_name'] ?: 'client'),
                    'time' => $this->timeAgo($case['date_created']),
                    'date' => $case['date_created'],
                    'url' => admin_url('cases/details?id=' . $case['id'])
                ];
            }
            
            // === RECENT CONSULTATIONS ===
            $this->db->select('cc.*, cl.company as client_name, "consultation" as activity_type');
            $this->db->from(db_prefix() . 'case_consultations cc');
            $this->db->join(db_prefix() . 'clients cl', 'cl.userid = cc.client_id', 'left');
            $this->db->order_by('cc.date_added', 'DESC');
            $this->db->limit($limit_per_type);
            
            $recent_consultations = $this->db->get()->result_array();
            
            foreach ($recent_consultations as $consultation) {
                $activities[] = [
                    'type' => 'consultation',
                    'icon' => 'comments',
                    'title' => 'Consultation ' . ($consultation['phase'] === 'litigation' ? 'upgraded' : 'added'),
                    'description' => 'With ' . ($consultation['client_name'] ?: 'client') . ($consultation['tag'] ? ' - ' . $consultation['tag'] : ''),
                    'time' => $this->timeAgo($consultation['date_added']),
                    'date' => $consultation['date_added'],
                    'url' => admin_url('cases?tab=consultations')
                ];
            }
            
            // === RECENT DOCUMENTS (if files table exists) ===
            if ($this->db->table_exists(db_prefix() . 'files')) {
                $this->db->select('f.*, "document" as activity_type');
                $this->db->from(db_prefix() . 'files f');
                $this->db->where('f.rel_type', 'case');
                $this->db->order_by('f.dateadded', 'DESC');
                $this->db->limit($limit_per_type);
                
                $recent_documents = $this->db->get()->result_array();
                
                foreach ($recent_documents as $doc) {
                    $activities[] = [
                        'type' => 'document',
                        'icon' => 'file-upload',
                        'title' => 'Document uploaded',
                        'description' => $doc['file_name'] . ' for case #' . $doc['rel_id'],
                        'time' => $this->timeAgo($doc['dateadded']),
                        'date' => $doc['dateadded'],
                        'url' => admin_url('documents')
                    ];
                }
            }
            
            // Sort by date (most recent first)
            usort($activities, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            echo json_encode([
                'success' => true,
                'activities' => array_slice($activities, 0, 20), // Limit to 20 most recent
                'generated_at' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error in Dashboard::get_recent_activity: ' . $e->getMessage());
            $this->outputError($e->getMessage());
        }
        exit;
    }

    /**
     * Get performance metrics for charts
     */
    public function get_performance_data()
    {
        try {
            if (!has_permission('cases', '', 'view')) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Access denied'
                ]);
                return;
            }

            $data = [];
            
            // Last 7 days activity
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $day = date('D', strtotime("-$i days"));
                
                // Count consultations for this day
                $this->db->where('DATE(date_added)', $date);
                $consultations = $this->db->count_all_results(db_prefix() . 'case_consultations');
                
                // Count hearings for this day
                $this->db->where('DATE(date)', $date);
                $hearings = $this->db->count_all_results(db_prefix() . 'hearings');
                
                $data[] = [
                    'day' => $day,
                    'date' => $date,
                    'consultations' => $consultations,
                    'hearings' => $hearings,
                    'total_activity' => $consultations + $hearings
                ];
            }
            
            echo json_encode([
                'success' => true,
                'performance_data' => $data,
                'generated_at' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error in Dashboard::get_performance_data: ' . $e->getMessage());
            $this->outputError($e->getMessage());
        }
        exit;
    }

    // ===============================
    // PRIVATE HELPER METHODS
    // ===============================

    /**
     * Calculate revenue metrics
     */
    private function calculateRevenue($this_month_start, $last_month_start, $last_month_end)
    {
        $revenue_stats = [
            'revenue_this_month' => 0,
            'revenue_growth' => 0,
            'outstanding_amount' => 0
        ];

        if (!$this->db->table_exists(db_prefix() . 'invoices')) {
            return $revenue_stats;
        }

        try {
            // This month's revenue
            $this->db->select('SUM(total) as revenue');
            $this->db->where('date >=', $this_month_start);
            $this->db->where('status', 2); // Status 2 = Paid in Perfex CRM
            $revenue_query = $this->db->get(db_prefix() . 'invoices');
            
            if ($revenue_query && $revenue_query->num_rows() > 0) {
                $revenue_row = $revenue_query->row();
                $revenue_stats['revenue_this_month'] = $revenue_row->revenue ? (float)$revenue_row->revenue : 0;
            }
            
            // Last month's revenue for comparison
            $this->db->select('SUM(total) as revenue');
            $this->db->where('date >=', $last_month_start);
            $this->db->where('date <=', $last_month_end);
            $this->db->where('status', 2);
            $last_revenue_query = $this->db->get(db_prefix() . 'invoices');
            
            if ($last_revenue_query && $last_revenue_query->num_rows() > 0) {
                $last_revenue_row = $last_revenue_query->row();
                $revenue_last_month = $last_revenue_row->revenue ? (float)$last_revenue_row->revenue : 0;
                
                $revenue_stats['revenue_growth'] = $revenue_last_month > 0 ? 
                    round((($revenue_stats['revenue_this_month'] - $revenue_last_month) / $revenue_last_month) * 100, 1) : 0;
            }
            
            // Outstanding invoices (unpaid)
            $this->db->select('SUM(total) as outstanding');
            $this->db->where('status', 1); // Status 1 = Unpaid
            $outstanding_query = $this->db->get(db_prefix() . 'invoices');
            
            if ($outstanding_query && $outstanding_query->num_rows() > 0) {
                $outstanding_row = $outstanding_query->row();
                $revenue_stats['outstanding_amount'] = $outstanding_row->outstanding ? (float)$outstanding_row->outstanding : 0;
            }

        } catch (Exception $e) {
            log_message('error', 'Error calculating revenue: ' . $e->getMessage());
        }

        return $revenue_stats;
    }

    /**
     * Calculate additional metrics
     */
    private function calculateAdditionalMetrics()
    {
        $metrics = [
            'avg_case_duration_days' => 0,
            'active_clients' => 0
        ];

        try {
            // Average case duration (for cases with hearings)
            $this->db->select('AVG(DATEDIFF(NOW(), date_created)) as avg_duration');
            $duration_query = $this->db->get(db_prefix() . 'cases');
            
            if ($duration_query && $duration_query->num_rows() > 0) {
                $duration_row = $duration_query->row();
                $metrics['avg_case_duration_days'] = $duration_row->avg_duration ? round($duration_row->avg_duration, 0) : 0;
            }
            
            // Total active clients with cases
            $this->db->select('COUNT(DISTINCT client_id) as active_clients');
            $active_clients_query = $this->db->get(db_prefix() . 'cases');
            
            if ($active_clients_query && $active_clients_query->num_rows() > 0) {
                $clients_row = $active_clients_query->row();
                $metrics['active_clients'] = $clients_row->active_clients ? (int)$clients_row->active_clients : 0;
            }

        } catch (Exception $e) {
            log_message('error', 'Error calculating additional metrics: ' . $e->getMessage());
        }

        return $metrics;
    }

    /**
     * Format invoice number for display
     */
    private function formatInvoiceNumber($invoice)
    {
        if (function_exists('format_invoice_number')) {
            return format_invoice_number($invoice['id']);
        }
        
        $prefix = !empty($invoice['prefix']) ? $invoice['prefix'] : 'INV-';
        $number = str_pad($invoice['number'], 6, '0', STR_PAD_LEFT);
        return $prefix . $number;
    }

    /**
     * Get invoice currency symbol
     */
    private function getInvoiceCurrency($invoice)
    {
        // Default currency
        $currency = '₹';
        
        try {
            if (!empty($invoice['currency'])) {
                $this->db->where('id', $invoice['currency']);
                $curr_query = $this->db->get(db_prefix() . 'currencies');
                
                if ($curr_query && $curr_query->num_rows() > 0) {
                    $curr = $curr_query->row();
                    if (!empty($curr->symbol)) {
                        $currency = $curr->symbol;
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting currency: ' . $e->getMessage());
        }
        
        return $currency;
    }

    /**
     * Convert timestamp to human readable "time ago" format
     */
    private function timeAgo($datetime) 
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }

    /**
     * Output error response
     */
    private function outputError($message)
    {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error occurred',
            'error' => ENVIRONMENT === 'development' ? $message : 'Internal server error'
        ]);
    }
}