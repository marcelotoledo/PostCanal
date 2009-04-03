<?php $confirm_url = B_Request::url("profile", "confirm", array("email" => $profile->login_email, "user" => $profile->hash)) ?>

<?php echo $this->translation()->mail_regiter_new_body ?>

<p><a href="<?php echo $confirm_url ?>" target="_blank"><?php echo $confirm_url ?></a></p>
