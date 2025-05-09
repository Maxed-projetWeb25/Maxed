<?php
class NotificationLayout {
    private $db;

    public function __construct() {
        $this->db = Config::getInstance()->getConnection();
    }

    public function checkUpcomingEvents() {
        try {
            // Debug: Get current time
            $currentTime = date('H:i:s');
            $currentDate = date('Y-m-d');
            error_log("Current Time: " . $currentTime);
            error_log("Current Date: " . $currentDate);

            // Modified query to handle time properly
            $query = "SELECT e.*, n.status as notification_status,
                            TIME_FORMAT(e.temp_event, '%H:%i:%s') as formatted_time,
                            TIMESTAMPDIFF(MINUTE, 
                                CURRENT_TIMESTAMP, 
                                CONCAT(e.date_event, ' ', TIME_FORMAT(e.temp_event, '%H:%i:%s'))
                            ) as minutes_until_event
                     FROM event e
                     LEFT JOIN notification n ON e.id_event = n.event_id
                     WHERE e.date_event = CURRENT_DATE
                     AND e.temp_event BETWEEN CURRENT_TIME 
                     AND ADDTIME(CURRENT_TIME, '01:00:00')
                     ORDER BY e.temp_event ASC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: Log found events
            foreach ($events as $event) {
                error_log("Found event: " . json_encode($event));
            }

            return $events;
        } catch (PDOException $e) {
            error_log("Error checking upcoming events: " . $e->getMessage());
            return [];
        }
    }

    public function render() {
        $upcomingEvents = $this->checkUpcomingEvents();
        
        if (empty($upcomingEvents)) {
            error_log("No upcoming events found");
            return '<div style="display:none;">No upcoming events</div>';
        }

        $html = '<div class="notification-container" style="
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 350px;
            z-index: 1000;
        ">';

        foreach ($upcomingEvents as $event) {
            $minutesUntil = $event['minutes_until_event'];
            
            $html .= '<div class="notification-card" style="
                background-color: #f8f9fa;
                border-left: 4px solid #007bff;
                border-radius: 4px;
                padding: 15px;
                margin-bottom: 10px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                animation: slideIn 0.3s ease-out;
            ">
                <h4 style="margin: 0 0 10px 0; color: #343a40;">' . htmlspecialchars($event['title_event']) . '</h4>
                <p style="margin: 0 0 5px 0; color: #6c757d;">Starting in ' . $minutesUntil . ' minutes</p>
                <p style="margin: 0; color: #6c757d;"><small>Location: ' . htmlspecialchars($event['loc_event']) . '</small></p>
                <p style="margin: 0; color: #6c757d;"><small>Time: ' . htmlspecialchars($event['formatted_time']) . '</small></p>
                <button onclick="dismissNotification(this, ' . $event['id_event'] . ')" style="
                    float: right;
                    background: none;
                    border: none;
                    color: #6c757d;
                    cursor: pointer;
                ">×</button>
            </div>';
        }

        $html .= '</div>';
        
        // Add CSS animation
        $html .= '<style>
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>';

        // Add JavaScript for dismissing notifications
        $html .= '<script>
            function dismissNotification(button, eventId) {
                const card = button.parentElement;
                card.style.animation = "slideOut 0.3s ease-out forwards";
                setTimeout(() => card.remove(), 300);
                
                fetch("dismiss_notification.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "event_id=" + eventId
                });
            }
        </script>';

        return $html;
    }
}
?> 