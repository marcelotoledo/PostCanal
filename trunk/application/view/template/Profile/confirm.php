<?php if ($this->accepted == true) : ?>

<p><?php echo $this->message ?></p>
<p><?php AB_Helper::a($this->translation->application_main_page, null) ?></p>

<?php else : ?>

<p><?php echo $this->translation->confirm_invalid_profile ?></p>

<?php endif ?>
