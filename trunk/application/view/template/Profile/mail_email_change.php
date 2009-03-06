<?php $email_url = AB_Request::url("profile", "email", array("email" => $profile->login_email, "uid" => $profile->uid)) ?>

<?php echo $this->translation->mail_email_change_body ?>

<p><a href="<?php echo $email_url ?>" target="_blank"><?php echo $email_url ?></a></p>
