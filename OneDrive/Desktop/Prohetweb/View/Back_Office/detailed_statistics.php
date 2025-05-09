<?php
require_once '../../Controller/EventC.php';
require_once '../../Controller/UserC.php';

$eventC = new EventC();
$userC = new UserC();

// Get statistics
$eventsByStatus = $eventC->getEventsByStatus();
$eventTrends = $eventC->getEventTrendsByMonth();
$usersByRole = $userC->getUsersByRole();
$userTrends = $userC->getUserRegistrationTrends();
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detailed Statistics</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="sass/main.css" rel="stylesheet">
</head>

<body>
    <main class="main-wrapper">
        <div class="main-content">
            <!-- Event Statistics -->
            <div class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Events by Status</h5>
                            <div id="eventStatusChart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Event Status Trends</h5>
                            <div id="eventTrendsChart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="row mt-4">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Users by Role</h5>
                            <div id="userRoleChart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Registration Trends</h5>
                            <div id="userTrendsChart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/plugins/apexcharts/apexcharts.min.js"></script>
    
    <script>
        // Event Status Pie Chart
        var eventStatusOptions = {
            series: <?php echo json_encode(array_values($eventsByStatus)); ?>,
            chart: {
                type: 'pie',
                height: 300
            },
            labels: <?php echo json_encode(array_keys($eventsByStatus)); ?>,
            colors: ['#4CAF50', '#2196F3', '#F44336']
        };
        new ApexCharts(document.querySelector("#eventStatusChart"), eventStatusOptions).render();

        // Event Trends Line Chart
        var eventTrendsOptions = {
            series: [{
                name: 'Upcoming',
                data: <?php echo json_encode(array_values($eventTrends['upcoming'])); ?>
            }, {
                name: 'Completed',
                data: <?php echo json_encode(array_values($eventTrends['completed'])); ?>
            }, {
                name: 'Cancelled',
                data: <?php echo json_encode(array_values($eventTrends['cancelled'])); ?>
            }],
            chart: {
                type: 'line',
                height: 300
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }
        };
        new ApexCharts(document.querySelector("#eventTrendsChart"), eventTrendsOptions).render();

        // User Role Donut Chart
        var userRoleOptions = {
            series: <?php echo json_encode(array_values($usersByRole)); ?>,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: <?php echo json_encode(array_keys($usersByRole)); ?>,
            colors: ['#673AB7', '#FF9800']
        };
        new ApexCharts(document.querySelector("#userRoleChart"), userRoleOptions).render();

        // User Registration Trends Area Chart
        var userTrendsOptions = {
            series: [{
                name: 'Admin',
                data: <?php echo json_encode(array_values($userTrends['admin'])); ?>
            }, {
                name: 'Regular Users',
                data: <?php echo json_encode(array_values($userTrends['user'])); ?>
            }],
            chart: {
                type: 'area',
                height: 300,
                stacked: true
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }
        };
        new ApexCharts(document.querySelector("#userTrendsChart"), userTrendsOptions).render();
    </script>
</body>
</html> 