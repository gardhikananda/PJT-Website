<?php

// =========================================================
// CONTACT FORM HANDLER
// =========================================================

// Return JSON response
header("Content-Type: application/json; charset=UTF-8");


// =========================================================
// ONLY ACCEPT POST REQUEST
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);

    exit;
}


// =========================================================
// GET FORM DATA
// =========================================================

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");


// =========================================================
// VALIDATION
// =========================================================

if (
    empty($name) ||
    empty($email) ||
    empty($mobile) ||
    empty($subject) ||
    empty($message)
) {

    echo json_encode([
        "status" => "error",
        "message" => "Please complete all required fields."
    ]);

    exit;
}


// =========================================================
// VALIDATE EMAIL
// =========================================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "status" => "error",
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


// =========================================================
// SANITIZE DATA
// =========================================================

$name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
$mobile = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
$subject = htmlspecialchars($subject, ENT_QUOTES, "UTF-8");
$message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");


// =========================================================
// EMAIL SETTINGS
// =========================================================

$to = "info@prabujayatunggal.com";

$emailSubject = "New Contact Inquiry - Prabu Jaya Tunggal";

$emailBody =
    "New contact inquiry received from the website.\n\n" .

    "Name: " . $name . "\n" .
    "Email: " . $email . "\n" .
    "Phone: " . $mobile . "\n" .
    "Subject: " . $subject . "\n\n" .

    "Message:\n" .
    $message . "\n\n" .

    "----------------------------------------\n" .
    "Prabu Jaya Tunggal Website\n";


// =========================================================
// EMAIL HEADERS
// =========================================================

$headers = [];

$headers[] = "From: Prabu Jaya Tunggal Website <info@prabujayatunggal.com>";
$headers[] = "Reply-To: " . $email;
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";


// =========================================================
// SEND EMAIL
// =========================================================

$mailSent = mail(
    $to,
    $emailSubject,
    $emailBody,
    implode("\r\n", $headers)
);


// =========================================================
// RESPONSE
// =========================================================

if ($mailSent) {

    echo json_encode([
        "status" => "success",
        "message" => "Thank you for contacting us. Your message has been sent successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Sorry, we could not send your message. Please try again later."
    ]);
}

exit;
?>