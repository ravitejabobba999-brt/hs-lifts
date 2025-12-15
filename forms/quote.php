<?php
/**
 * Quote Form Handler - Configure your email settings below
 * 
 * Replace the $to, $from, and mail configuration as needed
 */

// Allow CORS from same origin or local testing tools
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

// Get the JSON input (preferred) or fall back to form-encoded POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data && !empty($_POST)) {
    // Convert $_POST to a simple array
    $data = [];
    foreach ($_POST as $k => $v) {
        $data[$k] = $v;
    }
}

// Validate input
if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input data.'
    ]);
    exit;
}

// Extract and sanitize data
$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$type = isset($data['type']) ? trim($data['type']) : '';
$timeline = isset($data['timeline']) ? trim($data['timeline']) : '';
$budget = isset($data['budget']) ? trim($data['budget']) : 'Not specified';
$message = isset($data['message']) ? trim($data['message']) : '';

// Validate required fields
if (empty($name) || empty($email) || empty($phone) || empty($type) || empty($timeline) || empty($message)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'All required fields must be filled.'
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address.'
    ]);
    exit;
}

// ============================================
// CONFIGURE YOUR EMAIL SETTINGS BELOW
// ============================================

// Set this to your email address where quotes should be sent
$to = 'your-email@example.com';  // CHANGE THIS TO YOUR EMAIL

// Set the from address (you can use the customer's email or your domain)
$from = 'noreply@hslifts.com';  // CHANGE THIS TO YOUR DOMAIN EMAIL

// Email subject
$subject = 'New Quote Request from ' . $name;

// Build the email body
$body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: #baff00; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #222; }
        .value { color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Quote Request - HS LIFTS</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <span class='label'>Name:</span>
                <div class='value'>" . htmlspecialchars($name) . "</div>
            </div>
            <div class='field'>
                <span class='label'>Email:</span>
                <div class='value'>" . htmlspecialchars($email) . "</div>
            </div>
            <div class='field'>
                <span class='label'>Phone:</span>
                <div class='value'>" . htmlspecialchars($phone) . "</div>
            </div>
            <div class='field'>
                <span class='label'>Project Type:</span>
                <div class='value'>" . htmlspecialchars($type) . "</div>
            </div>
            <div class='field'>
                <span class='label'>Timeline:</span>
                <div class='value'>" . htmlspecialchars($timeline) . "</div>
            </div>
            <div class='field'>
                <span class='label'>Budget:</span>
                <div class='value'>" . htmlspecialchars($budget) . "</div>
            </div>
            <div class='field'>
                <span class='label'>Project Description:</span>
                <div class='value' style='white-space: pre-wrap;'>" . htmlspecialchars($message) . "</div>
            </div>
        </div>
    </div>
</body>
</html>
";

// Set email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . $from . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// Try to send the email
if (@mail($to, $subject, $body, $headers)) {
    // Email sent successfully
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Quote request received. We will contact you within 24 hours.'
    ]);
} else {
    // Email failed - still return success to avoid exposing server errors
    // In production, log this error for debugging
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Quote request received. We will contact you within 24 hours.'
    ]);
}
?>
