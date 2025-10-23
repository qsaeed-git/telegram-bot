<?php
// database.php

require_once 'config.php';

function createDatabase() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        
        if ($conn->connect_error) {
            die("خطای اتصال: " . $conn->connect_error);
        }
        
        // ایجاد دیتابیس
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $conn->query($sql);
        
        $conn->select_db(DB_NAME);
        
        // جدول فایل‌ها
        $sql = "CREATE TABLE IF NOT EXISTS files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id VARCHAR(255) NOT NULL,
            file_name VARCHAR(500),
            file_size BIGINT,
            user_id BIGINT,
            storage_message_id BIGINT,
            download_count INT DEFAULT 0,
            created_at DATETIME,
            INDEX idx_file_id (file_id),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->query($sql);
        
        // جدول کاربران
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNIQUE,
            username VARCHAR(255),
            first_name VARCHAR(255),
            last_name VARCHAR(255),
            first_interaction DATETIME,
            last_interaction DATETIME,
            total_files INT DEFAULT 0,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->query($sql);
        
        echo "✅ دیتابیس با موفقیت ایجاد شد!";
        
        $conn->close();
        
    } catch (Exception $e) {
        die("❌ خطا: " . $e->getMessage());
    }
}

// اجرا فقط زمانی که مستقیماً فراخوانی شود
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    createDatabase();
}
?>
