CREATE DATABASE IF NOT EXISTS qlsv_db;
USE qlsv_db;

CREATE TABLE IF NOT EXISTS sinhvien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    masv VARCHAR(20) NOT NULL,
    hoten VARCHAR(100) NOT NULL,
    lop VARCHAR(50) NOT NULL,
    diem FLOAT DEFAULT 0
);

INSERT INTO sinhvien (masv, hoten, lop, diem) VALUES
('DTC241200007', 'Vàng Thị Sinh', 'CNTT-K23E', 9.5),
('DTC241200008', 'Nguyễn Văn An', 'CNTT-K23E', 8.0),
('DTC241200009', 'Lý Thị Hà', 'CNTT-K23E', 8.5);
