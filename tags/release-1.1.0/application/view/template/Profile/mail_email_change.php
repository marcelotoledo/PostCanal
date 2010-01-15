<?php 

$email_url = B_Request::url("profile", "email", array("email" => $profile->login_email, "user" => $profile->hash));

?>

<p>Would you like to change your email for PostCanal.com?</p>

<p>If you do, just use the link below:</p>

<p><a href="<?php echo $email_url ?>" target="_blank"><?php echo $email_url ?></a></p>
