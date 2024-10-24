<?php
require_once 'config.php';

// List of cities in India for monitoring
$cities = ['Delhi', 'Mumbai', 'Chennai', 'Bangalore', 'Kolkata', 'Hyderabad'];

// Fetch weather data for each city and store it
foreach ($cities as $city) {
    $url = BASE_URL . "?q=$city&appid=" . API_KEY;
    $response = file_get_contents($url);
    
    if ($response !== false) {
        $data = json_decode($response, true);

        // Convert temperatures from Kelvin to Celsius
        $temp = $data['main']['temp'] - 273.15;
        $feels_like = $data['main']['feels_like'] - 273.15;
        $weather_condition = $data['weather'][0]['main'];
        $timestamp = date('Y-m-d H:i:s', $data['dt']);  // Convert Unix timestamp to human-readable format

        // Store data in the database
        $pdo = db_connect();
        $stmt = $pdo->prepare("
            INSERT INTO weather_data (city, temperature, feels_like, weather_condition, created_at)
            VALUES (:city, :temperature, :feels_like, :weather_condition, :created_at)
        ");
        $stmt->execute([
            ':city' => $city,
            ':temperature' => $temp,
            ':feels_like' => $feels_like,
            ':weather_condition' => $weather_condition,
            ':created_at' => $timestamp,
        ]);

        // Check for alert thresholds
        if ($temp > TEMP_THRESHOLD) {
            echo "Alert: Temperature in $city exceeded " . TEMP_THRESHOLD . "°C at $timestamp.\n";
        }
    } else {
        echo "Failed to retrieve data for $city.\n";
    }
}
?>
