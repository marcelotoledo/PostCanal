<?php if(count($this->blogs) > 0) : ?>

<div id="feedarea">
<div id="feedareahead" class="containerhead">
    <div class="title"><?php echo $this->translation()->feeds ?></div>

    <div class="feeddisplay">
    <b><?php echo $this->translation()->feeds_display ?>: </b>
    <span class="feedsdspall" style="display:none">
        <?php echo $this->translation()->feeds_display_all ?> | 
        <a id="feeddsplnkthr"><?php echo $this->translation()->feeds_display_threaded ?></a>
    </span>
    <span class="feedsdspthr" style="display:none">
        <a id="feeddsplnkall"><?php echo $this->translation()->feeds_display_all ?></a> | 
        <?php echo $this->translation()->feeds_display_threaded ?>
    </span>
    </div>

    <div class="articledisplay">
    <b><?php echo $this->translation()->articles_display ?>: </b>
    <span class="articledsplst" style="display:none">
        <?php echo $this->translation()->articles_display_list ?> | 
        <a id="articledsplnkexp"><?php echo $this->translation()->articles_display_expanded ?></a>
    </span>
    <span class="articledspexp" style="display:none">
        <a id="articledsplnklst"><?php echo $this->translation()->articles_display_list?></a> | 
        <?php echo $this->translation()->articles_display_expanded ?>
    </span>
    </div>

    <div class="feednavigation" style="display:none">
        <a id="articlepreviouslnk">&lt; <?php echo $this->translation()->previous ?></a> | 
        <a id="articlenextlnk"><?php echo $this->translation()->next ?> &gt;</a>
    </div>

    <div class="feedrefresh">
        <a id="feedrefreshlnk"><?php echo $this->translation()->refresh ?></a>
    </div>

    <div style="clear:both"></div>
</div>

<div id="feedlistarea"></div>
</div>

<?php else : ?>

<p class="noblogmsg"><?php echo $this->translation()->no_registered_blog ?></p>

<div id="noblogmsg" class="b-dialog" style="display:none">
<?php echo $this->translation()->no_registered_blog ?>. <?php B_Helper::a(ucfirst($this->translation()->application_click_here), "blog", "add") ?> <?php echo $this->translation()->new_blog_instruction ?>.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation()->application_close ?></a>
</div>
</div>

<?php endif ?>
