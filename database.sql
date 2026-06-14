CREATE DATABASE IF NOT EXISTS timmo_db;
USE timmo_db;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL,
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(50),
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    staff_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status VARCHAR(30) DEFAULT 'new',
    payment_status VARCHAR(20) DEFAULT 'unpaid',
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (staff_id) REFERENCES staff(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

INSERT INTO services (name, price, duration) VALUES
('Sơn gel', 150000, 45),
('Đắp bột', 300000, 90),
('Nối móng', 250000, 75),
('Vẽ móng nghệ thuật', 200000, 60),
('Chăm sóc móng tay', 120000, 40),
('Chăm sóc móng chân', 180000, 50);

INSERT INTO staff (name, phone, role) VALUES
('Linh', '0900000001', 'Nail Artist'),
('Trang', '0900000002', 'Nail Artist'),
('My', '0900000003', 'Senior Staff'),
('Hân', '0900000004', 'Nail Artist');