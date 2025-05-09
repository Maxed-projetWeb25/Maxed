<!-- view/Back_Office/editTicket.php -->
<?php
require_once __DIR__ . '/../../Model/TicketModel.php';
require_once __DIR__ . '/../../Model/EventModel.php';

$ticketModel = new TicketModel();
$ticket = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $ticket = $ticketModel->getTicketById($id);
}

// If no ticket is found, show error message
if ($ticket === null) {
    echo "<div class='alert alert-danger'>Ticket not found!</div>";
    exit;
}

// Get all events for the dropdown
$eventModel = new EventModel();
$events = $eventModel->getAllEvents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0 !important;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d3e2;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .form-label {
            font-weight: 600;
            color: #5a5c69;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
            border-radius: 10px 0 0 10px;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #5a5c69;
        }

        .form-section {
            background-color: #fff;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #5a5c69;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e3e6f0;
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: var(--danger-color) !important;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        .shake {
            animation: shake 0.3s;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="page-header">
            <h1><i class="fas fa-ticket-alt me-2"></i>Edit Ticket</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-4">
                <form id="ticketForm" method="POST" action="index.php?controller=ticket&action=editTicket&id=<?= htmlspecialchars($ticket['id']) ?>" class="needs-validation" novalidate>
                    <input type="hidden" id="ticket_id" name="ticket_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
                    
                    <div class="form-section">
                        <h2 class="form-section-title">Event Information</h2>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="event_id" class="form-label">Event</label>
                                <select class="form-select" id="event_id" name="event_id" required>
                                    <option value="">Select Event</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?= htmlspecialchars($event->getIdEvent()) ?>" 
                                                <?= ($ticket['event_id'] == $event->getIdEvent()) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($event->getTitleEvent()) ?> - 
                                            <?= htmlspecialchars($event->getDateEvent()) ?> 
                                            (<?= htmlspecialchars($event->getLocEvent()) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="user_id" class="form-label">User ID</label>
                                <input type="text" class="form-control" id="user_id" name="user_id" required
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       value="<?= htmlspecialchars($ticket['user_id'] ?? '') ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2 class="form-section-title">Ticket Details</h2>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label">Ticket Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="VIP" <?= ($ticket['type'] ?? '') === 'VIP' ? 'selected' : '' ?>>VIP</option>
                                    <option value="Regular" <?= ($ticket['type'] ?? '') === 'Regular' ? 'selected' : '' ?>>Regular</option>
                                    <option value="Premium" <?= ($ticket['type'] ?? '') === 'Premium' ? 'selected' : '' ?>>Premium</option>
                                    <option value="Student" <?= ($ticket['type'] ?? '') === 'Student' ? 'selected' : '' ?>>Student</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="price" name="price" required
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46"
                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                                           value="<?= htmlspecialchars($ticket['price'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="valid" <?= ($ticket['status'] ?? '') === 'valid' ? 'selected' : '' ?>>Valid</option>
                                <option value="cancelled" <?= ($ticket['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                <option value="used" <?= ($ticket['status'] ?? '') === 'used' ? 'selected' : '' ?>>Used</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="index.php?controller=ticket&action=listTickets" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Form Validation Script -->
    <script>
    $(document).ready(function() {
        // Function to show error message
        function showError(input, message) {
            const $input = $(input);
            $input.addClass('is-invalid shake');
            $input.next('.invalid-feedback').text(message);
        }

        // Function to clear error message
        function clearError(input) {
            const $input = $(input);
            $input.removeClass('is-invalid shake');
            $input.next('.invalid-feedback').text('');
        }

        // Validate Event ID - Only numbers allowed
        $('#event_id').on('input', function() {
            const value = $(this).val().trim();
            if (value === '') {
                showError(this, 'Event ID is required');
            } else if (!/^\d+$/.test(value)) {
                showError(this, 'Event ID must contain only numbers');
            } else {
                clearError(this);
            }
        });

        // Validate User ID - Only numbers allowed
        $('#user_id').on('input', function() {
            const value = $(this).val().trim();
            if (value === '') {
                showError(this, 'User ID is required');
            } else if (!/^\d+$/.test(value)) {
                showError(this, 'User ID must contain only numbers');
            } else {
                clearError(this);
            }
        });

        // Validate Ticket Type - Only 3 options allowed
        $('#type').on('change', function() {
            const value = $(this).val();
            if (value === '') {
                showError(this, 'Please select a ticket type');
            } else if (!['VIP', 'Regular', 'Student'].includes(value)) {
                showError(this, 'Please select a valid ticket type');
            } else {
                clearError(this);
            }
        });

        // Validate Price
        $('#price').on('input', function() {
            let value = $(this).val().trim();
            // Remove any non-numeric characters except decimal point
            value = value.replace(/[^0-9.]/g, '');
            // Ensure only one decimal point
            if ((value.match(/\./g) || []).length > 1) {
                value = value.substring(0, value.lastIndexOf('.'));
            }
            // Limit to 2 decimal places
            if (value.indexOf('.') !== -1) {
                value = value.substring(0, value.indexOf('.') + 3);
            }
            $(this).val(value);

            if (value === '') {
                showError(this, 'Price is required');
            } else if (!/^\d+(\.\d{1,2})?$/.test(value)) {
                showError(this, 'Price must be a valid number with up to 2 decimal places');
            } else {
                const price = parseFloat(value);
                if (price < 0) {
                    showError(this, 'Price cannot be negative');
                } else if (price > 10000) {
                    showError(this, 'Price cannot exceed $10,000');
                } else {
                    clearError(this);
                }
            }
        });

        // Validate Status
        $('#status').on('change', function() {
            const value = $(this).val();
            if (value === '') {
                showError(this, 'Please select a status');
            } else {
                clearError(this);
            }
        });

        // Form submission validation
        $('#ticketForm').on('submit', function(e) {
            let isValid = true;

            // Trigger validation for all fields
            $('#event_id, #user_id, #type, #price, #status').trigger('input change');

            // Check if any field has error
            if ($('.is-invalid').length > 0) {
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                // Show error message at the top
                if (!$('.alert-danger').length) {
                    $('<div class="alert alert-danger mt-3">Please correct the errors in the form before submitting.</div>')
                        .insertBefore('#ticketForm');
                }
            }
        });

        // Load existing ticket data
        function loadTicketData() {
            const ticketId = $('#ticket_id').val();
            if (ticketId) {
                $.ajax({
                    url: 'index.php?controller=ticket&action=getTicket',
                    method: 'GET',
                    data: { id: ticketId },
                    success: function(response) {
                        if (response.success) {
                            const ticket = response.data;
                            $('#event_id').val(ticket.event_id);
                            $('#user_id').val(ticket.user_id);
                            $('#type').val(ticket.type);
                            $('#price').val(ticket.price);
                            $('#status').val(ticket.status);
                        }
                    },
                    error: function() {
                        alert('Error loading ticket data');
                    }
                });
            }
        }

        // Load ticket data when page loads
        loadTicketData();
    });
    </script>
</body>
</html>
