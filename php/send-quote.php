<?php

// =========================================================
// GET QUOTE FORM HANDLER
// =========================================================

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
$company = trim($_POST["company"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$service = trim($_POST["service"] ?? "");
$sample = trim($_POST["sample"] ?? "");
$sample_quantity = trim($_POST["sample_quantity"] ?? "");
$parameters = trim($_POST["parameters"] ?? "");
$message = trim($_POST["message"] ?? "");


// =========================================================
// REQUIRED FIELD VALIDATION
// =========================================================

if (
    empty($name) ||
    empty($company) ||
    empty($email) ||
    empty($phone) ||
    empty($service)
) {

    echo json_encode([
        "status" => "error",
        "message" => "Please complete all required fields."
    ]);

    exit;
}


// =========================================================
// EMAIL VALIDATION
// =========================================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "status" => "error",
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


// =========================================================
// FILE SETTINGS
// =========================================================

$maxFileSize = 5 * 1024 * 1024; // 5 MB

$allowedExtensions = [
    "pdf",
    "doc",
    "docx",
    "xls",
    "xlsx",
    "jpg",
    "jpeg",
    "png"
];


// =========================================================
// CHECK ATTACHMENT
// =========================================================

$attachment = null;

if (
    isset($_FILES["attachment"]) &&
    $_FILES["attachment"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    // Check upload error
    if ($_FILES["attachment"]["error"] !== UPLOAD_ERR_OK) {

        echo json_encode([
            "status" => "error",
            "message" => "There was a problem uploading the attachment."
        ]);

        exit;
    }


    // Check file size
    if ($_FILES["attachment"]["size"] > $maxFileSize) {

        echo json_encode([
            "status" => "error",
            "message" => "The attachment must not exceed 5 MB."
        ]);

        exit;
    }


    // Get extension
    $fileName = $_FILES["attachment"]["name"];

    $fileExtension = strtolower(
        pathinfo($fileName, PATHINFO_EXTENSION)
    );


    // Check extension
    if (!in_array($fileExtension, $allowedExtensions, true)) {

        echo json_encode([
            "status" => "error",
            "message" => "This file type is not allowed."
        ]);

        exit;
    }


    // Store temporary file information
    $attachment = [
        "name" => $fileName,
        "tmp_name" => $_FILES["attachment"]["tmp_name"],
        "type" => $_FILES["attachment"]["type"],
        "size" => $_FILES["attachment"]["size"]
    ];
}


// =========================================================
// SANITIZE TEXT DATA
// =========================================================

$name = htmlspecialchars(
    $name,
    ENT_QUOTES,
    "UTF-8"
);

$company = htmlspecialchars(
    $company,
    ENT_QUOTES,
    "UTF-8"
);

$email = htmlspecialchars(
    $email,
    ENT_QUOTES,
    "UTF-8"
);

$phone = htmlspecialchars(
    $phone,
    ENT_QUOTES,
    "UTF-8"
);

$service = htmlspecialchars(
    $service,
    ENT_QUOTES,
    "UTF-8"
);

$sample = htmlspecialchars(
    $sample,
    ENT_QUOTES,
    "UTF-8"
);

$sample_quantity = htmlspecialchars(
    $sample_quantity,
    ENT_QUOTES,
    "UTF-8"
);

$parameters = htmlspecialchars(
    $parameters,
    ENT_QUOTES,
    "UTF-8"
);

$message = htmlspecialchars(
    $message,
    ENT_QUOTES,
    "UTF-8"
);


// =========================================================
// EMAIL SETTINGS
// =========================================================

$to = "info@prabujayatunggal.com";

$emailSubject = "New Quote Request - Prabu Jaya Tunggal";


// =========================================================
// EMAIL BODY
// =========================================================

$emailBody =
    "NEW QUOTE REQUEST\n" .
    "============================\n\n" .

    "CONTACT INFORMATION\n" .
    "Name: " . $name . "\n" .
    "Company: " . $company . "\n" .
    "Email: " . $email . "\n" .
    "Phone / WhatsApp: " . $phone . "\n\n" .

    "TESTING REQUIREMENT\n" .
    "Service: " . $service . "\n" .
    "Sample / Equipment: " . ($sample ?: "-") . "\n" .
    "Number of Samples: " . ($sample_quantity ?: "-") . "\n" .
    "Required Testing / Parameters: " . ($parameters ?: "-") . "\n\n" .

    "ADDITIONAL INFORMATION\n" .
    ($message ?: "-") . "\n\n" .

    "Attachment: " .
    ($attachment ? $attachment["name"] : "None") . "\n\n" .

    "============================\n" .
    "Prabu Jaya Tunggal Website";


// =========================================================
// EMAIL HEADERS
// =========================================================

$headers = [];

$headers[] =
    "From: Prabu Jaya Tunggal Website <info@prabujayatunggal.com>";

$headers[] =
    "Reply-To: " . $email;

$headers[] =
    "MIME-Version: 1.0";


// =========================================================
// WITHOUT ATTACHMENT
// =========================================================

if ($attachment === null) {

    $headers[] =
        "Content-Type: text/plain; charset=UTF-8";

    $mailSent = mail(
        $to,
        $emailSubject,
        $emailBody,
        implode("\r\n", $headers)
    );

}


// =========================================================
// WITH ATTACHMENT
// =========================================================

else {

    $boundary = md5(
        "boundary" . microtime(true)
    );


    $headers[] =
        "Content-Type: multipart/mixed; boundary=\"" .
        $boundary .
        "\"";


    // Start email body
    $emailMessage = "";

    $emailMessage .=
        "--" . $boundary . "\r\n";

    $emailMessage .=
        "Content-Type: text/plain; charset=UTF-8\r\n";

    $emailMessage .=
        "Content-Transfer-Encoding: 8bit\r\n\r\n";

    $emailMessage .=
        $emailBody . "\r\n\r\n";


    // Read attachment
    $fileContent = file_get_contents(
        $attachment["tmp_name"]
    );

    $encodedFile = chunk_split(
        base64_encode($fileContent)
    );


    // Attachment headers
    $emailMessage .=
        "--" . $boundary . "\r\n";

    $emailMessage .=
        "Content-Type: " .
        $attachment["type"] .
        "; name=\"" .
        basename($attachment["name"]) .
        "\"\r\n";

    $emailMessage .=
        "Content-Disposition: attachment; filename=\"" .
        basename($attachment["name"]) .
        "\"\r\n";

    $emailMessage .=
        "Content-Transfer-Encoding: base64\r\n\r\n";

    $emailMessage .=
        $encodedFile . "\r\n";


    // End boundary
    $emailMessage .=
        "--" . $boundary . "--\r\n";


    // Send email
    $mailSent = mail(
        $to,
        $emailSubject,
        $emailMessage,
        implode("\r\n", $headers)
    );

}


// =========================================================
// RESPONSE
// =========================================================

if ($mailSent) {

    echo json_encode([
        "status" => "success",
        "message" =>
            "Thank you. Your quote request has been sent successfully. Our team will review your requirements and contact you soon."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" =>
            "Sorry, we could not send your quote request. Please try again later."
    ]);

}

exit;

?>