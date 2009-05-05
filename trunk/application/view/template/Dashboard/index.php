<?php if(count($this->blogs) > 0) : ?>

<div id="middlecontent">
feeds
</div>

<div id="bottombar">
<div id="bottomleftbar"><nobr>

<table><tr>
<td><span class="title"><?php echo $this->translation()->queue ?>:</span></td>
<td><span class="mode"><?php echo $this->translation()->mode ?>:&nbsp;
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
