<?php if ($this->accepted == true) : ?>

<p><?php echo $this->message ?></p>
<p><a href="/"><?php echo $this->translation()->main_page ?></a></p>

<?php else : ?>

<p><?php echo $this->translation()->invalid_profile ?></p>

<?php endif ?>
