-- ================================================
-- LOCAL DEVELOPMENT DATABASE SETUP
-- ================================================
-- Run this in your LOCAL MySQL (XAMPP phpMyAdmin)
-- NOT on Hostinger!

-- Create local development database
CREATE DATABASE IF NOT EXISTS rsd_local CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Create local dev user (optional, you can use root)
-- GRANT ALL PRIVILEGES ON rsd_local.* TO 'rsd_dev'@'localhost' IDENTIFIED BY 'local_dev_password';
-- FLUSH PRIVILEGES;

-- Instructions:
-- 1. Open http://localhost/phpmyadmin/
-- 2. Click "Import" tab
-- 3. Upload this file OR copy/paste the CREATE DATABASE line
-- 4. Then import a database dump from production
--    (Export from Hostinger → Import to local rsd_local)
