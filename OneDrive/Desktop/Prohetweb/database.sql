-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS project;
USE project;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `notification`;
DROP TABLE IF EXISTS `ticket`;
DROP TABLE IF EXISTS `event`;
DROP TABLE IF EXISTS `user`;

-- Create the user table
CREATE TABLE `user` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the event table
CREATE TABLE `event` (
    `id_event` INT PRIMARY KEY AUTO_INCREMENT,
    `title_event` VARCHAR(255) NOT NULL,
    `desc_event` TEXT,
    `temp_event` VARCHAR(50),
    `date_event` DATE,
    `loc_event` VARCHAR(255),
    `cap_event` INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the ticket table
CREATE TABLE `ticket` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `status` ENUM('valid', 'cancelled', 'used') NOT NULL DEFAULT 'valid',
    FOREIGN KEY (`event_id`) REFERENCES `event`(`id_event`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the notification table
CREATE TABLE `notification` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `notification_time` DATETIME NOT NULL,
    `status` ENUM('pending', 'sent', 'read') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`event_id`) REFERENCES `event`(`id_event`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample events
INSERT INTO `event` (`title_event`, `desc_event`, `temp_event`, `date_event`, `loc_event`, `cap_event`) VALUES 
('Tech Startup Pitch', 'Annual tech startup pitch event', '25', '2024-04-15', 'Tech Hub Center', 200),
('Innovation Summit', 'Innovation and entrepreneurship summit', '28', '2024-05-20', 'Business District', 150),
('Student Pitch Competition', 'University student pitch competition', '22', '2024-06-10', 'University Hall', 100);

-- Insert sample users
INSERT INTO `user` (`name`, `email`, `password`) VALUES 
('Test User', 'test@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: test123
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample tickets
INSERT INTO `ticket` (`event_id`, `user_id`, `type`, `price`, `status`) VALUES 
(1, 1, 'VIP', 100.00, 'valid'),
(1, 2, 'Regular', 50.00, 'valid'),
(2, 1, 'Premium', 75.00, 'valid'),
(3, 3, 'Student', 25.00, 'valid'); 