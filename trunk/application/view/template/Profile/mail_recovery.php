<?php $password_url = B_Request::url("profile", "password", array("email" => $profile->login_email, "user" => $profile->hash)) ?>

<?php echo $this->translation->mail_recovery_body ?>

<p><a href="<?php echo $password_url ?>" target="_blank"><?php echo $password_url ?></a></p>