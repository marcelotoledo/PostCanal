<?php $confirm_url = AB_Request::url("profile", "confirm", array("email" => $profile->login_email, "uid" => $profile->uid)) ?>

<?php echo $this->translation->mail_regiter_new_body ?>

<p><a href="<?php echo $confirm_url ?>" target="_blank"><?php echo $confirm_url ?></a></p>
