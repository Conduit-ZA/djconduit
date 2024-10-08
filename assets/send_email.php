<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA validation
    $recaptcha_secret = 'YOUR_SECRET_KEY';
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $responseKeys = json_decode($response, true);
    
    if (intval($responseKeys["success"]) !== 1) {
        echo 'reCAPTCHA validation failed. Please try again.';
        exit;
    }

    // Sanitize inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Set up SendGrid
    require 'vendor/autoload.php'; // Make sure to include SendGrid PHP Library
    $sendgrid = new \SendGrid('YOUR_SENDGRID_API_KEY');
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("goodbums@outlook.com", "DJ Conduit Contact Form");
    $email->setSubject("New Contact Form Submission");
    $email->addTo("goodbums@outlook.com", "DJ Conduit");
    $email->addContent("text/plain", "Name: $name\nEmail: $email\nMessage: $message");

    try {
        $response = $sendgrid->send($email);
        if ($response->statusCode() == 202) {
            echo 'Message sent successfully!';
        } else {
            echo 'Failed to send message. Please try again later.';
        }
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage();
    }
}
?>
