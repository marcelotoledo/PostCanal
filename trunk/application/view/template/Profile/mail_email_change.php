<?php $email_url = B_Request::url("profile", "email", array("email" => $profile->login_email, "user" => $profile->hash)) ?>

<?php echo $this->translation->mail_email_change_body ?>

<p><a href="<?php echo $email_url ?>" target="_blank"><?php echo $email_url ?></a></p>
