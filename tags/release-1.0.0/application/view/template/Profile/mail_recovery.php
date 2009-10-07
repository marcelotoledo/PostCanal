<?php 

$password_url = B_Request::url("profile", "password", array("email" => $profile->login_email, "user" => $profile->hash)); 

?>

<p>Forgot your password for PostCanal.com?</p>

<p>If you don't remember your password you can reset using the link below:</p>

<p><a href="<?php echo $password_url ?>" target="_blank"><?php echo $password_url ?></a></p>

<p>If you received this email by mistake, do not worry, someone mistyped while trying to register in our website, just ignore it. No changes will be made to your account.</p>

<p>This is an automated message, please do not reply. If you need to contact us, please use <a href="mailto:help@postcanal.com">help@postcanal.com</a></p>
