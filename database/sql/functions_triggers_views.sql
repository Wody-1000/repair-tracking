-- Run by migration 2026_10_18_000010_create_db_objects.php (kept here so it can be read and defended)

-- Partial index: only parts that are at or below their reorder level
CREATE INDEX idx_parts_low_stock ON parts (quantity) WHERE quantity <= reorder_level;

-- Foreign key / filter indexes used by the app's most common queries
CREATE INDEX idx_tickets_status_created ON tickets (status_id, created_at DESC);
CREATE INDEX idx_tickets_customer ON tickets (customer_id);
CREATE INDEX idx_ticket_parts_ticket ON ticket_parts (ticket_id);
CREATE INDEX idx_ticket_parts_part ON ticket_parts (part_id);

-- Function: deduct stock when a part is used on a ticket, raise a low-stock alert.
-- SECURITY DEFINER: it runs with the owner's rights, so stock can only change through this
-- trigger (row-level security on `parts` lets only admins edit stock by hand).
CREATE OR REPLACE FUNCTION fn_deduct_part_stock() RETURNS TRIGGER
LANGUAGE plpgsql SECURITY DEFINER SET search_path = public, pg_temp AS $$
DECLARE
    v_qty INTEGER;
    v_reorder INTEGER;
BEGIN
    SELECT quantity, reorder_level INTO v_qty, v_reorder
    FROM parts WHERE id = NEW.part_id FOR UPDATE;

    IF v_qty IS NULL THEN
        RAISE EXCEPTION 'Part % does not exist', NEW.part_id;
    END IF;

    IF v_qty < NEW.quantity THEN
        RAISE EXCEPTION 'Insufficient stock for part %: requested %, available %', NEW.part_id, NEW.quantity, v_qty;
    END IF;

    UPDATE parts SET quantity = quantity - NEW.quantity, updated_at = NOW() WHERE id = NEW.part_id;

    -- One open alert per part (no duplicates while the part stays low)
    IF (v_qty - NEW.quantity) <= v_reorder
       AND NOT EXISTS (SELECT 1 FROM low_stock_alerts WHERE part_id = NEW.part_id AND resolved = FALSE) THEN
        INSERT INTO low_stock_alerts (part_id, quantity_at_alert, created_at, updated_at)
        VALUES (NEW.part_id, v_qty - NEW.quantity, NOW(), NOW());
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_deduct_part_stock
BEFORE INSERT ON ticket_parts
FOR EACH ROW EXECUTE FUNCTION fn_deduct_part_stock();

-- Function: give stock back when a used part is removed from a ticket, and close the alert if stock is healthy again
CREATE OR REPLACE FUNCTION fn_restore_part_stock() RETURNS TRIGGER
LANGUAGE plpgsql SECURITY DEFINER SET search_path = public, pg_temp AS $$
BEGIN
    UPDATE parts SET quantity = quantity + OLD.quantity, updated_at = NOW() WHERE id = OLD.part_id;

    UPDATE low_stock_alerts SET resolved = TRUE, updated_at = NOW()
    WHERE part_id = OLD.part_id AND resolved = FALSE
      AND (SELECT quantity > reorder_level FROM parts WHERE id = OLD.part_id);

    RETURN OLD;
END;
$$;

CREATE TRIGGER trg_restore_part_stock
AFTER DELETE ON ticket_parts
FOR EACH ROW EXECUTE FUNCTION fn_restore_part_stock();

-- View: parts at or below reorder level (used by the dashboard warning and reports)
CREATE OR REPLACE VIEW v_low_stock_parts AS
SELECT p.id, p.sku, p.name, c.name AS category, p.quantity, p.reorder_level
FROM parts p
JOIN part_categories c ON c.id = p.category_id
WHERE p.quantity <= p.reorder_level;

-- View: ticket totals (parts + labor), used by reports
CREATE OR REPLACE VIEW v_ticket_totals AS
SELECT t.id AS ticket_id, t.ticket_number, t.labor_cost,
       COALESCE(SUM(tp.quantity * tp.unit_price), 0) AS parts_cost,
       t.labor_cost + COALESCE(SUM(tp.quantity * tp.unit_price), 0) AS total_cost
FROM tickets t
LEFT JOIN ticket_parts tp ON tp.ticket_id = t.id
GROUP BY t.id, t.ticket_number, t.labor_cost;
