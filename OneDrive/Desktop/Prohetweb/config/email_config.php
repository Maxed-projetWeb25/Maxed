<?php
// Email Configuration

// Default values (change these to your actual Gmail credentials)
$defaultConfig = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    'smtp_username' => 'your.email@gmail.com', // Replace with your Gmail
    'smtp_password' => 'your-app-password',    // Replace with your Gmail App Password
    'from_email' => 'your.email@gmail.com',    // Replace with your Gmail
    'from_name' => 'Event System'
];

// Check for environment-specific configuration file
$envConfigFile = __DIR__ . '/email_config_local.php';
if (file_exists($envConfigFile)) {
    $envConfig = require $envConfigFile;
    return array_merge($defaultConfig, $envConfig);
}

return $defaultConfig;
?> 