<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Visualizations</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Daily Weather Summaries</h1>

    <div class="row">
        <div class="col-md-6">
            <h3>Average Temperature</h3>
            <canvas id="avgTempChart"></canvas>
        </div>
        <div class="col-md-6">
            <h3>Maximum and Minimum Temperatures</h3>
            <canvas id="maxMinTempChart"></canvas>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h3>Dominant Weather Conditions</h3>
            <canvas id="weatherConditionChart"></canvas>
        </div>
    </div>
</div>

<script>
// Fetch data from the server using PHP
let days = [];
let avgTemps = [];
let maxTemps = [];
let minTemps = [];
let weatherConditions = [];

<?php
// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=weather_monitoring', 'root', ''); // Adjust credentials as needed

// Fetch weather data
$query = $pdo->query("SELECT DISTINCT city_name, main_condition, temp, dt FROM weather_data");
$weatherData = $query->fetchAll(PDO::FETCH_ASSOC);

// Process the data for charts
foreach ($weatherData as $data) {
    echo "days.push('".date('h:i A', strtotime($data['dt']))."');"; // Assuming `dt` holds the date
    echo "avgTemps.push(".($data['temp']).");"; // Assuming `temp` is average for simplicity
    echo "maxTemps.push(".($data['temp'] + 5).");"; // Example: Adding 5 for max
    echo "minTemps.push(".($data['temp'] - 5).");"; // Example: Subtracting 5 for min
    echo "weatherConditions.push('".$data['main_condition']."');"; // Get the main condition
}
?>

const ctx1 = document.getElementById('avgTempChart').getContext('2d');
const avgTempChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: days,
        datasets: [{
            label: 'Average Temperature (°C)',
            data: avgTemps,
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
        }]
    }
});

const ctx2 = document.getElementById('maxMinTempChart').getContext('2d');
const maxMinTempChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: days,
        datasets: [
            {
                label: 'Max Temp (°C)',
                data: maxTemps,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
            },
            {
                label: 'Min Temp (°C)',
                data: minTemps,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
            }
        ]
    }
});

// Prepare data for the weather condition chart
const weatherConditionCounts = {};
weatherConditions.forEach(condition => {
    weatherConditionCounts[condition] = (weatherConditionCounts[condition] || 0) + 1;
});

const ctx3 = document.getElementById('weatherConditionChart').getContext('2d');
const weatherConditionChart = new Chart(ctx3, {
    type: 'pie',
    data: {
        labels: Object.keys(weatherConditionCounts),
        datasets: [{
            label: 'Dominant Weather Conditions',
            data: Object.values(weatherConditionCounts),
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
        }]
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
