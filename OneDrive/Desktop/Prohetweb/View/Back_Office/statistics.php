<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__ . '/../../Controller/EventC.php';
require_once __DIR__ . '/../../Controller/TicketController.php';

$eventC = new EventC();
$ticketC = new TicketController();

// Get statistics
$totalEvents = $eventC->getTotalEvents();
$totalTickets = $ticketC->getTotalTickets();

// Get detailed statistics
$capacityByLocation = $eventC->getCapacityByLocation();
$ticketsByStatus = $ticketC->getTicketsByStatus();
$ticketMonthlyTrends = $ticketC->getMonthlyStats();

// Set page title
$pageTitle = 'Statistics Dashboard';

// Start output buffering
ob_start();
?>

<!-- Required styles -->
<link href="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.5/css/perfect-scrollbar.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/metismenu/dist/metisMenu.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/simplebar@5.3.6/dist/simplebar.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">

<!--main css-->
<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<link href="assets/css/dark-theme.css" rel="stylesheet">
<link href="assets/css/semi-dark.css" rel="stylesheet">
<link href="assets/css/bordered-theme.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
  <div class="breadcrumb-title pe-3">Statistics</div>
  <div class="ps-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 p-0">
        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard Statistics</li>
      </ol>
    </nav>
  </div>
</div>
<!--end breadcrumb-->

<div class="row row-cols-1 row-cols-xl-2 row-cols-xxl-2">
  <div class="col">
    <div class="card radius-10">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <div class="">
            <h3 class="mb-0"><?php echo $totalEvents; ?></h3>
            <p class="mb-0">Total Events</p>
          </div>
          <div class="ms-auto fs-2 text-primary">
            <i class="material-icons-outlined">event</i>
          </div>
        </div>
        <div id="chart1" class=""></div>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card radius-10">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
          <div class="">
            <h3 class="mb-0"><?php echo $totalTickets; ?></h3>
            <p class="mb-0">Total Tickets</p>
          </div>
          <div class="ms-auto fs-2 text-success">
            <i class="material-icons-outlined">confirmation_number</i>
          </div>
        </div>
        <div id="chart2" class=""></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-xl-6 d-flex">
    <div class="card radius-10 w-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <h6 class="mb-0">Capacity by Location</h6>
          <div class="dropdown ms-auto">
            <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret" type="button" data-bs-toggle="dropdown">
              <i class="material-icons-outlined">more_vert</i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </div>
        </div>
        <div id="capacityLocationChart"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-6 d-flex">
    <div class="card radius-10 w-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <h6 class="mb-0">Tickets by Status</h6>
          <div class="dropdown ms-auto">
            <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret" type="button" data-bs-toggle="dropdown">
              <i class="material-icons-outlined">more_vert</i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </div>
        </div>
        <div id="ticketStatusChart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-xl-6 d-flex">
    <div class="card radius-10 w-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <h6 class="mb-0">Monthly Ticket Trends</h6>
          <div class="dropdown ms-auto">
            <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret" type="button" data-bs-toggle="dropdown">
              <i class="material-icons-outlined">more_vert</i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </div>
        </div>
        <div id="ticketTrendsChart"></div>
      </div>
    </div>
  </div>
</div>

<!-- Required scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.5/dist/perfect-scrollbar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/metismenu/dist/metisMenu.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simplebar@5.3.6/dist/simplebar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- Initialize Perfect Scrollbar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (sidebarWrapper) {
        new PerfectScrollbar(sidebarWrapper);
    }
});
</script>

<!-- Charts initialization script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart 1 - Total Events Trend
    var options1 = {
        series: [{
            name: 'Events',
            data: [25, 35, 45, 55, 65, 75, 85, 95, 100, 110, 120, 130]
        }],
        chart: {
            foreColor: '#9ba7b2',
            height: 180,
            type: 'area',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            },
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        colors: ['#3461ff'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#3461ff'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100, 100, 100]
            },
        },
        grid: {
            show: true,
            borderColor: '#ededed',
            strokeDashArray: 4,
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return val + " events"
                }
            }
        }
    };
    if (document.querySelector("#chart1")) {
        var chart1 = new ApexCharts(document.querySelector("#chart1"), options1);
        chart1.render();
    }

    // Chart 2 - Total Tickets Trend
    var options2 = {
        series: [{
            name: 'Tickets',
            data: [15, 25, 35, 45, 55, 65, 75, 85, 95, 100, 110, 120]
        }],
        chart: {
            foreColor: '#9ba7b2',
            height: 180,
            type: 'area',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            },
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        colors: ['#02c27a'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#02c27a'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100, 100, 100]
            },
        },
        grid: {
            show: true,
            borderColor: '#ededed',
            strokeDashArray: 4,
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return val + " tickets"
                }
            }
        }
    };
    if (document.querySelector("#chart2")) {
        var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
        chart2.render();
    }

    // Capacity Location Chart
    var capacityLocationOptions = {
        series: [{
            name: 'Capacity',
            data: <?php echo json_encode(array_values($capacityByLocation)); ?>
        }],
        chart: {
            foreColor: '#9ba7b2',
            height: 380,
            type: 'bar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: false,
                barHeight: '50%',
                dataLabels: {
                    position: 'bottom'
                }
            }
        },
        colors: ['#3461ff'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#fff']
            },
            formatter: function (val) {
                return val.toLocaleString() + ' seats';
            },
            offsetX: 0
        },
        xaxis: {
            categories: <?php echo json_encode(array_keys($capacityByLocation)); ?>,
            labels: {
                formatter: function (val) {
                    return Math.round(val).toLocaleString();
                }
            }
        },
        yaxis: {
            title: {
                text: 'Location'
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                title: {
                    formatter: function () {
                        return 'Total Capacity:';
                    }
                },
                formatter: function (val) {
                    return val.toLocaleString() + ' seats';
                }
            }
        }
    };
    if (document.querySelector("#capacityLocationChart")) {
        var capacityLocationChart = new ApexCharts(document.querySelector("#capacityLocationChart"), capacityLocationOptions);
        capacityLocationChart.render();
    }

    // Ticket Status Chart
    var ticketStatusOptions = {
        series: <?php echo json_encode(array_values($ticketsByStatus)); ?>,
        chart: {
            foreColor: '#9ba7b2',
            height: 380,
            type: 'donut',
        },
        colors: ['#02c27a', '#fc185a', '#3461ff'],
        labels: <?php echo json_encode(array_keys($ticketsByStatus)); ?>,
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 360
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    if (document.querySelector("#ticketStatusChart")) {
        var ticketStatusChart = new ApexCharts(document.querySelector("#ticketStatusChart"), ticketStatusOptions);
        ticketStatusChart.render();
    }

    // Monthly Ticket Trends
    var ticketTrendsOptions = {
        series: [{
            name: 'Tickets',
            data: <?php echo json_encode(array_values($ticketMonthlyTrends)); ?>
        }],
        chart: {
            foreColor: '#9ba7b2',
            height: 380,
            type: 'line',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#02c27a'],
        grid: {
            show: true,
            borderColor: '#ededed',
            strokeDashArray: 4,
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return val + " tickets"
                }
            }
        }
    };
    if (document.querySelector("#ticketTrendsChart")) {
        var ticketTrendsChart = new ApexCharts(document.querySelector("#ticketTrendsChart"), ticketTrendsOptions);
        ticketTrendsChart.render();
    }
});
</script>

<?php
$content = ob_get_clean();
require_once 'event-template.php';
?> 