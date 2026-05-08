-- Referral System Database Schema
-- Execute this SQL directly in your MySQL database

USE cafe_berco;

-- Create referrals table
CREATE TABLE IF NOT EXISTS referrals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_id BIGINT UNSIGNED NOT NULL,
    referee_id BIGINT UNSIGNED NULL,
    referral_code VARCHAR(255) UNIQUE NOT NULL,
    reward_amount DECIMAL(10, 2) DEFAULT 0,
    status ENUM('pending', 'completed', 'expired') DEFAULT 'pending',
    first_order_id BIGINT UNSIGNED NULL,
    completed_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (first_order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_referrer_id (referrer_id),
    INDEX idx_referee_id (referee_id),
    INDEX idx_referral_code (referral_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create achievements table
CREATE TABLE IF NOT EXISTS achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description LONGTEXT,
    icon VARCHAR(10),
    threshold INT DEFAULT 1,
    reward_amount DECIMAL(10, 2) DEFAULT 0,
    type ENUM('orders_count', 'total_spent', 'referrals_count') DEFAULT 'orders_count',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_type (type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_achievements table
CREATE TABLE IF NOT EXISTS user_achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    achievement_id BIGINT UNSIGNED NOT NULL,
    earned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_achievement (user_id, achievement_id),
    INDEX idx_user_id (user_id),
    INDEX idx_achievement_id (achievement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add columns to users table if they don't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS referral_code VARCHAR(255) UNIQUE NULL AFTER email,
ADD COLUMN IF NOT EXISTS referred_by BIGINT UNSIGNED NULL AFTER referral_code,
ADD COLUMN IF NOT EXISTS referral_balance DECIMAL(10, 2) DEFAULT 0 AFTER referred_by,
ADD FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL;

-- Create indices for better performance
ALTER TABLE users 
ADD INDEX IF NOT EXISTS idx_referral_code (referral_code),
ADD INDEX IF NOT EXISTS idx_referred_by (referred_by),
ADD INDEX IF NOT EXISTS idx_referral_balance (referral_balance);

-- Insert achievements data
INSERT INTO achievements (name, slug, description, icon, threshold, reward_amount, type, is_active) VALUES
    ('Pemula', 'pemula', 'Lakukan pembelian pertama Anda', '🎯', 1, 5000, 'orders_count', TRUE),
    ('Pelanggan Setia', 'pelanggan-setia', 'Lakukan 10 pembelian', '⭐', 10, 25000, 'orders_count', TRUE),
    ('Penggemar Kopi', 'penggemar-kopi', 'Total pembelian mencapai 100 ribu', '☕', 100000, 50000, 'total_spent', TRUE),
    ('Duta Kafe', 'duta-kafe', 'Ajak 5 teman untuk berbelanja', '👥', 5, 100000, 'referrals_count', TRUE),
    ('Atlet Espresso', 'atlet-espresso', 'Lakukan 50 pembelian', '🏆', 50, 150000, 'orders_count', TRUE)
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Optional: Show the tables created
SHOW TABLES LIKE 'referrals';
SHOW TABLES LIKE 'achievements';
SHOW TABLES LIKE 'user_achievements';

-- Verify users table has new columns
SHOW COLUMNS FROM users LIKE 'referral%';
SHOW COLUMNS FROM users LIKE 'referred_by';
