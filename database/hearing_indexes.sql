-- =============================================================================
-- HEARING SYSTEM DATABASE OPTIMIZATIONS
-- Indexes and performance improvements for the hearing system
-- =============================================================================

-- Index for temporal queries (date-based filtering)
-- Optimizes queries like: WHERE DATE(date) >= '2024-01-01'
CREATE INDEX IF NOT EXISTS idx_hearings_date_status ON tblhearings(date, status);

-- Index for case-based queries
-- Optimizes queries like: WHERE case_id = 123 ORDER BY date DESC
CREATE INDEX IF NOT EXISTS idx_hearings_case_date ON tblhearings(case_id, date);

-- Index for parent-child relationships
-- Optimizes queries like: WHERE parent_hearing_id = 123
CREATE INDEX IF NOT EXISTS idx_hearings_parent ON tblhearings(parent_hearing_id);

-- Index for status-based filtering
-- Optimizes queries like: WHERE status != 'Cancelled'
CREATE INDEX IF NOT EXISTS idx_hearings_status ON tblhearings(status);

-- Index for time-based ordering within dates
-- Optimizes queries like: ORDER BY date, time
CREATE INDEX IF NOT EXISTS idx_hearings_datetime ON tblhearings(date, time);

-- Index for next_date queries (if used)
-- Optimizes queries like: WHERE DATE(next_date) = '2024-01-01'
CREATE INDEX IF NOT EXISTS idx_hearings_next_date ON tblhearings(next_date);

-- Index for court room conflicts
-- Optimizes queries joining with cases for court room availability
CREATE INDEX IF NOT EXISTS idx_cases_court_room ON tblcases(court_room_id);

-- Index for client-based queries
-- Optimizes queries joining with clients
CREATE INDEX IF NOT EXISTS idx_cases_client ON tblcases(client_id);

-- Index for consultation-based queries
-- Optimizes queries joining with consultations
CREATE INDEX IF NOT EXISTS idx_cases_consultation ON tblcases(consultation_id);

-- Composite index for hearing conflicts
-- Optimizes complex queries checking for scheduling conflicts
CREATE INDEX IF NOT EXISTS idx_hearings_conflict_check ON tblhearings(date, time, status, case_id);

-- =============================================================================
-- PERFORMANCE ANALYSIS QUERIES
-- Use these queries to analyze performance after adding indexes
-- =============================================================================

-- Query to check index usage
-- EXPLAIN SELECT * FROM tblhearings WHERE DATE(date) >= CURDATE() AND status != 'Cancelled' ORDER BY date, time;

-- Query to check table statistics
-- SHOW TABLE STATUS LIKE 'tblhearings';

-- Query to check index cardinality
-- SHOW INDEX FROM tblhearings;

-- =============================================================================
-- CLEANUP OLD INDEXES (if needed)
-- Remove these comments to drop old or redundant indexes
-- =============================================================================

-- DROP INDEX IF EXISTS old_index_name ON tblhearings;

-- =============================================================================
-- MAINTENANCE RECOMMENDATIONS
-- =============================================================================

-- 1. Run ANALYZE TABLE tblhearings; periodically to update statistics
-- 2. Consider partitioning by date for very large datasets
-- 3. Monitor query performance with EXPLAIN plans
-- 4. Archive old hearings to separate table if needed
-- 5. Consider adding full-text search index for description/purpose fields:
--    CREATE FULLTEXT INDEX idx_hearings_fulltext ON tblhearings(description, hearing_purpose);

-- =============================================================================
-- BACKUP RECOMMENDATIONS
-- =============================================================================

-- Before running these index additions:
-- 1. Create a backup of the database
-- 2. Run during maintenance window
-- 3. Test on staging environment first
-- 4. Monitor database performance after implementation