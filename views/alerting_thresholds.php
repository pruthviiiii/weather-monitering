<?php
// DB connection
$pdo = new PDO('mysql:host=localhost;dbname=weather_monitoring', 'root', '');

// Fetch the latest thresholds
$stmt = $pdo->query("SELECT * FROM weather_alert_thresholds ORDER BY created_at DESC");
$thresholds = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Array to hold alert messages
$alertMessages = [];

// Loop through each threshold and check for alerts
foreach ($thresholds as $threshold) {
    $tempThreshold = $threshold['temp_threshold'];
    $weatherCondition = $threshold['weather_condition'];

    // Fetch the latest two weather updates for the specific city
    $stmt = $pdo->prepare("
        SELECT city_name, temp, main_condition, created_at
        FROM weather_data
        WHERE city_name = :city_name
        ORDER BY created_at DESC
        LIMIT 2
    ");

    // Fetch updates for each city
    $cities = ['Delhi', 'Mumbai', 'Chennai', 'Bangalore', 'Kolkata', 'Hyderabad'];
    
    foreach ($cities as $city) {
        $stmt->execute([':city_name' => $city]);
        $weatherUpdates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if the temperature exceeds the threshold for 2 consecutive updates
        if (count($weatherUpdates) === 2) {
            $latestTemp = $weatherUpdates[0]['temp'];
            $previousTemp = $weatherUpdates[1]['temp'];
            $latestCondition = $weatherUpdates[0]['main_condition'];

            if (
                $latestTemp > $tempThreshold &&
                $previousTemp > $tempThreshold &&
                ($weatherCondition === '' || $latestCondition === $weatherCondition)
            ) {
                $alertMessages[] = "Temperature exceeded {$tempThreshold}°C in {$city} for two consecutive updates!";
            }
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerting Thresholds</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Weather Alert Thresholds</h1>

    <form method="POST" action="save_thresholds.php">
        <div class="mb-3">
            <label for="tempThreshold" class="form-label">Temperature Threshold (°C)</label>
            <input type="number" class="form-control" id="tempThreshold" name="tempThreshold" placeholder="Enter max temperature threshold" required>
        </div>
        <div class="mb-3">
            <label for="weatherCondition" class="form-label">Alert for Specific Weather Condition</label>
            <select class="form-select" id="weatherCondition" name="weatherCondition">
                <option value="">None</option>
                <option value="Rain">Rain</option>
                <option value="Clear">Clear</option>
                <option value="Clouds">Clouds</option>
                <option value="Smoke">Smoke</option>
                <option value="Haze">Haze</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Thresholds</button>
    </form>

    <h2 class="mt-5">Triggered Alerts</h2>
    <?php 
        // Display alert messages
        if (!empty($alertMessages)) {
            foreach ($alertMessages as $message) {
                echo '<div class="alert alert-warning" role="alert">' . $message . '</div>';
            }
        } else {
            echo '<div class="alert alert-success" role="alert">No alerts triggered.</div>';
        }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
