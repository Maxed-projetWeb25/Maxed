<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/TicketModel.php';

$eventModel = new EventModel();
$ticketModel = new TicketModel();

$events = $eventModel->getAllEvents();
$tickets = $ticketModel->getAllTickets();

// Set page title
$pageTitle = 'Event List';

// Start output buffering
ob_start();
?>

<!-- Theme CSS -->
<link href="assets/sass/main.css" rel="stylesheet" />
<link href="assets/sass/dark-theme.css" rel="stylesheet" />
<link href="assets/sass/blue-theme.css" rel="stylesheet" />
<link href="assets/sass/bordered-theme.css" rel="stylesheet" />
<link href="assets/sass/semi-dark.css" rel="stylesheet" />
<link href="assets/sass/responsive.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="assets/plugins/datatable/css/buttons.bootstrap5.min.css" rel="stylesheet" />

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Theme Switcher -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Theme Settings</h5>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="themeCustomizeBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="material-icons-outlined">palette</i> Customize
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="themeCustomizeBtn">
                    <li>
                        <button class="dropdown-item theme-option" data-theme="default">
                            <i class="material-icons-outlined">brightness_medium</i> Default Theme
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item theme-option" data-theme="dark">
                            <i class="material-icons-outlined">dark_mode</i> Dark Theme
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item theme-option" data-theme="blue">
                            <i class="material-icons-outlined">water</i> Blue Theme
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item theme-option" data-theme="bordered">
                            <i class="material-icons-outlined">border_style</i> Bordered Theme
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item theme-option" data-theme="semi-dark">
                            <i class="material-icons-outlined">contrast</i> Semi Dark Theme
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Events List</h5>
            <a href="add-event.php" class="btn btn-primary">
                <i class="material-icons-outlined">add</i> Add Event
            </a>
        </div>
        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo $event->getIdEvent(); ?></td>
                                <td><?php echo $event->getTitleEvent(); ?></td>
                                <td><?php echo $event->getDescEvent(); ?></td>
                                <td><?php echo $event->getTempEvent(); ?></td>
                                <td><?php echo $event->getDateEvent(); ?></td>
                                <td><?php echo $event->getLocEvent(); ?></td>
                                <td><?php echo $event->getCapEvent(); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit_event.php?id=<?php echo $event->getIdEvent(); ?>" class="btn btn-sm btn-warning">
                                        <i class="material-icons-outlined">edit</i>
                                    </a>
                                    <a href="delete_event.php?id=<?php echo $event->getIdEvent(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this event?');">
                                        <i class="material-icons-outlined">delete</i>
                                    </a>
                                    <button class="btn btn-sm btn-info send-email-btn" data-event-id="<?php echo $event->getIdEvent(); ?>">
                                        <i class="material-icons-outlined">email</i>
                                    </button>
                                </div>
                            </td>
                            </tr>
                        <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No events found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Tickets List</h5>
            <a href="addTicket.php" class="btn btn-primary">
                <i class="material-icons-outlined">add</i> Add Ticket
            </a>
            <button id="regenerateQR" class="btn btn-secondary">
                <i class="material-icons-outlined">refresh</i> Regenerate QR Codes
            </button>
        </div>
                  <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>QR Code</th>
                                  <th>Event ID</th>
                                  <th>User ID</th>
                                  <th>Type</th>
                                  <th>Price</th>
                                  <th>Status</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                    <?php if (!empty($tickets)): ?>
                                  <?php foreach ($tickets as $ticket): ?>
                                      <tr>
                                <td><?php echo $ticket['id']; ?></td>
                                <td>
                                    <div class="qr-code-container" id="qr-container-<?php echo $ticket['id']; ?>">
                                        <?php if (!empty($ticket['qr_code'])): ?>
                                            <img src="<?php echo $ticket['qr_code']; ?>" 
                                                 alt="QR Code" 
                                                 class="img-fluid" 
                                                 style="max-width: 100px;">
                                            <button class="btn btn-sm btn-info download-qr" 
                                                    onclick="downloadQR(<?php echo $ticket['id']; ?>)">
                                                <i class="material-icons-outlined">download</i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">No QR Code</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo $ticket['event_id']; ?></td>
                                <td><?php echo $ticket['user_id']; ?></td>
                                <td><?php echo $ticket['type']; ?></td>
                                <td>$<?php echo number_format($ticket['price'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $ticket['status'] === 'Available' ? 'success' : 
                                            ($ticket['status'] === 'Reserved' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo $ticket['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="material-icons-outlined">edit</i>
                                        </a>
                                        <a href="delete_ticket.php?id=<?php echo $ticket['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this ticket?');">
                                            <i class="material-icons-outlined">delete</i>
                                        </a>
                                    </div>
                                          </td>
                                      </tr>
                                  <?php endforeach; ?>
                    <?php else: ?>
                                  <tr>
                            <td colspan="8" class="text-center">No tickets found</td>
                                  </tr>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
            </div>
  </div>

<!-- jQuery and DataTables JS -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
<script src="assets/plugins/datatable/js/jszip.min.js"></script>
<script src="assets/plugins/datatable/js/pdfmake.min.js"></script>
<script src="assets/plugins/datatable/js/vfs_fonts.js"></script>
<script src="assets/plugins/datatable/js/buttons.html5.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.print.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.colVis.min.js"></script>

<!-- Theme Switcher JS -->
<script src="assets/js/theme-switcher.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables with theme-aware styling
        var eventTable = $('#example').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-primary',
                    text: '<i class="material-icons-outlined">content_copy</i> Copy'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success',
                    text: '<i class="material-icons-outlined">table_chart</i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger',
                    text: '<i class="material-icons-outlined">picture_as_pdf</i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info',
                    text: '<i class="material-icons-outlined">print</i> Print'
                }
            ],
            responsive: true,
            language: {
                search: "Search events:",
                lengthMenu: "Show _MENU_ events per page",
                info: "Showing _START_ to _END_ of _TOTAL_ events"
            },
            searchDelay: 350,
            search: {
                return: true,
                smart: true
            },
            processing: true
        });

        // Initialize Tickets DataTable
        var ticketTable = $('#example2').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-primary',
                    text: '<i class="material-icons-outlined">content_copy</i> Copy'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success',
                    text: '<i class="material-icons-outlined">table_chart</i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger',
                    text: '<i class="material-icons-outlined">picture_as_pdf</i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info',
                    text: '<i class="material-icons-outlined">print</i> Print'
                }
            ],
            responsive: true,
            language: {
                search: "Search tickets:",
                lengthMenu: "Show _MENU_ tickets per page",
                info: "Showing _START_ to _END_ of _TOTAL_ tickets"
            },
            searchDelay: 350,
            search: {
                return: true,
                smart: true
            },
            processing: true
        });

        // Add event listeners for dynamic search on both tables
        $('#example_filter input').on('keyup', function() {
            eventTable.search(this.value).draw();
        });

        $('#example2_filter input').on('keyup', function() {
            ticketTable.search(this.value).draw();
        });

        // Theme change handler for DataTables
        document.addEventListener('themeChanged', function(e) {
            eventTable.draw();
            ticketTable.draw();
        });

        // Theme switching functionality
        $('.btn-check').on('change', function() {
            const themeId = $(this).attr('id');
            let themeName = '';
            
            switch(themeId) {
                case 'BlueTheme':
                    themeName = 'blue';
                    break;
                case 'LightTheme':
                    themeName = 'default';
                    break;
                case 'DarkTheme':
                    themeName = 'dark';
                    break;
                case 'SemiDarkTheme':
                    themeName = 'semi-dark';
                    break;
                case 'BorderedTheme':
                    themeName = 'bordered';
                    break;
            }
            
            if (themeName) {
                switchTheme(themeName);
            }
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('preferred-theme') || 'blue';
        $(`#${savedTheme.charAt(0).toUpperCase() + savedTheme.slice(1)}Theme`).prop('checked', true);
        switchTheme(savedTheme);

        // QR Code regeneration
        $('#regenerateQR').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            btn.html('<i class="material-icons-outlined">hourglass_empty</i> Regenerating...').prop('disabled', true);
            
            $.ajax({
                url: 'regenerate_qr.php',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error regenerating QR codes');
                    }
                },
                error: function() {
                    alert('Error regenerating QR codes');
                },
                complete: function() {
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });
    });

    function downloadQR(ticketId) {
        const qrContainer = document.getElementById('qr-container-' + ticketId);
        const qrImage = qrContainer.querySelector('img');
        
        if (qrImage) {
            // Create a temporary link element
            const link = document.createElement('a');
            link.download = 'ticket-' + ticketId + '-qr.png';
            link.href = qrImage.src;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('No QR code available for this ticket');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Add click event listeners to all send-email buttons
        document.querySelectorAll('.send-email-btn').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                
                // Show confirmation dialog
                Swal.fire({
                    title: 'Send Email Reminders?',
                    text: 'This will send email reminders to all ticket holders for this event.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, send reminders',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to send reminders
                        fetch('/projet web/Controller/send_reminders.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'event_id=' + encodeURIComponent(eventId)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to send reminders. Please try again.',
                                icon: 'error'
                            });
                            console.error('Error:', error);
                        });
                    }
                });
            });
        });
    });
</script>

<!-- Add the customizer button and panel at the bottom of the page, before closing main-wrapper -->
<!--start switcher-->
<button class="btn btn-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
</button>

<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
        <div class="">
            <h5 class="mb-0">Theme Customizer</h5>
            <p class="mb-0">Customize your theme</p>
        </div>
        <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
            <i class="material-icons-outlined">close</i>
        </a>
    </div>
    <div class="offcanvas-body">
        <div>
            <p>Theme variation</p>
            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
                    <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
                        <span class="material-icons-outlined">water</span>
                        <span>Blue</span>
                    </label>
                </div>
                <div class="col-12 col-xl-6">
                    <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
                    <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
                        <span class="material-icons-outlined">light_mode</span>
                        <span>Light</span>
                    </label>
                </div>
                <div class="col-12 col-xl-6">
                    <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
                    <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
                        <span class="material-icons-outlined">dark_mode</span>
                        <span>Dark</span>
                    </label>
                </div>
                <div class="col-12 col-xl-6">
                    <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
                    <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
                        <span class="material-icons-outlined">contrast</span>
                        <span>Semi Dark</span>
                    </label>
                </div>
                <div class="col-12 col-xl-6">
                    <input type="radio" class="btn-check" name="theme-options" id="BorderedTheme">
                    <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BorderedTheme">
                        <span class="material-icons-outlined">border_style</span>
                        <span>Bordered</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end switcher-->

<?php
$content = ob_get_clean();
require_once 'event-template.php';
?>