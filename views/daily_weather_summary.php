<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'weather_monitoring');
define('DB_USER', 'root'); // Replace with your MySQL username
define('DB_PASS', ''); // Replace with your MySQL password

// Create a database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// File to store the last run date
$lastRunFile = 'last_run.txt';

// Get today's date
$today = date('Y-m-d');



// Get today's date range
$startDate = date('Y-m-d 00:00:00'); // Start of today
$endDate = date('Y-m-d H:i:s'); // Current date and time

// Query to fetch today's weather data for all cities
$sql = "SELECT city_name, 
               AVG(temp) as avg_temp, 
               MAX(temp) as max_temp, 
               MIN(temp) as min_temp, 
               main_condition 
        FROM weather_data 
        WHERE dt BETWEEN '$startDate' AND '$endDate' 
        GROUP BY city_name, main_condition";

$result = $conn->query($sql);

$insertMessages = []; // Array to hold messages for summary insertions

if ($result->num_rows > 0) {
    // Prepare summary data for each city
    while ($row = $result->fetch_assoc()) {
        $cityName = $row['city_name'];
        $avgTemp = $row['avg_temp'];
        $maxTemp = $row['max_temp'];
        $minTemp = $row['min_temp'];
        $dominantCondition = $row['main_condition'];
        $summaryDate = date('Y-m-d'); // Today's date for the summary

        // Insert summary into daily_weather_summary table
        $insertSql = "INSERT INTO daily_weather_summary (summary_date, avg_temp, max_temp, min_temp, dominant_condition, city_name, created_at) 
                      VALUES ('$summaryDate', '$avgTemp', '$maxTemp', '$minTemp', '$dominantCondition', '$cityName', NOW())";
        
        // Execute the insert query
        if ($conn->query($insertSql) === TRUE) {
            $insertMessages[] = "Summary inserted successfully for $cityName.";
        } else {
            $insertMessages[] = "Error inserting summary for $cityName: " . $conn->error;
        }
    }
} else {
    $insertMessages[] = "No data found for today.";
}

// Output all messages after processing
foreach ($insertMessages as $message) {
    echo $message . "<br>";
}

// Close the database connection
$conn->close();
?>
