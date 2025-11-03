-- Backfill existing applications with NULL/blank status to 'pending'
UPDATE applications SET status='pending' WHERE status IS NULL OR TRIM(status)='';

-- Optionally enforce a default (may vary per DB engine; adjust as needed)
-- MySQL example:
ALTER TABLE applications MODIFY COLUMN status VARCHAR(64) NOT NULL DEFAULT 'pending';
