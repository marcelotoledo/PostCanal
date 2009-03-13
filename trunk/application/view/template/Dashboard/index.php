<?php if(count($this->cms) == 0) : ?>

<div class="nocms">Nenhum CMS cadastrado. <?php B_Helper::a("Adicionar novo CMS", "cms", "add") ?></span> 

<?php else : ?>

<div id="lcol">
<div class="ctit">
<?php B_Helper::a("adicionar", "cms", "add") ?>
</div>
<div class="cbox" id="mlcb" style="height:500px">

<?php foreach($this->cms as $cms) : ?>
<div class="cmsitm" cid="<?php echo $cms->cid ?>"><a><?php echo $cms->name ?></a></div>
<?php endforeach ?>

</div></div>

<div id="rcol">
<div class="ctit">Super CMS</div>
<div class="cbox" id="mrcb" style="height:500px">

<table id="rbox"><tbody><tr><td id="rbcA">

<div id="rbdA">
&nbsp;
</div>

</td><td id="rbcB">

<div id="rbdB">
&nbsp;
</div>

</td></tr>
<tr><td colspan="2">

<div id="rbdC">
&nbsp;
</div>

</td></tr></tbody></table>

</div></div>

<div id="bbox" style="display:none">&nbsp;</div>

<?php endif ?>
