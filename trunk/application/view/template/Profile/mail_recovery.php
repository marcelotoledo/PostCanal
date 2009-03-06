<?php $password_url = AB_Request::url("profile", "password", array("email" => $profile->login_email, "uid" => $profile->uid)) ?>

<?php echo $this->translation->mail_recovery_body ?>

<p><a href="<?php echo $password_url ?>" target="_blank"><?php echo $password_url ?></a></p>
