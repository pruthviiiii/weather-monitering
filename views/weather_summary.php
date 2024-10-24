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
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Daily Weather Summary</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>City</th>
                    <th>Average Temperature (&deg;C)</th>
                    <th>Maximum Temperature (&deg;C)</th>
                    <th>Minimum Temperature (&deg;C)</th>
                    <th>Dominant Weather Condition</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Assuming $dailySummaries is an array of daily weather data
                $dailySummaries = getDailyWeatherSummaries(); // Function to retrieve daily summaries from DB
                
                if (empty($dailySummaries)) {
                    echo "<tr><td colspan='6' class='text-center'>No weather data available</td></tr>";
                } else {
                    foreach ($dailySummaries as $summary) {
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($summary['summary_date'])) ?></td>
                            <td><?= $summary['city_name'] ?></td> <!-- Display city name -->
                            <td><?= $summary['avg_temp'] ?> &deg;C</td>
                            <td><?= $summary['max_temp'] ?> &deg;C</td>
                            <td><?= $summary['min_temp'] ?> &deg;C</td>
                            <td><?= $summary['dominant_condition'] ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../partials/footer.php'); ?>

<?php
// Function to get daily summaries from the database
function getDailyWeatherSummaries() {
    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=weather_monitoring', 'root', ''); // Adjust credentials as needed

    // Query to get daily summaries with city name
    $query = $pdo->query("SELECT summary_date, city_name, avg_temp, max_temp, min_temp, dominant_condition FROM daily_weather_summary ORDER BY summary_date DESC");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>
</body>
</html>
