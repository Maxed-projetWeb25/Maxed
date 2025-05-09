<?php
try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS feed");
    $pdo->exec("USE feed");

    // Create post table with indexes
    $pdo->exec("CREATE TABLE IF NOT EXISTS post (
        postid INT AUTO_INCREMENT PRIMARY KEY,
        userid INT NOT NULL,
        description TEXT,
        media LONGBLOB,
        posttype VARCHAR(50) NOT NULL,
        visibility VARCHAR(20) NOT NULL,
        date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_userid (userid),
        INDEX idx_posttype (posttype),
        INDEX idx_visibility (visibility),
        INDEX idx_date_posted (date_posted)
    )");

    // Create react table for likes with indexes
    $pdo->exec("CREATE TABLE IF NOT EXISTS react (
        postid INT,
        userid INT,
        type VARCHAR(20) NOT NULL,
        reacttime TIMESTAMP NULL,
        PRIMARY KEY (postid, userid),
        INDEX idx_reacttime (reacttime),
        INDEX idx_type (type)
    )");

    // Create repost table with indexes
    $pdo->exec("CREATE TABLE IF NOT EXISTS repost (
        postid INT,
        userid INT,
        reposttime TIMESTAMP NULL,
        PRIMARY KEY (postid, userid),
        INDEX idx_reposttime (reposttime)
    )");

    echo "Database and tables created successfully with optimized indexes!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 