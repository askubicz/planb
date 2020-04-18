<?php
exit(); // do nothing
$to      = 'askubicz@svsu.edu';
$subject = 'new task assigned';
$message = 'click this link to confirm';
$headers = 'From: akubicz@svsu.edu' . "\r\n" .
    'Reply-To: akubicz@svsu.edu' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>