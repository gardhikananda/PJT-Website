<?php

header("Content-Type: application/json; charset=UTF-8");


// ========================================
// Only allow POST request
// ========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


// ========================================
// Get form data
// ========================================

$name = trim($_POST["name"] ?? "");

$company = trim($_POST["company"] ?? "");

$phone = trim($_POST["phone"] ?? "");

$email = trim($_POST["email"] ?? "");

$service = trim($_POST["service"] ?? "");

$message = trim($_POST["message"] ?? "");


// ========================================
// Validate required fields
// ========================================

if (
    empty($name) ||
    empty($company) ||
    empty($phone) ||
    empty($email) ||
    empty($service) ||
    empty($message)
) {

    echo json_encode([
        "success" => false,
        "message" => "Please complete all required fields."
    ]);

    exit;
}


// ========================================
// Validate email
// ========================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


// ========================================
// Sanitize data
// ========================================

$name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");

$company = htmlspecialchars($company, ENT_QUOTES, "UTF-8");

$phone = htmlspecialchars($phone, ENT_QUOTES, "UTF-8");

$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");

$service = htmlspecialchars($service, ENT_QUOTES, "UTF-8");

$message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");


// ========================================
// Company email
// ========================================

$to = "info@prabujayatunggal.com";


// ========================================
// Email subject
// ========================================

$subject = "New Consultation Request - Prabu Jaya Tunggal";


// ========================================
// Email body
// ========================================

$emailBody = "

NEW CONSULTATION REQUEST
========================

Name:
$name

Company / Organization:
$company

Phone / WhatsApp:
$phone

Email:
$email

Type of Service:
$service

Message:
$message

========================
This message was sent from
the Prabu Jaya Tunggal website.
";


// ========================================
// Email headers
// ========================================

$headers = "From: Prabu Jaya Tunggal Website <info@prabujayatunggal.com>\r\n";

$headers .= "Reply-To: $email\r\n";

$headers .= "MIME-Version: 1.0\r\n";

$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


// ========================================
// Send email
// ========================================

if (mail($to, $subject, $emailBody, $headers)) {

    echo json_encode([
        "success" => true,
        "message" => "Thank you. Your consultation request has been sent successfully. Our team will contact you shortly."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to send your request. Please try again later."
    ]);

}

?>