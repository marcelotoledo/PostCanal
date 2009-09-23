<?php
$confirm_url = B_Request::url("profile", "confirm", array("email" => $profile->login_email, "user" => $profile->hash));
$support_url = B_Request::url("support");
?>

<p>Welcome to PostCanal.com!</p>

<p>Your registration is almost complete, click the link below and we'll confirm your registration:

<p><a href="<?php echo $confirm_url ?>" target="_blank"><?php echo $confirm_url ?></a></p>

<p>If the link above does not work, copy and paste to your browser.</p>

<p>If you received this email by mistake, do not worry, someone mistyped while trying to register in our website, just ignore it and we'll clear you from our database.</p>

<p>This is an automated message, please do not reply. If you need to contact us, please use <a href="mailto:help@postcanal.com">help@postcanal.com</a></p>
