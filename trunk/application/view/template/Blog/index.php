<div class="subpage">

<h1><?php echo $this->translation()->blogs ?></h1>

<div id="blogaddlnkdiv">
    <?php B_Helper::a($this->translation()->blog_add, 'blog', 'add') ?>
</div>

<div id="bloglistarea">
    <?php foreach($this->blogs as $b): ?>
    <div class="blogitem" blog="<?php echo $b->hash ?>">
        <?php echo $b->name ?><br/>
        <small><?php echo $b->url ?></small>
    </div>
    <?php endforeach ?>
</div>

</div>
