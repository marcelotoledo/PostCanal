<?php if(count($this->blogs) > 0) : ?>

<div id="feedarea">
<div id="feedareahead" class="containerhead">
    <span class="title"><?php echo $this->translation()->feeds ?></span>
    <span class="feeddisplay">
    <b><?php echo $this->translation()->feeds_display ?>: </b>
    <span class="feedsdspall" style="display:none">
        <?php echo $this->translation()->feeds_display_all ?> | 
        <a id="feeddsplnkthr"><?php echo $this->translation()->feeds_display_threaded ?></a>
    </span>
    <span class="feedsdspthr" style="display:none">
        <a id="feeddsplnkall"><?php echo $this->translation()->feeds_display_all ?></a> | 
        <?php echo $this->translation()->feeds_display_threaded ?>
    </span>
    </span>
    <span class="articledisplay">
    <b><?php echo $this->translation()->articles_display ?>: </b>
    <span class="articledsplst" style="display:none">
        <?php echo $this->translation()->articles_display_list ?> | 
        <a id="articledsplnkexp"><?php echo $this->translation()->articles_display_expanded ?></a>
    </span>
    <span class="articledspexp" style="display:none">
        <a id="articledsplnklst"><?php echo $this->translation()->articles_display_list?></a> | 
        <?php echo $this->translation()->articles_display_expanded ?>
    </span>
    </span>
</div>
<div id="feedlistarea"></div>
</div>

<div id="bottombar">
<div id="bottomleftbar"><nobr>

<table><tr>
<td><span><b><?php echo $this->translation()->mode ?>: </b>
<select name="queuemode">
    <option value="M"><?php echo $this->translation()->queue_mode_manual ?></option>
    <option value="A"><?php echo $this->translation()->queue_mode_automatic ?></option>
</select>
</span></td>
<td><span class="qctl">&nbsp;</span></td>
<th>&nbsp;</th>
<td><span class="queued"><?php echo $this->translation()->queued ?>: <i>0</i></span></td>
<td><span class="nextpub"><?php echo $this->translation()->next_publication ?>: <i>never</i></span></td>
</tr></table>

</nobr></div>

<div id="bottomrightbar"><nobr>
<span class="hctl hctl-open">&nbsp;</span>
</nobr></div>
</div>

<div id="queuelistbar" style="display:none">
queue
</div>

<?php else : ?>

<div id="noblogmsg" class="b-dialog" style="display:none">
<?php echo $this->translation()->no_registered_blog ?>. <?php B_Helper::a(ucfirst($this->translation()->application_click_here), "blog", "add") ?> <?php echo $this->translation()->new_blog_instruction ?>.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation()->application_close ?></a>
</div>
</div>

<?php endif ?>
