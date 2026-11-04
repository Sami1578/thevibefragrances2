THE VIBE FRAGRANCES - PHP + MySQL store

SETUP (XAMPP / any PHP 7.4+ hosting with MySQL)
1. Copy the "thevibe" folder into htdocs (or your hosting public_html).
2. Edit config.php if your MySQL user/password differ (XAMPP default: root / empty).
3. Open  http://localhost/thevibe/install.php  -> choose admin username/password -> Install.
   (This creates the database "thevibe_fragrances", tables and 4 sample perfumes.)
4. DELETE install.php afterwards.
5. Admin panel: http://localhost/thevibe/admin/   (default login if you skip a password: admin / admin123)
6. In Admin > Settings set your real WhatsApp number (e.g. 923001234567), delivery fee, social links.

ADMIN CAN MANAGE
- Fragrances: name, price, sale price, size, notes, description, main image + gallery, featured/visible
- Banners (home slider), Categories, Orders (status + WhatsApp the customer), Settings, password

CUSTOMER SIDE
- Banner slider, categories, featured, search/filter/sort, product page, AJAX cart,
  checkout (name, phone, address only - cash on delivery), floating WhatsApp button.

Make sure the uploads/ folder is writable by PHP.
