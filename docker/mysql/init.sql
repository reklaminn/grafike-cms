-- IRASPA CMS - MySQL Initialization
-- This runs when the MySQL container is first created

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Ensure database uses correct charset
ALTER DATABASE IF EXISTS iraspa_cms
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
