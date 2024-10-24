<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Monitoring System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Auto refresh every 5 minutes -->
    <script>
        setTimeout(function(){
            window.location.reload(1);
        }, 86400000); // 30 seconds in milliseconds (30000ms = 30 seconds)
    </script>
    <script>
        // Function to call the PHP script
        function runDailyWeatherSummary() {
            fetch('daily_weather_summary.php')
                .then(response => response.text())
                .then(data => {
                    // Display the response from the PHP script
                    document.getElementById('output').innerHTML += data + "<br>";
                })
                .catch(error => console.error('Error:', error));
        }

        // Run the function every 5 minutes (300000 milliseconds)
        //Run the function every day once 86400000
        setInterval(runDailyWeatherSummary, 86400000); // 5 minutes
    </script>

</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Weather Monitoring Dashboard</h1>

    <div class="row">
        <!-- Card for each city's weather data -->
        <?php
        // Define cities with their latitude and longitude
        $cities = [
            'Delhi' => ['lat' => 28.6139, 'lon' => 77.2090],
            'Mumbai' => ['lat' => 19.0760, 'lon' => 72.8777],
            'Chennai' => ['lat' => 13.0827, 'lon' => 80.2707],
            'Bangalore' => ['lat' => 12.9716, 'lon' => 77.5946],
            'Kolkata' => ['lat' => 22.5726, 'lon' => 88.3639],
            'Hyderabad' => ['lat' => 17.3850, 'lon' => 78.4867]
        ];

        foreach ($cities as $city => $coords) {
            $weather = getLatestWeatherDataForCity($coords['lat'], $coords['lon']); // Get latest weather data
            saveWeatherData($city, $weather); // Save data to weather_data table
            ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $city ?></h5>
                        <p class="card-text"><strong>Main Condition:</strong> <?= $weather['main'] ?></p>
                        <p class="card-text"><strong>Temperature:</strong> <?= $weather['temp'] ?> &deg;C</p>
                        <p class="card-text"><strong>Feels Like:</strong> <?= $weather['feels_like'] ?> &deg;C</p>
                        <p class="card-text"><strong>Last Updated:</strong> <?= date('d M Y, H:i', $weather['dt']) ?></p>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light text-center text-lg-start mt-auto">
  <div class="text-center p-3">
    &copy; <?= date('Y') ?> Weather Monitoring System. All rights reserved.
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Function to get the latest weather data for a city based on latitude and longitude
function getLatestWeatherDataForCity($lat, $lon) {
    $apiKey = '955134103635536238f1ea0efda9e0ca'; // Replace with your actual OpenWeatherMap API key
    $url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$apiKey&units=metric";

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors in the cURL request
    if (curl_errno($ch)) {
        return [
            'main' => 'Error fetching data',
            'temp' => null,
            'feels_like' => null,
            'dt' => time()
        ];
    }

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the API returned a successful response
    if (isset($data['main'])) {
        return [
            'main' => $data['weather'][0]['main'],   // Main weather condition (e.g., Rain, Clear)
            'temp' => $data['main']['temp'],          // Current temperature in Celsius
            'feels_like' => $data['main']['feels_like'], // Perceived temperature in Celsius
            'dt' => $data['dt']                        // Timestamp of the weather update
        ];
    } else {
        return [
            'main' => 'Error fetching data',
            'temp' => null,
            'feels_like' => null,
            'dt' => time()
        ];
    }
}

function saveWeatherData($city, $weather) {
    try {
        // Manually set up the PDO connection
        $pdo = new PDO('mysql:host=localhost;dbname=weather_monitoring', 'root', '');

        // Set PDO error mode to exception for better error handling
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("
            INSERT INTO weather_data (city_name, main_condition, temp, feels_like, dt, created_at)
            VALUES (:city_name, :main_condition, :temp, :feels_like, :dt, NOW())
        ");

        // Execute the statement with the given weather data
        $stmt->execute([
            ':city_name' => $city,
            ':main_condition' => $weather['main'],
            ':temp' => $weather['temp'],
            ':feels_like' => $weather['feels_like'],
            ':dt' => date('Y-m-d H:i:s', $weather['dt'])  // Convert timestamp to datetime format
        ]);

    } catch (PDOException $e) {
        // Handle potential errors
        echo "Error: " . $e->getMessage();
    }
}

?>
