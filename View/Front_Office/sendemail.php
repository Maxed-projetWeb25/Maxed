<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $to = "bibodoubleb@example.com"; // 🔁 Replace with your email
    $from = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message']);
    $subject = "New Feedback on Chapter";

    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "Thank you for your feedback!";
    } else {
        echo "There was an error sending your message.";
    }
} else {
    echo "Invalid request.";
}
?>
