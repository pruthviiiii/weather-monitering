<?php
// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=weather_monitoring', 'root', ''); // Adjust this to your DB credentials

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form input values
    $tempThreshold = isset($_POST['tempThreshold']) ? floatval($_POST['tempThreshold']) : null;
    $weatherCondition = isset($_POST['weatherCondition']) ? $_POST['weatherCondition'] : '';

    // Check if the temperature threshold is provided
    if ($tempThreshold !== null) {
        // Insert the threshold values into the `weather_alert_thresholds` table
        $stmt = $pdo->prepare("
            INSERT INTO weather_alert_thresholds (temp_threshold, weather_condition)
            VALUES (:temp_threshold, :weather_condition)
        ");
        $stmt->execute([
            ':temp_threshold' => $tempThreshold,
            ':weather_condition' => $weatherCondition
        ]);

        // Redirect back to the alert threshold page after saving
        header('Location: alerting_thresholds.php');
        exit();
    } else {
        // If no temperature threshold is provided, redirect back with an error message
        echo "Temperature threshold is required!";
    }
}
?>
