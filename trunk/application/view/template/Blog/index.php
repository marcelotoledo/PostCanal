<div class="subcontainer">

<h1><?php echo $this->translation()->blogs ?></h1>

<div id="blogaddlnkdiv">
    <?php B_Helper::a($this->translation()->blog_add, 'blog', 'add') ?>
</div>

<div id="bloglistarea">
    <?php foreach($this->blogs_ as $b): ?>
    <div class="blogitem" blog="<?php echo $b->hash ?>">
        <div class="blogitemleft">
        <?php echo $b->name ?><br/>
        <small><?php echo $b->blog_url ?></small>
        </div>
        <div class="blogitemright">
        <a class="blogdeletelnk" blog="<?php echo $b->hash ?>"><?php echo $this->translation()->delete ?></a>
        </div>
        <div style="clear:both"></div>
    </div>
    <?php endforeach ?>
</div>

</div>
