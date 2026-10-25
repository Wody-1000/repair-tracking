-- Public parts list for customers (read-only browsing).
-- Run by migration 2026_10_25_000012_create_public_parts_view.php. Safe to run more than once.
--
-- Why a view and not a policy on `parts`:
--   Customers must never see exact stock counts, SKUs or reorder levels. The view exposes only
--   name, category, price and a simple availability label, and only for parts that are in stock.
--   A view runs with its owner's rights, so the `parts` table itself stays closed to guests (RLS).
--   The view still answers only when the app has said who is calling (app_role() is set),
--   so a plain connection with no context sees nothing.
CREATE OR REPLACE VIEW v_public_parts AS
SELECT p.id,
       p.name,
       c.name AS category,
       p.unit_price AS price,
       CASE WHEN p.quantity <= p.reorder_level THEN 'Limited stock' ELSE 'In stock' END AS availability
FROM parts p
JOIN part_categories c ON c.id = p.category_id
WHERE p.quantity > 0
  AND app_role() IS NOT NULL;

-- If the limited roles already exist, let them read the view. (If they do not exist yet, roles.sql grants it later.)
DO $$
DECLARE r text;
BEGIN
    FOREACH r IN ARRAY ARRAY['repair_app', 'repair_readonly'] LOOP
        IF EXISTS (SELECT FROM pg_roles WHERE rolname = r) THEN
            EXECUTE format('GRANT SELECT ON v_public_parts TO %I', r);
        END IF;
    END LOOP;
END
$$;
