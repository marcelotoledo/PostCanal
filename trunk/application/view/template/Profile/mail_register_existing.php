<?php $password_url = B_Request::url("profile", "password", array("email" => $profile->login_email, "user" => $profile->hash)) ?>

<p><?php echo $this->translation()->mail_register_existing_body_1 ?></p>

<p><a href="<?php echo BASE_URL ?>" target="_blank"><?php echo BASE_URL ?></a></p>

<p><?php echo $this->translation()->mail_register_existing_body_2 ?></p>

<p><a href="<?php echo $password_url ?>" target="_blank"><?php echo $password_url ?></a></p>
