-- Panchakanya, NS, Rijalco and Shine plumbing catalog images.
-- Safe to run more than once: it only creates missing records.

INSERT INTO brands (name, status)
SELECT 'Panchakanya', 1 WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Panchakanya');
INSERT INTO brands (name, status)
SELECT 'NS', 1 WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'NS');
INSERT INTO brands (name, status)
SELECT 'Rijalco', 1 WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Rijalco');
INSERT INTO brands (name, status)
SELECT 'Shine', 1 WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Shine');

INSERT INTO categories (name, slug, status)
SELECT 'Water Storage', 'water-storage', 1
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'water-storage');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'Panchakanya PVC Pipe & Fittings', 'panchakanya-pvc-pipe-fittings',
       'Durable PVC pipes and fittings for dependable water supply and drainage installations.',
       'assets/images/catalog/panchakanya-pvc-01.jpg', 1850, 50, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'plumbing' AND b.name = 'Panchakanya'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'panchakanya-pvc-pipe-fittings');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'Panchakanya CPVC Pipe & Fittings', 'panchakanya-cpvc-pipe-fittings',
       'CPVC plumbing pipes and fittings designed for hot- and cold-water applications.',
       'assets/images/catalog/panchakanya-cpvc-01.jpg', 2650, 40, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'plumbing' AND b.name = 'Panchakanya'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'panchakanya-cpvc-pipe-fittings');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'Panchakanya PPR Pipe & Fittings', 'panchakanya-ppr-pipe-fittings',
       'PP-R pipes and fittings for efficient hot- and cold-water plumbing systems.',
       'assets/images/catalog/panchakanya-ppr-01.jpg', 3200, 40, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'plumbing' AND b.name = 'Panchakanya'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'panchakanya-ppr-pipe-fittings');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'NS HDPE Pipe', 'ns-hdpe-pipe',
       'High-density polyethylene pipe for durable water distribution and outdoor installations.',
       'assets/images/catalog/hdpe-pipe-01.jpg', 4800, 30, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'plumbing' AND b.name = 'NS'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'ns-hdpe-pipe');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'Stainless Steel Water Tank', 'stainless-steel-water-tank',
       'Stainless steel water storage tank with a hygienic and long-lasting finish.',
       'assets/images/catalog/stainless-steel-tank-01.jpg', 28000, 12, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'water-storage' AND b.name = 'Panchakanya'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'stainless-steel-water-tank');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'Rijalco Plastic Water Tank', 'rijalco-plastic-water-tank',
       'Strong plastic water tank for reliable household water storage.',
       'assets/images/catalog/rijalco-water-tank-01.jpg', 13500, 20, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'water-storage' AND b.name = 'Rijalco'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'rijalco-plastic-water-tank');

INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status)
SELECT c.id, b.id, 'Shine Plastic Dhara / Taps', 'shine-plastic-dhara-taps',
       'Practical plastic taps and dhara fittings for everyday water connections.',
       'assets/images/catalog/shine-tap-01.jpg', 250, 100, 1, 1
FROM categories c CROSS JOIN brands b
WHERE c.slug = 'plumbing' AND b.name = 'Shine'
  AND NOT EXISTS (SELECT 1 FROM products WHERE slug = 'shine-plastic-dhara-taps');

-- Populate price and stock for catalogs installed before these values were added.
UPDATE products
SET
    price = CASE WHEN price = 0 THEN CASE slug
        WHEN 'panchakanya-pvc-pipe-fittings' THEN 1850
        WHEN 'panchakanya-cpvc-pipe-fittings' THEN 2650
        WHEN 'panchakanya-ppr-pipe-fittings' THEN 3200
        WHEN 'ns-hdpe-pipe' THEN 4800
        WHEN 'stainless-steel-water-tank' THEN 28000
        WHEN 'rijalco-plastic-water-tank' THEN 13500
        WHEN 'shine-plastic-dhara-taps' THEN 250 ELSE price END ELSE price END,
    stock = CASE WHEN stock = 0 THEN CASE slug
        WHEN 'panchakanya-pvc-pipe-fittings' THEN 50
        WHEN 'panchakanya-cpvc-pipe-fittings' THEN 40
        WHEN 'panchakanya-ppr-pipe-fittings' THEN 40
        WHEN 'ns-hdpe-pipe' THEN 30
        WHEN 'stainless-steel-water-tank' THEN 12
        WHEN 'rijalco-plastic-water-tank' THEN 20
        WHEN 'shine-plastic-dhara-taps' THEN 100 ELSE stock END ELSE stock END
WHERE slug IN ('panchakanya-pvc-pipe-fittings', 'panchakanya-cpvc-pipe-fittings',
    'panchakanya-ppr-pipe-fittings', 'ns-hdpe-pipe', 'stainless-steel-water-tank',
    'rijalco-plastic-water-tank', 'shine-plastic-dhara-taps');

-- Additional gallery views for every catalog product.
INSERT INTO product_images (product_id, image)
SELECT p.id, i.image FROM products p CROSS JOIN (
    SELECT 'panchakanya-pvc-pipe-fittings' slug, 'assets/images/catalog/panchakanya-pvc-02.jpg' image UNION ALL
    SELECT 'panchakanya-pvc-pipe-fittings', 'assets/images/catalog/panchakanya-pvc-03.jpg' UNION ALL
    SELECT 'panchakanya-pvc-pipe-fittings', 'assets/images/catalog/panchakanya-pvc-04.jpg' UNION ALL
    SELECT 'panchakanya-pvc-pipe-fittings', 'assets/images/catalog/panchakanya-pvc-05.jpg' UNION ALL
    SELECT 'panchakanya-cpvc-pipe-fittings', 'assets/images/catalog/panchakanya-cpvc-02.jpg' UNION ALL
    SELECT 'panchakanya-cpvc-pipe-fittings', 'assets/images/catalog/panchakanya-cpvc-03.jpg' UNION ALL
    SELECT 'panchakanya-cpvc-pipe-fittings', 'assets/images/catalog/panchakanya-cpvc-04.jpg' UNION ALL
    SELECT 'panchakanya-ppr-pipe-fittings', 'assets/images/catalog/panchakanya-ppr-02.jpg' UNION ALL
    SELECT 'panchakanya-ppr-pipe-fittings', 'assets/images/catalog/panchakanya-ppr-03.jpg' UNION ALL
    SELECT 'ns-hdpe-pipe', 'assets/images/catalog/hdpe-pipe-02.jpg' UNION ALL
    SELECT 'ns-hdpe-pipe', 'assets/images/catalog/hdpe-pipe-03.jpg' UNION ALL
    SELECT 'stainless-steel-water-tank', 'assets/images/catalog/stainless-steel-tank-02.jpg' UNION ALL
    SELECT 'stainless-steel-water-tank', 'assets/images/catalog/stainless-steel-tank-03.jpg' UNION ALL
    SELECT 'stainless-steel-water-tank', 'assets/images/catalog/stainless-steel-tank-04.jpg' UNION ALL
    SELECT 'rijalco-plastic-water-tank', 'assets/images/catalog/rijalco-water-tank-02.jpg' UNION ALL
    SELECT 'shine-plastic-dhara-taps', 'assets/images/catalog/shine-tap-02.jpg' UNION ALL
    SELECT 'shine-plastic-dhara-taps', 'assets/images/catalog/shine-tap-03.jpg' UNION ALL
    SELECT 'shine-plastic-dhara-taps', 'assets/images/catalog/shine-tap-04.jpg' UNION ALL
    SELECT 'shine-plastic-dhara-taps', 'assets/images/catalog/shine-tap-05.jpg'
) i ON p.slug = i.slug
WHERE NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.product_id = p.id AND pi.image = i.image);
