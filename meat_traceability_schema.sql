-- Meat Traceability System Database Schema
-- Compatible with MySQL / MariaDB / phpMyAdmin

CREATE DATABASE IF NOT EXISTS meat_traceability_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE meat_traceability_system;

-- Drop tables first to avoid foreign key conflicts during re-import
DROP TABLE IF EXISTS qr_scan_logs;
DROP TABLE IF EXISTS meat_inspection_certificates;
DROP TABLE IF EXISTS meat_products;
DROP TABLE IF EXISTS users;

-- USERS table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Inspector', 'Manager') NOT NULL DEFAULT 'Inspector',
    branch_or_slaughterhouse VARCHAR(100) NOT NULL,
    account_status ENUM('Active', 'Pending', 'Inactive') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- MEAT_PRODUCTS table
CREATE TABLE meat_products (
    meat_product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    point_of_origin VARCHAR(150) NOT NULL,
    farm_or_supplier VARCHAR(150) NOT NULL,
    date_issued DATE NOT NULL,
    time_issued TIME NOT NULL,
    encoder_user_id INT NOT NULL,
    qr_code_string VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_meat_products_encoder
        FOREIGN KEY (encoder_user_id)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- MEAT_INSPECTION_CERTIFICATES table
CREATE TABLE meat_inspection_certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    meat_product_id INT NOT NULL,
    certificate_number VARCHAR(100) NOT NULL UNIQUE,
    inspection_date DATE NOT NULL,
    inspector_name VARCHAR(100) NOT NULL,
    verification_remarks VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_certificates_meat_product
        FOREIGN KEY (meat_product_id)
        REFERENCES meat_products(meat_product_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- QR_SCAN_LOGS table
CREATE TABLE qr_scan_logs (
    scan_log_id INT AUTO_INCREMENT PRIMARY KEY,
    meat_product_id INT NOT NULL,
    scanned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    device_info VARCHAR(150) NOT NULL,
    scan_result VARCHAR(50) NOT NULL,

    CONSTRAINT fk_qr_scan_logs_meat_product
        FOREIGN KEY (meat_product_id)
        REFERENCES meat_products(meat_product_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Helpful indexes for faster searching/filtering
CREATE INDEX idx_meat_products_encoder_user_id ON meat_products(encoder_user_id);
CREATE INDEX idx_meat_products_qr_code_string ON meat_products(qr_code_string);
CREATE INDEX idx_certificates_meat_product_id ON meat_inspection_certificates(meat_product_id);
CREATE INDEX idx_qr_scan_logs_meat_product_id ON qr_scan_logs(meat_product_id);
CREATE INDEX idx_qr_scan_logs_scanned_at ON qr_scan_logs(scanned_at);
