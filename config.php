<?php
// Configuration settings

// OpenWeatherMap API credentials
define('API_KEY', '955134103635536238f1ea0efda9e0ca');  // Replace with your actual API key
define('BASE_URL', 'https://api.openweathermap.org/data/2.5/weather');

// MySQL Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'weather_monitoring');
define('DB_USER', 'root');  // Replace with your MySQL username
define('DB_PASS', '');      // Replace with your MySQL password

// Function to connect to the database
function db_connect() {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

// Temperature threshold for alerting
define('TEMP_THRESHOLD', 35);  // Example threshold (35°C)
