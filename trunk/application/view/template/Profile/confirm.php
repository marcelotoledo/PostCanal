<?php if ($this->accepted == true) : ?>

<p><?php echo $this->message ?></p>
<p><?php B_Helper::a($this->translation()->main_page, null) ?></p>

<?php else : ?>

<p><?php echo $this->translation()->invalid_profile ?></p>

<?php endif ?>
