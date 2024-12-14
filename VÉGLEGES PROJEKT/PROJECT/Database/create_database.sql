-- Create database webshop
CREATE DATABASE IF NOT EXISTS webshop;
USE webshop;

-- Table: cart
CREATE TABLE IF NOT EXISTS cart (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    size VARCHAR(5) NOT NULL,
    order_id INT(11),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
    );

-- Table: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    product_image VARCHAR(255) NOT NULL,
    product_size VARCHAR(10) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
    );

-- Table: orders
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) T NULL,
    customer_phone VARCHAR(15) NOT NULL,
    shipping_address TEXT NOT NULL,
    order_date DATETIME NOT NULL,
    payment_method ENUM('Cash', 'Card', 'PayPal') NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL
    );

-- Table: products
CREATE TABLE IF NOT EXISTS products (
    id INT(11) PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size INT(11) NOT NULL,
    category VARCHAR(50) NOT NULL
    );

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
    );


INSERT INTO products (id, image, name, price, size, category) VALUES
(1, 'https://gfx.r-gol.com/media/res/products/493/180493/465x605/ib0036_1.webp', 'Adidas Real Madrid 23/24 Training Top', 299.99, NULL, 'Clothings'),
(2, 'https://gfx.r-gol.com/media/res/products/606/183606/465x605/fd7117-061_1.webp', 'Nike Liverpool FC 23/24 Strike Track Suit', 549.99, NULL, 'Clothings'),
(3, 'https://gfx.r-gol.com/media/res/products/572/180572/465x605/iq0609_1.webp', 'Adidas FC Bayern 23/24 Training Top', 274.99, NULL, 'Clothings'),
(4, 'https://shop.fradi.hu/api/uploads/0c00/0d00/0c00/0c00/3dc0/6760/7120/7030/6810/6818/6448/6848/7d88/7e08/7880/250x.jpg', 'Ferencváros FTC Nike Men\'s Black Hoodie', 219.15, NULL, 'Clothings'),
(5, 'https://fanshop.sepsiosk.ro/cdn/shop/files/DF108D68-25F3-4BEC-8DB5-3AF8456476E7.jpg?v=1698248329&width=600', 'Sepsi OSK Unisex Zip-up Hoodie', 135.00, NULL, 'Clothings'),
(6, 'https://soldigo.azureedge.net/images/15861/600x600/pcebmpioik.jpg', 'FK Csíkszereda Ultra-Light Premium Cotton Anorak', 93.00, NULL, 'Clothings'),
(7, 'https://gfx.r-gol.com/media/res/products/296/185296/465x605/fn4684-437_2.jpg', 'Nike Tottenham Hotspur 23/24 Strike Hoodie', 328.85, NULL, 'Clothings'),
(8, 'https://gfx.r-gol.com/media/res/products/605/183605/465x605/fj5427-620_1.webp', 'Nike FC Barcelona 23/24 Strike Hoodie', 612.50, NULL, 'Clothings'),
(9, 'https://gfx.r-gol.com/media/res/products/322/159322/465x605/hr3796_1.jpg', 'Adidas Real Madrid 23/24 Home Replica Jersey', 462.67, NULL, 'Jerseys'),
(10, 'https://gfx.r-gol.com/media/res/products/340/162340/465x605/dx2618-688_1.jpg', 'Nike Liverpool FC 23/24 Home Vapor Match Jersey', 694.08, NULL, 'Jerseys'),
(11, 'https://gfx.r-gol.com/media/res/products/321/159321/465x605/hr3729_1.jpg', 'Adidas FC Bayern 23/24 Home Authentic Jersey', 552.66, NULL, 'Jerseys'),
(12, 'https://shop.fradi.hu/api/uploads/0300/0280/0280/0380/0380/0648/c66c/c666/8fb6/cec6/cec4/3ec0/1ee0/1e60/1f30/1e30/250x.jpg', 'Ferencváros Nike Men\'s Home Jersey', 321.26, NULL, 'Jerseys'),
(13, 'https://fanshop.sepsiosk.ro/cdn/shop/files/IMG-5727.jpg?v=1711289608&width=493', 'Sepsi OSK Unisex Game Jersey', 195.00, NULL, 'Jerseys'),
(14, 'https://soldigo.azureedge.net/images/15861/zaxa2brkvx.png', 'FK Csíkszereda Home Jersey', 93.00, NULL, 'Jerseys'),
(15, 'https://gfx.r-gol.com/media/res/products/680/162680/465x605/dx2625-101_1.jpg', 'Nike Tottenham Hotspur 23/24 Home Vapor Match Jersey', 694.08, NULL, 'Jerseys'),
(16, 'https://gfx.r-gol.com/media/res/products/811/179811/465x605/dx2687-456_6.jpg', 'Nike FC Barcelona 23/24 Home Stadium Jersey', 597.66, NULL, 'Jerseys'),
(17, 'https://gfx.r-gol.com/media/res/products/563/184563/465x605/fj2579-001_6.webp', 'Nike Phantom Luna II Elite AG-PRO', 1554.79, NULL, 'Shoes'),
(18, 'https://gfx.r-gol.com/media/res/products/759/185759/465x605/ie2416_1.webp', 'Adidas X Crazyfast+ FG', 1349.08, NULL, 'Shoes'),
(19, 'https://gfx.r-gol.com/media/res/products/979/187979/465x605/107828-01_1.webp', 'Puma Future 7 Ultimate Rush FG/AG', 1086.95, NULL, 'Shoes'),
(20, 'https://gfx.r-gol.com/media/res/products/513/187513/465x605/fd0250-700_1.webp', 'Nike Zoom Mercurial Superfly 9 Elite SG-PRO Player Edition', 1413.33, NULL, 'Shoes'),
(21, 'https://gfx.r-gol.com/media/res/products/924/182924/465x605/iq3682-5_1.webp', 'Adidas Fussballliebe EURO 2024 Pro Ball (Size 5)', 501.01, 5, 'Balls'),
(22, 'https://gfx.r-gol.com/media/res/products/969/186969/465x605/in9340-5_1.webp', 'Adidas UCL Pro 23/24 Ball (Size 5)', 558.83, 5, 'Balls'),
(23, 'https://gfx.r-gol.com/media/res/products/347/184347/465x605/fb2979-101-5_1.webp', 'Nike Premier League Flight Ball (Size 5)', 610.23, 5, 'Balls'),
(24, 'https://gfx.r-gol.com/media/res/products/901/181901/465x605/084106-01-5_1.webp', 'Puma Orbita 1 La Liga Ball (Size 5)', 411.06, 5, 'Balls');
