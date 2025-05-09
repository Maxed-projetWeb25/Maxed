<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Check if database exists
    $result = $db->query("SELECT DATABASE()");
    $dbname = $result->fetchColumn();
    echo "Current database: " . $dbname . "\n";
    
    // Check tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "\nExisting tables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
        
        // Show table structure
        $columns = $db->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        echo "  Columns:\n";
        foreach ($columns as $column) {
            echo "    {$column['Field']} - {$column['Type']}\n";
        }
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "Error checking database: " . $e->getMessage() . "\n";
}
?> 