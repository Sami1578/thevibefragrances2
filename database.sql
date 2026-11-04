-- TheVibe Fragrances database schema + starter data
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  k VARCHAR(60) PRIMARY KEY,
  v TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  notes VARCHAR(255) DEFAULT NULL,
  size VARCHAR(50) DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  sale_price DECIMAL(10,2) NULL,
  image VARCHAR(255) DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (category_id),
  INDEX (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image VARCHAR(255) NOT NULL,
  INDEX (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS banners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  subtitle VARCHAR(300) DEFAULT NULL,
  button_text VARCHAR(60) DEFAULT NULL,
  button_link VARCHAR(255) DEFAULT NULL,
  image VARCHAR(255) DEFAULT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_ref VARCHAR(20) DEFAULT NULL,
  token VARCHAR(40) NOT NULL,
  customer_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address TEXT NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
  delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(200) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  qty INT NOT NULL,
  INDEX (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO settings (k, v) VALUES
('site_name', 'The Vibe Fragrances'),
('tagline', 'Wear your signature. Leave a memory.'),
('whatsapp', '923001234567'),
('currency', 'Rs.'),
('phone', '+92 300 1234567'),
('email', 'hello@thevibefragrances.com'),
('address', 'Abbottabad, Pakistan'),
('instagram', ''),
('facebook', ''),
('delivery_fee', '250'),
('free_delivery_over', '5000'),
('announcement', 'Free delivery on orders over Rs. 5,000  •  Cash on delivery available nationwide');

INSERT INTO categories (name, sort_order) SELECT * FROM (SELECT 'For Him' AS n, 1 AS s) t WHERE NOT EXISTS (SELECT 1 FROM categories);
INSERT INTO categories (name, sort_order) SELECT * FROM (SELECT 'For Her' AS n, 2 AS s) t WHERE (SELECT COUNT(*) FROM categories) = 1;
INSERT INTO categories (name, sort_order) SELECT * FROM (SELECT 'Unisex' AS n, 3 AS s) t WHERE (SELECT COUNT(*) FROM categories) = 2;

INSERT INTO banners (title, subtitle, button_text, button_link, sort_order) SELECT * FROM (
  SELECT 'Discover Your Signature Scent' AS a, 'Luxury fragrances crafted to make every moment unforgettable.' AS b, 'Shop Collection' AS c, 'shop.php' AS d, 1 AS e
) t WHERE NOT EXISTS (SELECT 1 FROM banners);
INSERT INTO banners (title, subtitle, button_text, button_link, sort_order) SELECT * FROM (
  SELECT 'New Arrivals Are Here' AS a, 'Fresh, bold and long-lasting. Meet this season\'s finest.' AS b, 'Explore Now' AS c, 'shop.php?sort=new' AS d, 2 AS e
) t WHERE (SELECT COUNT(*) FROM banners) = 1;
INSERT INTO banners (title, subtitle, button_text, button_link, sort_order) SELECT * FROM (
  SELECT 'Gift the Feeling of Luxury' AS a, 'Perfect presents, delivered to their doorstep.' AS b, 'Order on WhatsApp' AS c, 'shop.php' AS d, 3 AS e
) t WHERE (SELECT COUNT(*) FROM banners) = 2;

INSERT INTO products (category_id, name, description, notes, size, price, sale_price, is_featured)
SELECT * FROM (SELECT 1 AS c, 'Noir Oud' AS n, 'A deep, smoky oud blended with warm amber and a touch of leather. Bold, magnetic and made for evenings that matter. Long-lasting performance that follows you through the night.' AS d, 'Oud • Amber • Leather' AS no, '100ml' AS s, 6500 AS p, 5500 AS sp, 1 AS f) t
WHERE NOT EXISTS (SELECT 1 FROM products);
INSERT INTO products (category_id, name, description, notes, size, price, sale_price, is_featured)
SELECT * FROM (SELECT 2 AS c, 'Rose Éclat' AS n, 'Velvety Damascus rose layered over soft musk and vanilla. Romantic, graceful and effortlessly feminine.' AS d, 'Rose • Musk • Vanilla' AS no, '100ml' AS s, 5800 AS p, NULL AS sp, 1 AS f) t
WHERE (SELECT COUNT(*) FROM products) = 1;
INSERT INTO products (category_id, name, description, notes, size, price, sale_price, is_featured)
SELECT * FROM (SELECT 3 AS c, 'Citrus Mirage' AS n, 'Sparkling bergamot and grapefruit over fresh cedar. A clean, energetic scent for every day.' AS d, 'Bergamot • Grapefruit • Cedar' AS no, '50ml' AS s, 4200 AS p, 3800 AS sp, 1 AS f) t
WHERE (SELECT COUNT(*) FROM products) = 2;
INSERT INTO products (category_id, name, description, notes, size, price, sale_price, is_featured)
SELECT * FROM (SELECT 1 AS c, 'Midnight Saffron' AS n, 'Spicy saffron and cardamom resting on creamy sandalwood. Mysterious, rich and unforgettable.' AS d, 'Saffron • Sandalwood • Spice' AS no, '100ml' AS s, 7200 AS p, NULL AS sp, 1 AS f) t
WHERE (SELECT COUNT(*) FROM products) = 3;
