-- EXPLAIN demo for defense. Run: psql -U <user> -d repair_tracking -f database/sql/explain_demo.sql
-- With few rows PostgreSQL may prefer a seq scan; the next line forces the planner to show index use.
SET enable_seqscan = off;

-- 1) Customer status page lookup by ticket number -> uses the UNIQUE index on tickets.ticket_number
EXPLAIN ANALYZE SELECT id, status_id FROM tickets WHERE ticket_number = 'RT-2026-0001';

-- 2) Ticket list filtered by status, newest first -> uses idx_tickets_status_created
EXPLAIN ANALYZE SELECT id, ticket_number FROM tickets WHERE status_id = 1 ORDER BY created_at DESC LIMIT 20;

-- 3) Low-stock check -> uses the partial index idx_parts_low_stock
EXPLAIN ANALYZE SELECT id, sku, quantity FROM parts WHERE quantity <= reorder_level;
