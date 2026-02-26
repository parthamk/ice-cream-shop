CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_amount DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) NOT NULL,
    platform_fee DECIMAL(10, 2) NOT NULL DEFAULT 1.50,
    payment_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES ('Ice Cream'), ('Cakes'), ('Pastries'), ('Waffles');

INSERT INTO products (category_id, name, description, price, image_url) VALUES 
(1, 'Classic Vanilla', 'Rich and creamy.', 3.99, 'https://images.unsplash.com/photo-1688841914419-482222767a22?w=500&q=80'),
(1, 'Strawberry Bliss', 'Made with fresh strawberries.', 4.50, 'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?w=500&q=80'),
(2, 'Black Forest Cake', 'Chocolate sponge with cherry filling.', 25.00, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=500&q=80'),
(2, 'Red Velvet Slice', 'Smooth cream cheese frosting.', 5.50, 'https://images.unsplash.com/photo-1616541823729-00fe0aacd32c?w=500&q=80'),
(3, 'Butter Croissant', 'Flaky, buttery, and baked fresh.', 3.00, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&q=80'),
(3, 'Chocolate Éclair', 'Filled with vanilla custard.', 4.25, 'https://images.unsplash.com/photo-1612201142855-7873bc1661b4?w=500&q=80'),
(4, 'Belgian Waffle', 'Topped with maple syrup and butter.', 6.50, 'https://images.unsplash.com/photo-1616709555072-418df2a5b062?w=500&q=80'),
(4, 'Nutella Waffle', 'Smothered in rich hazelnut spread.', 7.50, 'https://images.unsplash.com/photo-1504113888839-1c8eb50233d3?w=500&q=80');